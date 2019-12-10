<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserChallengeLevelDays extends Model
{
    protected $table = "user_challenge_level_days";

    public function UserChallengeLevel()
    {
        return $this->belongsTo(UserChallengeLevel::class, 'user_challenge_level_id', 'id');
    }

    public function ChallangeLevelDayData()
    {
        return $this->belongsTo(UserChallengeLevel::class, 'user_challenge_level_id', 'id');
    }
}
