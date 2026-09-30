<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdminActivity extends Model
{
    protected $fillable = ['admin_id', 'action', 'subject_type', 'subject_id', 'metadata', 'ip_address'];
    protected $casts = ['metadata' => 'array'];
    public function admin() { return $this->belongsTo(User::class, 'admin_id'); }
}
