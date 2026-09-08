<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Post extends Model

{
    //
    protected $fillable = ['post_text', 'user_id', 'status', 'image', 'video', 'visibility', 'shared_post_id'];

    public function scopeVisibleTo(Builder $query, $user): Builder
    {
        if (!$user) {
            return $query->where('visibility', 'public');
        }

        $blockedIds = Block::where('user_id', $user->id)->pluck('blocked_id')
            ->merge(Block::where('blocked_id', $user->id)->pluck('user_id'))
            ->unique()
            ->values();

        if ($blockedIds->isNotEmpty()) {
            $query->whereNotIn('user_id', $blockedIds);
        }

        $friendIds = Friend::where('state', true)
            ->where(function ($friendQuery) use ($user) {
                $friendQuery->where('user_id', $user->id)
                    ->orWhere('friends_id', $user->id);
            })
            ->get(['user_id', 'friends_id'])
            ->map(fn ($friend) => (int) ($friend->user_id == $user->id ? $friend->friends_id : $friend->user_id))
            ->unique()
            ->values();

        return $query->where(function ($visibilityQuery) use ($user, $friendIds) {
            $visibilityQuery->where('visibility', 'public')
                ->orWhere('user_id', $user->id)
                ->orWhere(function ($friendsQuery) use ($friendIds) {
                    $friendsQuery->where('visibility', 'friends')
                        ->whereIn('user_id', $friendIds);
                });
        });
    }

    public function sharedPost()
    {
        return $this->belongsTo(Post::class, 'shared_post_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function commentes()
    {
        return $this->hasMany(Commente::class);

    }
    public function photos()
    {
        return $this->hasMany(Photo::class);
    }
    public function videos()
    {
        return $this->hasMany(Video::class);
    }
    public function react()
    {
        return $this->hasMany(React::class,'post_id');
    }
    
    public function photopro()
    {
        return $this->belongsTo(photo::class,'profile_photo_id');
    }

}
