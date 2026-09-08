<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model

{
    //
    protected $fillable = ['post_text', 'user_id','status','image','video'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function commentes()
    {
        return $this->hasMany(Commente::class);
    }
    public function photos()
    {
        return $this->hasMany(Photo::class);
    }
    public function videos()
    {
        return $this->hasMany(Video::class);
    }
    public function react()
    {
        return $this->hasMany(React::class,'post_id');
    }
    
    public function photopro()
    {
        return $this->belongsTo(photo::class,'profile_photo_id');
    }

}
