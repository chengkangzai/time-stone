<?php

namespace App\Jobs\Schedule;

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
use JetBrains\PhpStorm\ArrayShape;
use Log;
use Microsoft\Graph\Exception\GraphException;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;
use Notification;

class MsScheduleToCalendarJob extends SyncScheduleToCalendar
{
    private Graph $graph;

    /** @var Model\Event[] */
    public array $events;
    private string $timeZone;

    public function __construct(User $user, ScheduleConfig $config, string $causeBy)
    {
        parent::__construct($user, $config, $causeBy);
        $this->graph = MicrosoftGraphService::getGraphWithUser($user);
        $this->timeZone = TimeZoneService::$timeZoneMap['Singapore Standard Time'];
    }

    public function handle()
    {
        try {
            $this->events = $this->getEventFromCalendar();

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

    protected function getAttendees(array $attendeeAddresses): array
    {
        return collect($attendeeAddresses)
            ->map(fn($add) => ['emailAddress' => ['address' => $add], 'type' => 'required'])
            ->toArray();
    }

    /**
     * @throws GraphException
     * @throws GuzzleException
     */
    protected function syncCalendar($schedule): Model\Event
    {
        return $this->graph->createRequest('POST', '/me/events')
            ->attachBody($this->formatNewEvent($schedule))
            ->setReturnType(Model\Event::class)
            ->execute();
    }

    #[ArrayShape(['subject' => "", 'attendees' => "array", 'start' => "array", 'end' => "array", 'body' => "string[]"])]
    protected function formatNewEvent($schedule): array
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
    protected function getEventFromCalendar(): array
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

    protected function isEventCreatedBefore($schedule): bool
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
