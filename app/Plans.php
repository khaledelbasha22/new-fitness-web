<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Plans extends Model
{
    protected $table = 'plans';

    public function PlanLang()
    {
        return $this->hasOne(PlansLang::class, 'plan_id', 'id');
    }
}
