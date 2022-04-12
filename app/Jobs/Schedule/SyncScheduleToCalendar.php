<?php

namespace App\Jobs\Schedule;

use App\Models\ScheduleConfig;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

abstract class SyncScheduleToCalendar implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected string $causeBy;
    protected User $user;
    protected ScheduleConfig $config;

    public const CAUSED_BY = [
        'Console' => 'Console',
        'Web' => 'Web',
    ];

    public function __construct(User $user, ScheduleConfig $config, string $causeBy)
    {
        $this->user = $user;
        $this->config = $config;
        $this->causeBy = $causeBy;
    }

    abstract public function handle();

    abstract protected function getAttendees(array $attendeeAddresses): array;

    abstract protected function syncCalendar($schedule);

    abstract protected function formatNewEvent($schedule);

    abstract protected function getEventFromCalendar(): array;

    abstract protected function isEventCreatedBefore($schedule): bool;

    protected function getEventBodyContent($schedule): string
    {
        return collect("Hi, {$this->user->name}, you have a class of $schedule->MODULE_NAME.")
            ->add("The class is from $schedule->TIME_FROM to $schedule->TIME_TO at $schedule->ROOM ")
            ->add("The lecturer is $schedule->NAME ($schedule->SAMACCOUNTNAME@staffemail.apu.edu.my)")
            ->when($this->causeBy === static::CAUSED_BY['Console'], function ($content) {
                $content->add("This is an automated message from " . config('app.name') . ".");
                $content->add("To unsubscribe, please click on the following link: " . URL::signedRoute('unsubscribe', ['email' => $this->user->email]));
            })
            ->implode("\n");
    }

    protected static function isSameTimeAndDay(Carbon $time1, Carbon $time2): bool
    {
        return $time1->day === $time2->day && $time1->hour === $time2->hour && $time1->minute === $time2->minute;
    }
}
