<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class React extends Model
{
    //
   protected $fillable  =   ['user_id','post_id','type'];
   public function user(){ return $this->belongsTo(User::class); }
  
 
}
