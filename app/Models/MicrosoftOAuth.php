<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MicrosoftOAuth extends Model
{
    protected $fillable = [
        'accessToken',
        'refreshToken',
        'tokenExpires',
        'userName',
        'userEmail',
        'userTimeZone',
        'userId',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
