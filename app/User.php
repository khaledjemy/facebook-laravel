<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailBehavior;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmailContract
{
    use Notifiable, MustVerifyEmailBehavior;
    

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 'profile_photo_id', 'image', 'cover_photo_id',
        'about', 'default_post_visibility', 'dark_mode', 'is_admin', 'is_active',
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
        'dark_mode' => 'boolean',
        'is_admin' => 'boolean',
        'is_active' => 'boolean',
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

    public function getAvatarUrlAttribute(): string
    {
        $photo = $this->photopro;
        if ($photo && $photo->mediaFileExists()) {
            return $photo->url;
        }

        return asset('img/Default_avatar_profile.jpg');
    }

    public function getCoverUrlAttribute(): ?string
    {
        $cover = $this->coverpro;
        if ($cover && $cover->mediaFileExists()) {
            return $cover->url;
        }

        return null;
    }

    public function stories()
    {
        return $this->hasMany(Story::class);
    }

    public function activeStories()
    {
        return $this->hasMany(Story::class)->active();
    }

    public function blockedUsers()
    {
        return $this->hasMany(Block::class, 'user_id');
    }

    public function blockedByUsers()
    {
        return $this->hasMany(Block::class, 'blocked_id');
    }

    public function hasBlocked($userId): bool
    {
        return $this->blockedUsers()->where('blocked_id', $userId)->exists();
    }

    public function isBlockedBy($userId): bool
    {
        return $this->blockedByUsers()->where('user_id', $userId)->exists();
    }
}
