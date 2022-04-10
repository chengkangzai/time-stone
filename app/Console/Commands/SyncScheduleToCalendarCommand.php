<?php

namespace App\Console\Commands;

use App\Jobs\AddAPUScheduleToCalenderJob;
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
        $this->info('Starting sync schedule to calendar');
        ScheduleConfig::with('user')->whereHas('user.msOauth')->subscribed()->get()
            ->tap(function ($schedules) {
                $this->output->progressStart($schedules->count());
            })
            ->each(function ($config) {
                AddAPUScheduleToCalenderJob::dispatch($config->user, $config, AddAPUScheduleToCalenderJob::CAUSED_BY['Console']);
                $this->output->progressAdvance();
            })
            ->tap(function () {
                $this->output->progressFinish();
                $this->info('Sync schedule to calendar finished');
            });

        return self::SUCCESS;
    }
}
