<?php

namespace App\Actions\Schedule;

use Chengkangzai\ApuSchedule\ApuSchedule;

class GetGroupingAndModule
{
    public function execute($intake_code, $grouping = null): array
    {
        $groupings = ApuSchedule::getGroupings($intake_code);
        $modules = ApuSchedule::getMODID($intake_code, $grouping);

        return [$groupings, $modules];
    }
}
