<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
class Page extends Model { protected $fillable = ['user_id','name','description']; public function owner(){ return $this->belongsTo(User::class,'user_id'); } }
