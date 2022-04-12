<?php

namespace App\Actions\Schedule;

use App\Models\ScheduleConfig;
use App\Models\User;
use Chengkangzai\ApuSchedule\ApuSchedule;
use Illuminate\Support\Collection;
use JetBrains\PhpStorm\ArrayShape;

class GetUserConfigAndEvents
{
    #[ArrayShape(['config' => ScheduleConfig::class, 'events' => Collection::class])]
    public function execute(User $user): array
    {
        $config = $user->scheduleConfig;
        $events = $config ? ApuSchedule::getSchedule($config->intake_code, $config->grouping, $config->except) : collect();

        return [$config, $events];
    }
}
