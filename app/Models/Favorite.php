<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    protected $fillable = [
        'user_id',
        'country_code',
        'country_name',
        'flag_url',
        'capital',
        'population',
        'currency',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}