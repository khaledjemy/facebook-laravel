<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Replie extends Model
{
    //
    protected $table = "replies";
    protected $fillable = ['comment_id','user_id','userreply_id','reply','media_path','media_type'];
    function comment()
    {
       return $this->belongsTo(Commente::class);
    }
    function userreply()
    {
       return $this->belongsTo(User::class,'user_id');
    }
    public function photopro()
    {
        return $this->belongsTo(photo::class,'profile_photo_id');
    }
}
