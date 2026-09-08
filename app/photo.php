<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class photo extends Model
{
    //
    protected $fillable =   ['user_id','path','state','type','album_id'];
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
