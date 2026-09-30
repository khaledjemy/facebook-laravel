<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContentReport extends Model
{
    protected $fillable = ['user_id', 'post_id', 'reason', 'details', 'status', 'reviewed_by', 'reviewed_at'];

    protected $casts = ['reviewed_at' => 'datetime'];

    public function user() { return $this->belongsTo(User::class); }
    public function post() { return $this->belongsTo(Post::class); }
    public function reviewer() { return $this->belongsTo(User::class, 'reviewed_by'); }
}
