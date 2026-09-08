<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class videoreply extends Model
{
    use HasFactory;

    protected $fillable =   ['comment_id','user_id','userreply_id','reply'];
    function photocomment()
    {
       return $this->belongsTo(Videocommente::class);
    }
    function comment()
    {
       return $this->belongsTo(Commente::class);
    }
    function userreply()
    {
       return $this->belongsTo(User::class,'user_id');
    }
}
