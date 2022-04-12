<?php

namespace App\Actions\Schedule;

use App\Models\ScheduleConfig;
use Validator;

class UpdateScheduleConfig
{
    public function execute(array $data, ScheduleConfig $scheduleConfig): bool
    {
        $data = $this->validate($data);

        return $scheduleConfig->update($data);
    }

    private function validate(array $data): array
    {
        $data = Validator::make($data, [
            'intake_code' => 'required|string',
            'grouping' => 'required|string',
            'is_subscribed' => 'sometimes|boolean',
            'except' => 'sometimes|array',
        ])
            ->validate();

        $data = collect($data);
        $data->getOrPut('except', null);
        $data->getOrPut('is_subscribed', false);

        return $data->toArray();
    }
}
