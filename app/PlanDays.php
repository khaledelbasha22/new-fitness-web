<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PlanDays extends Model
{
    protected $table = 'plan_days';

    public function DayExercises()
    {
        return $this->hasMany(PlanDayExercises::class, 'plan_day_id', 'id');
    }

}
