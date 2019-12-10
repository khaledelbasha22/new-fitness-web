<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserChallenges extends Model
{
    protected $table = "user_challenges";

    public function Levels()
    {
        return $this->hasMany(UserChallengeLevel::class, 'user_challenge_id', 'id');
    }
}
