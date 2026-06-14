<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizResult extends Model
{
    protected $fillable = [
        'user_id',
        'score',
        'total_questions',
        'correct_answers',
        'duration_seconds',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getPercentageAttribute(): int
    {
        if ($this->total_questions === 0) return 0;
        return (int) round(($this->correct_answers / $this->total_questions) * 100);
    }
}