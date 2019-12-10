<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Videos extends Model
{
    protected $table = 'videos';

    public function Description()
    {
        return $this->hasOne(VideosLang::class, 'video_id', 'id');
    }

    public function Views()
    {
        return $this->hasMany(VideoViews::class, 'video_id', 'id');
    }

    public function Languages()
    {
        return $this->hasMany(VideosLang::class, 'video_id', 'id');
    }




}
