<?php

namespace App\Actions\Schedule;

use App\Models\ScheduleConfig;
use App\Models\User;
use Validator;

class StoreScheduleConfig
{
    public function execute(array $data, User $user): ScheduleConfig
    {
        $data = $this->validate($data);

        return $user->scheduleConfig()->create($data);
    }

    private function validate(array $data): array
    {
        return Validator::make($data, [
            'intake_code' => 'required|string',
            'grouping' => 'required|string',
            'is_subscribed' => 'sometimes|boolean',
        ])
            ->validate();
    }
}
