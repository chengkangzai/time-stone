<?php

namespace App\Jobs;

use App\Models\ScheduleConfig;
use App\Models\User;
use App\Notifications\CalendarSyncSuccessNotification;
use App\Services\MicrosoftGraphService;
use App\Services\TimeZoneService;
use Carbon\Carbon;
use Chengkangzai\ApuSchedule\ApuSchedule;
use DateTimeInterface;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;
use JetBrains\PhpStorm\ArrayShape;
use Log;
use Microsoft\Graph\Exception\GraphException;
use Microsoft\Graph\Graph as MicrosoftGraph;
use Microsoft\Graph\Model;
use Notification;

class AddAPUScheduleToCalenderJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public const CAUSED_BY = [
        'Console' => 'Console',
        'Web' => 'Web',
    ];

    private MicrosoftGraph $graph;
    private User $user;
    private ScheduleConfig $config;
    private string $causeBy;
    /** @var Model\Event[] */
    public array $events;
    private string $timeZone;

    public function __construct(User $user, ScheduleConfig $config, string $causeBy)
    {
        $this->graph = (new MicrosoftGraphService())->getGraph($user);
        $this->user = $user;
        $this->config = $config;
        $this->causeBy = $causeBy;
        $this->timeZone = TimeZoneService::$timeZoneMap['Singapore Standard Time'];
    }

    public function handle()
    {
        try {
            $this->events = $this->getEvent();

            $syncedSchedule = ApuSchedule::getSchedule($this->config->intake_code, $this->config->grouping, $this->config->except)
                ->map(function ($schedule) {
                    if (!$this->isEventCreatedBefore($schedule)) {
                        $this->syncCalendar($schedule);

                        return $schedule;
                    }

                    return null;
                })
                ->filter();

            Notification::send($this->user, new CalendarSyncSuccessNotification($this->config, $syncedSchedule));
        } catch (GuzzleException|GraphException|Exception $e) {
            Log::error($e->getMessage());
        }
    }

    private function getAttendees(array $attendeeAddresses): array
    {
        return collect($attendeeAddresses)
            ->map(fn($add) => ['emailAddress' => ['address' => $add], 'type' => 'required'])
            ->toArray();
    }

    /**
     * @throws GraphException
     * @throws GuzzleException
     */
    private function syncCalendar($schedule)
    {
        $this->graph->createRequest('POST', '/me/events')
            ->attachBody($this->formatNewEvent($schedule))
            ->setReturnType(Model\Event::class)
            ->execute();
    }

    #[ArrayShape(['subject' => "", 'attendees' => "array", 'start' => "array", 'end' => "array", 'body' => "string[]"])]
    private function formatNewEvent($schedule): array
    {
        return [
            'subject' => $schedule->MODID,
            'attendees' => $this->getAttendees(explode(';', $this->user->email)),
            'start' => [
                'dateTime' => $schedule->TIME_FROM_ISO,
                'timeZone' => $this->timeZone,
            ],
            'end' => [
                'dateTime' => $schedule->TIME_TO_ISO,
                'timeZone' => $this->timeZone,
            ],
            'body' => [
                'content' => $this->getEventBodyContent($schedule),
                'contentType' => 'text',
            ],
        ];
    }

    /**
     * @return Model\Event[]
     * @throws GuzzleException
     * @throws Exception
     *
     * @throws GraphException
     */
    private function getEvent(): array
    {
        $query = [
            'startDateTime' => Carbon::now()->subMonth()->format(DateTimeInterface::ISO8601),
            'endDateTime' => Carbon::now()->addMonth()->format(DateTimeInterface::ISO8601),
            '$select' => 'subject,organizer,start,end',
            '$orderby' => 'start/dateTime',
            '$top' => 50,
        ];

        $getEventsUrl = '/me/calendarView?' . http_build_query($query);

        return $this->graph->createRequest('GET', $getEventsUrl)
            ->addHeaders(['Prefer' => 'outlook.timezone="' . $this->timeZone . '"'])
            ->setReturnType(Model\Event::class)
            ->execute();
    }

    private function isEventCreatedBefore($schedule): bool
    {
        return collect($this->events)
            ->filter(function (Model\Event $event) use ($schedule) {
                $eventStart = Carbon::parse($event->getStart()->getDateTime());
                $eventEnd = Carbon::parse($event->getEnd()->getDateTime());

                $scheduleStart = Carbon::parse($schedule->TIME_FROM_ISO);
                $scheduleEnd = Carbon::parse($schedule->TIME_TO_ISO);

                return $this->isSameTimeAndDay($eventStart, $scheduleStart)
                    && $this->isSameTimeAndDay($eventEnd, $scheduleEnd);
            })
            ->isNotEmpty();
    }

    public function getEventBodyContent($schedule): string
    {
        return collect("Hi, {$this->user->name}, you have a class of $schedule->MODULE_NAME.")
            ->add("The class is from $schedule->TIME_FROM to $schedule->TIME_TO at $schedule->ROOM ")
            ->add("The lecturer is $schedule->NAME ($schedule->SAMACCOUNTNAME@staffemail.apu.edu.my)")
            ->when($this->causeBy === self::CAUSED_BY['Console'], function ($content) {
                $content->add("This is an automated message from " . config('app.name') . ".");
                $content->add("To unsubscribe, please click on the following link: " . URL::signedRoute('unsubscribe', ['email' => $this->user->email]));
            })
            ->implode("\n");
    }

    private function isSameTimeAndDay(Carbon $time1, Carbon $time2): bool
    {
        return $time1->day === $time2->day && $time1->hour === $time2->hour && $time1->minute === $time2->minute;
    }

    /**
     * Keep this, this is will make your life easier
     * @throws GuzzleException
     * @throws GraphException
     */
    private function removeEvent(Model\Event $event)
    {
        $this->graph->createRequest('DELETE', "/me/events/{$event->getId()}")
            ->setReturnType(Model\Event::class)
            ->execute();
    }
}
