<?php

namespace App\Http\Controllers;

use Chengkangzai\ApuSchedule\ApuSchedule;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $config = Auth::user()->scheduleConfig;
        $events = $config ? ApuSchedule::getSchedule($config->intake_code, $config->grouping, $config->except) : collect();

        return view('home', compact('events'));
    }

    public function about()
    {
        return view('about');
    }
}
