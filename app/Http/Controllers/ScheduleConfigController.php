<?php

namespace App\Http\Controllers;

use App\Actions\Schedule\GetGroupingAndModule;
use App\Actions\Schedule\GetUserConfigAndEvents;
use App\Actions\Schedule\StoreScheduleConfig;
use App\Actions\Schedule\UpdateScheduleConfig;
use App\Jobs\Schedule\MsScheduleToCalendarJob;
use App\Models\ScheduleConfig;
use Auth;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ScheduleConfigController extends Controller
{
    public function index(GetUserConfigAndEvents $action): Factory|View|Application
    {
        [$config, $events] = $action->execute(Auth::user());

        return view('schedule.index', compact('config', 'events'));
    }

    public function store(Request $request, StoreScheduleConfig $action): RedirectResponse
    {
        $action->execute($request->all(), auth()->user());

        return redirect()->route('scheduleConfig.index')->with('success', __('Schedule config has been setup'));
    }

    public function edit(ScheduleConfig $scheduleConfig, GetGroupingAndModule $action): Factory|View|Application
    {
        [$groupings, $modules] = $action->execute($scheduleConfig->intake_code, $scheduleConfig->grouping);

        return view('schedule.edit', compact('scheduleConfig', 'groupings', 'modules'));
    }

    public function update(Request $request, ScheduleConfig $scheduleConfig, UpdateScheduleConfig $action): RedirectResponse
    {
        $action->execute($request->all(), $scheduleConfig);

        return redirect()->route('scheduleConfig.index')->with('success', __('Schedule config has been updated'));
    }

    public function syncNow(GetUserConfigAndEvents $get): RedirectResponse
    {
        if (auth()->user()->msOauth()->doesntExist()) {
            return redirect()->route('scheduleConfig.index')->withErrors(__('Please link your microsoft account first'));
        }
        MsScheduleToCalendarJob::dispatch(auth()->user(), auth()->user()->scheduleConfig(), MsScheduleToCalendarJob::CAUSED_BY['Web']);

        return redirect()->route('scheduleConfig.index')->with('success', __('Schedule has been queued for sync, it will take a few minutes'));
    }

    public function getGrouping(Request $request, GetGroupingAndModule $action): JsonResponse
    {
        [$groupings] = $action->execute($request->get('intake_code'));

        return response()->json($groupings);
    }
}
