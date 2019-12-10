<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserChallengeLevel extends Model
{
    protected $table = "user_challenge_level";

    public function ChallengeDays()
    {
        return $this->hasMany(UserChallengeLevelDays::class, 'user_challenge_level_id', 'id');
    }

    public function ChallengeData()
    {
        return $this->hasMany(UserChallengeLevelDays::class, 'user_challenge_level_id', 'id');
    }
}
