<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'path',
        'type',
        "album_id",
        "title",
        'description',
        'duration',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
    public function users()
    {
        return $this->hasMany(User::class);
    }
    public function videocommentes()
    {
        return $this->hasMany(Videocommente::class,'video_id');
    }
    public function videoreact()
    {
        return $this->hasMany(VideoReact::class,'video_id');
    }
}
