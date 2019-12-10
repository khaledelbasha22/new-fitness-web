<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    protected $table = 'exercise';

    public function Videos()
    {
        return $this->hasMany(ExerciseVideos::class, 'exercise_id', 'id');
    }

    public function Description()
    {
        return $this->hasOne(ExerciseLang::class, 'exercise_id', 'id');
    }
}
