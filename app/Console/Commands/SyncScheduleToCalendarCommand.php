<?php

namespace App\Console\Commands;

use App\Jobs\Schedule\MsScheduleToCalendarJob;
use App\Jobs\Schedule\SyncScheduleToCalendar;
use App\Models\ScheduleConfig;
use Illuminate\Console\Command;

class SyncScheduleToCalendarCommand extends Command
{
    protected $signature = 'calendar:sync';

    protected $description = 'run sync command to sync apu schedule to calendar';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): int
    {
        ScheduleConfig::query()
            ->with('user')
            ->whereHas('user.msOauth')
            ->subscribed()
            ->get()
            ->tap(function ($schedules) {
                $this->info('Starting sync schedule to calendar');
                $this->output->progressStart($schedules->count());
            })
            ->each(function ($config) {
                MsScheduleToCalendarJob::dispatch($config->user, $config, SyncScheduleToCalendar::CAUSED_BY['Console']);
                $this->output->progressAdvance();
            })
            ->tap(function () {
                $this->output->progressFinish();
                $this->info('Sync schedule to calendar finished');
            });

        return self::SUCCESS;
    }
}
