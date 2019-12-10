<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserPlanDays extends Model
{
    protected $table = 'user_plan_days';
    protected $dates = ['day_date'];


    public function DayExercises()
    {
        return $this->hasOne(UserPlanDayExercises::class, 'user_plan_day_id', 'id');
    }



}
