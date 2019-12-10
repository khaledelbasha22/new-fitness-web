<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlanDayExercises extends Model
{
    protected $table = 'plan_day_exercises';

    public function Exercise()
    {
        return $this->hasOne(Exercise::class, 'id', 'exercise_id');
    }
}
