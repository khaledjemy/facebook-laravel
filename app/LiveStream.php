<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class LiveStream extends Model
{
    protected $fillable = ['user_id', 'video_id', 'title', 'description', 'visibility', 'status', 'viewer_count', 'peak_viewers', 'started_at', 'ended_at'];
    protected $casts = ['started_at' => 'datetime', 'ended_at' => 'datetime'];

    public function user() { return $this->belongsTo(User::class); }
    public function video() { return $this->belongsTo(Video::class); }
    public function comments() { return $this->hasMany(LiveComment::class); }

    public function scopeVisibleTo(Builder $query, User $viewer): Builder
    {
        $blockedIds = Block::where('user_id', $viewer->id)->pluck('blocked_id')
            ->merge(Block::where('blocked_id', $viewer->id)->pluck('user_id'))
            ->unique()
            ->values();

        if ($blockedIds->isNotEmpty()) {
            $query->whereNotIn('user_id', $blockedIds);
        }

        $friendIds = Friend::where('state', 1)
            ->where(fn ($q) => $q->where('user_id', $viewer->id)->orWhere('friends_id', $viewer->id))
            ->get()
            ->map(fn ($row) => (int) ($row->user_id == $viewer->id ? $row->friends_id : $row->user_id));

        return $query->where(function ($q) use ($viewer, $friendIds) {
            $q->where('visibility', 'public')
                ->orWhere('user_id', $viewer->id)
                ->orWhere(fn ($friends) => $friends->where('visibility', 'friends')->whereIn('user_id', $friendIds));
        });
    }
}
