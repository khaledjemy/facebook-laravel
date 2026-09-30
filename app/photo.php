<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class photo extends Model
{
    //
    protected $hidden = ['path'];
    protected $appends = ['url'];
    protected $fillable =   ['user_id','path','state','type','album_id'];

    public function getUrlAttribute(): string
    {
        return route('media.photos.show', $this->getKey());
    }

    public function mediaFileExists(): bool
    {
        $relativePath = trim((string) $this->path, '/').'/'.$this->id.$this->type;

        return str_starts_with($relativePath, 'private/')
            ? Storage::disk('local')->exists($relativePath)
            : is_file(public_path($relativePath));
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
    public function photocommentes()
    {
        return $this->hasMany(Photocommente::class,'photo_id');
    }
    public function reactphoto()
    {
        return $this->hasMany(PhotoReact::class,'photo_id');
    }

}
