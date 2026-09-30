<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $hidden = ['path', 'thumbnail_path'];
    protected $appends = ['playlist_url', 'thumbnail_url'];

    protected $fillable = [
        'user_id',
        'path',
        'type',
        "album_id",
        "title",
        'description',
        'duration',
        'seo_title',
        'seo_description',
        'keywords',
        'thumbnail_path',
    ];

    public function getPlaylistUrlAttribute(): string
    {
        return route('media.videos.hls', ['video' => $this->getKey(), 'file' => 'playlist.m3u8']);
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->thumbnail_path ? route('media.videos.thumbnail', $this->getKey()) : null;
    }

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
