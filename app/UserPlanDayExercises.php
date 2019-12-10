<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserPlanDayExercises extends Model
{
    protected $table = 'user_plan_day_exercises';

    public function Exercises()
    {
        return $this->hasOne(Exercise::class, 'id', 'exercise_id');
    }
}
