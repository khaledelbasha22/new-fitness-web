<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Challenges extends Model
{
    protected $table = "challenges";

    public function ChallengeLevels()
    {
        return $this->hasMany(ChallengesLevels::class, 'challenge_id', 'id');
    }
}
