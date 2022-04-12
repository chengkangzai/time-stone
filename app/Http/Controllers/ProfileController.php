<?php

namespace App\Http\Controllers;

use App\Actions\Profile\UpdateUserProfile;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show()
    {
        return view('auth.profile');
    }

    public function update(Request $request, UpdateUserProfile $action)
    {
        $action->execute($request->user(), $request->all());

        return redirect()->back()->with('success', 'Profile updated.');
    }
}
