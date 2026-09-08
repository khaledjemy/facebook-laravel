<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PhotoReact extends Model
{
    protected $table = 'photo_react';
    protected $fillable  =   ['user_id','photo_id','type'];
}
