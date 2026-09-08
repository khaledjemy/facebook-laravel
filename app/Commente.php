<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Commente extends Model
{
    //
    protected $fillable =   ['text_co','user_id','post_id'] ;
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
    public function commentes()
    {
        return $this->hasMany(Commente::class);
    }
    public function replie()
    {
        return $this->hasMany(Replie::class,'comment_id');
    }
     public function photopro()
    {
        return $this->belongsTo(photo::class,'profile_photo_id');
    }
    public function react()
    {
        return $this->hasMany(CommentReact::class,'comment_id');
    }
   
}
