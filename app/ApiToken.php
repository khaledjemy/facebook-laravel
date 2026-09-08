<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
class ApiToken extends Model { protected $fillable=['user_id','name','token_hash','last_used_at','expires_at']; protected $casts=['last_used_at'=>'datetime','expires_at'=>'datetime']; public function user(){ return $this->belongsTo(User::class); } }
