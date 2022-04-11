<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Donate extends Model
{
    use HasFactory;

    public $fillable = ['price'];

    public function payments(): HasOne
    {
        return $this->hasOne(Payment::class);
    }
}
