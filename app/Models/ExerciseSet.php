<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExerciseSet extends Model
{
    protected $table = 'exercise_sets';

    protected $fillable = [
        'exercise_name',
        'reps',
        'weight',
        'notes',
        'logged_at',
        'workout_session_id',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'logged_at' => 'datetime',
        ];
    }

    public function workoutSession(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(WorkoutSession::class);
    }
}
