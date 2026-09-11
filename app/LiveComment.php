<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LiveComment extends Model
{
    protected $fillable = ['live_stream_id', 'user_id', 'body'];
    public function user() { return $this->belongsTo(User::class); }
    public function stream() { return $this->belongsTo(LiveStream::class, 'live_stream_id'); }
}
