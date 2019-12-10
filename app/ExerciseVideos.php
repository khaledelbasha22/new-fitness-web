<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExerciseVideos extends Model
{
    protected $table = 'exercise_videos';

    public function Video()
    {
        return $this->hasOne(Videos::class, 'id', 'video_id');
    }
}
