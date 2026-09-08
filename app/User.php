<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name','last_name', 'email', 'password','profile_photo_id','image','cover_photo_id'
    ];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function comments()
    {
        return $this->hasMany(Commente::class);
    }
    public function photocommente()
    {
        return $this->hasMany(Photocommente::class);
    }
    public function photo()
    {
        return $this->belongsTo(User::class);
    }
    public function photopro()
    {
        return $this->belongsTo(photo::class,'profile_photo_id');
    }
    public function coverpro()
    {
        return $this->belongsTo(photo::class,'cover_photo_id');
    }
    
   
}
