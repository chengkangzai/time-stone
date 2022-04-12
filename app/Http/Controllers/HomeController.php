<?php

namespace App\Http\Controllers;

use App\Actions\Schedule\GetUserConfigAndEvents;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index(GetUserConfigAndEvents $events)
    {
        [, $events] = $events->execute(Auth::user());

        return view('home', compact('events'));
    }

    public function about()
    {
        return view('about');
    }
}
