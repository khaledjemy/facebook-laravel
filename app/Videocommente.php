<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Videocommente extends Model
{
    use HasFactory;
    protected  $fillable   =   ['user_id','comment','video_id'];

    
    public function users()
    {
        return $this->belongsTo(User::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function reply()
    {
        return $this->hasMany(Videoreply::class,'comment_id');
    }
}
