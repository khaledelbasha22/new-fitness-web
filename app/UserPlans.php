<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserPlans extends Model
{
    protected $table = 'user_plans';
    protected $dates = ['start_Date', 'end_Date'];




    public function User()
    {
        return $this->belongsTo(Users::class, 'user_id', 'id');
    }


    public function PlanDays()
    {
        return $this->hasMany(UserPlanDays::class, 'user_plan_id', 'id');
    }




    public function Plan()
    {
        return $this->belongsTo(Plans::class, 'plan_id', 'id');
    }



}
