<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Friend extends Model
{
    //
    protected $fillable = ['user_id', 'friends_id', 'state'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }




    
}
