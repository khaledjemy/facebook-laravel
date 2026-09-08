<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Photocommente extends Model
{
    //
    protected $table = 'photocommentes';
    protected  $fillable   =   ['user_id','comment','photo_id'];

   
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
        return $this->hasMany(photoreply::class,'comment_id');
    }
}
