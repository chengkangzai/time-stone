<?php

namespace App\Jobs;

use App\Services\MicrosoftGraphService;
use App\Services\TimeZoneService;
use App\Models\ScheduleConfig;
use App\Models\User;
use App\Notifications\CalendarSyncSuccessNotification;
use Carbon\Carbon;
use Chengkangzai\ApuSchedule\ApuSchedule;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
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

class AddAPUScheduleToCalenderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    const CAUSED_BY = [
        'Console' => 'Console',
        'Web' => 'Web',
    ];

    private MicrosoftGraph $graph;
    private User $user;
    private ScheduleConfig $config;
    private ?string $causeBy;

    public function __construct(User $user, ScheduleConfig $config, string|null $causeBy)
    {
        $graphService = new MicrosoftGraphService();
        $this->graph = $graphService->getGraph($user);
        $this->user = $user;
        $this->config = $config;
        $this->causeBy = $causeBy;
    }

    public function handle()
    {
        $attendeeAddresses = explode(';', $this->user->email);
        $attendees = $this->getAttendees($attendeeAddresses);
        $schedules = ApuSchedule::getSchedule($this->config->intake_code, $this->config->grouping, $this->config->except);

        try {
            $events = $this->getEvent();
            $syncedSchedule = collect();
            foreach ($schedules as $schedule) {
                $isEventCreatedBefore = false;
                foreach ($events as $event) {
                    $eventStart = Carbon::parse($event->getStart()->getDateTime())->format(DateTimeInterface::ISO8601);
                    $eventEnd = Carbon::parse($event->getEnd()->getDateTime())->format(DateTimeInterface::ISO8601);

                    $scheduleStart = Carbon::parse($schedule->TIME_FROM_ISO)->format(DateTimeInterface::ISO8601);
                    $scheduleEnd = Carbon::parse($schedule->TIME_TO_ISO)->format(DateTimeInterface::ISO8601);

                    if ($eventStart == $scheduleStart && $eventEnd == $scheduleEnd) {
                        $isEventCreatedBefore = true;
                        break;
                    }
                }
                if (!$isEventCreatedBefore) {
                    $newEvent = $this->formatNewEvent($schedule, $attendees);
//                    $this->syncCalendar($newEvent);
                    $syncedSchedule->add($schedule);
                }
            }

            Notification::send($this->user, new CalendarSyncSuccessNotification($this->config, $syncedSchedule));
        } catch (GuzzleException|GraphException|Exception $e) {
            Log::error($e->getMessage());
        }
    }

    private function getAttendees(array $attendeeAddresses): array
    {
        $attendees = [];
        foreach ($attendeeAddresses as $attendeeAddress) {
            $attendees[] = [
                'emailAddress' => [
                    'address' => $attendeeAddress
                ],
                'type' => 'required'
            ];
        }
        return $attendees;
    }

    /**
     * @throws GraphException
     * @throws GuzzleException
     */
    private function syncCalendar(array $newEvent)
    {
        $this->graph->createRequest('POST', '/me/events')
            ->attachBody($newEvent)
            ->setReturnType(Model\Event::class)
            ->execute();
    }

    #[ArrayShape(['subject' => "", 'attendees' => "array", 'start' => "array", 'end' => "array", 'body' => "string[]"])]
    private function formatNewEvent($schedule, array $attendees): array
    {
        return [
            'subject' => $schedule->MODID,
            'attendees' => $attendees,
            'start' => [
                'dateTime' => $schedule->TIME_FROM_ISO,
                'timeZone' => TimeZoneService::$timeZoneMap['Singapore Standard Time']
            ],
            'end' => [
                'dateTime' => $schedule->TIME_TO_ISO,
                'timeZone' => TimeZoneService::$timeZoneMap['Singapore Standard Time']
            ],
            'body' => [
                'content' =>
                    "Hi," . $this->user->name . ", you have a class of $schedule->MODULE_NAME with lecturer $schedule->NAME ($schedule->SAMACCOUNTNAME@staffemail.apu.edu.my)" .
                    " at $schedule->ROOM from $schedule->TIME_FROM to $schedule->TIME_TO \n" .
                    ($this->causeBy == self::CAUSED_BY['Console'] ?
                        "Sync Schedule will run every Saturday at 06:00 AM (GMT+8 Malaysia Timezone).\n" .
                        "To unsubscribe, please click on the link below: \n" .
                        URL::signedRoute('public.unsubscribe', ['email' => $this->user->email]) : ''
                    ),
                'contentType' => 'text'
            ]
        ];
    }

    /**
     * @throws GraphException
     * @throws GuzzleException
     * @throws Exception
     */
    private function getEvent(): array
    {
        $timezone = new DateTimeZone(TimeZoneService::$timeZoneMap["Singapore Standard Time"]);

        // Get start and end of week
        $startOfWeek = new DateTimeImmutable('sunday -4 week', $timezone);
        $endOfWeek = new DateTimeImmutable('sunday +4 week', $timezone);

        $queryParams = array(
            'startDateTime' => $startOfWeek->format(DateTimeInterface::ISO8601),
            'endDateTime' => $endOfWeek->format(DateTimeInterface::ISO8601),
            // Only request the properties used by the app
            '$select' => 'subject,organizer,start,end',
            // Sort them by start time
            '$orderby' => 'start/dateTime',
            // Limit results to 25
            '$top' => 50
        );

        // Append query parameters to the '/me/calendarView' url
        $getEventsUrl = '/me/calendarView?' . http_build_query($queryParams);

        return $this->graph->createRequest('GET', $getEventsUrl)
            // Add the user's timezone to the Prefer header
            ->addHeaders(array(
                'Prefer' => 'outlook.timezone="' . TimeZoneService::$timeZoneMap["Singapore Standard Time"] . '"'
            ))
            ->setReturnType(Model\Event::class)
            ->execute();
    }
}
