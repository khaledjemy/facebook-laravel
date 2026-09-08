<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Messanger extends Model
{
    protected $fillable = [
        'my_id',
        'user_id',
        'message',
        'attachment',
        'attachment_type',
        'read'
    ];

    public function sender(){
        return $this->belongsTo(User::class, 'my_id');
    }

    public function receiver(){
        return $this->belongsTo(User::class, 'user_id');
    }
}