<?php

namespace App\Actions\Profile;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Validator;

class UpdateUserProfile
{
    public function execute(User $user, $data): bool
    {
        $data = Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'string', 'max:255', Rule::unique('users')->ignore(Auth::user())],
            'password' => ['nullable', 'string', 'confirmed', 'min:8'],
        ]);

        $data = collect($data);
        $data->getOrPut('password', null);

        if ($data->password != null) {
            $user->update(['password' => Hash::make($data['password'])]);
        }

        return $user->update([
            'name' => $data->name,
            'email' => $data->email,
        ]);
    }
}
