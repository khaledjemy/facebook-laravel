<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommentReact extends Model
{
    use HasFactory;
    protected $table = 'comment_react';
    protected $fillable =   ['comment_id','user_id','type'] ;
   
}
