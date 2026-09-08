<?php

namespace App\Http\Controllers;

use App\Block;
use App\Friend;
use App\User;
use Illuminate\Http\Request;

class BlockController extends Controller
{
    public function index()
    {
        $blockedUsers = Block::with('blockedUser.photopro')
            ->where('user_id', auth()->id())
            ->latest()
            ->get()
            ->map(fn ($b) => [
                'id' => $b->blockedUser->id,
                'name' => $b->blockedUser->first_name . ' ' . $b->blockedUser->last_name,
                'avatar' => $b->blockedUser->avatar_url,
                'blocked_at' => $b->created_at->diffForHumans(),
            ]);

        return response()->json([
            'status' => true,
            'blocked_users' => $blockedUsers,
        ]);
    }

    public function block($id)
    {
        $me = auth()->id();
        abort_if((int) $id === (int) $me, 422, 'You cannot block yourself.');

        $targetUser = User::findOrFail($id);

        Block::firstOrCreate([
            'user_id' => $me,
            'blocked_id' => $targetUser->id,
        ]);

        // Break any existing friendship or pending requests
        Friend::where(function ($q) use ($me, $targetUser) {
            $q->where('user_id', $me)->where('friends_id', $targetUser->id);
        })->orWhere(function ($q) use ($me, $targetUser) {
            $q->where('user_id', $targetUser->id)->where('friends_id', $me);
        })->delete();

        return response()->json([
            'status' => true,
            'message' => 'User blocked successfully.',
        ]);
    }

    public function unblock($id)
    {
        $me = auth()->id();

        Block::where('user_id', $me)
            ->where('blocked_id', $id)
            ->delete();

        return response()->json([
            'status' => true,
            'message' => 'User unblocked successfully.',
        ]);
    }
}
