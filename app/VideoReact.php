<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoReact extends Model
{

    use HasFactory;
    protected $table = 'video_react';
    protected $fillable = [
        'user_id',
        'video_id',
        'type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function video()
    {
        return $this->belongsTo(Video::class);
    }
    

}
