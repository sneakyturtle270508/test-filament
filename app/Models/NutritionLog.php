<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NutritionLog extends Model
{
    protected $fillable = [
        'user_id',
        'calories',
        'protein',
        'carbs',
        'fat',
        'stress_level',
        'date',
    ];
}
