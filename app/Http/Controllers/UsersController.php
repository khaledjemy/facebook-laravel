<?php

namespace App\Http\Controllers;

use App\Album;
use App\Friend;
use App\Notifications\FriendAcceptedNotification;
use App\Notifications\FriendRequestNotification;
use App\Post;
use App\User;
use App\photo;
use App\Video;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Ramsey\Collection\Map\AssociativeArrayMap;

class UsersController extends Controller
{
    //
    public function profile($id, Request $request)
    {
        if (auth()->check()) {
            $me = auth()->id();
            $isBlocked = \App\Block::where(function ($q) use ($me, $id) {
                $q->where('user_id', $me)->where('blocked_id', $id);
            })->orWhere(function ($q) use ($me, $id) {
                $q->where('user_id', $id)->where('blocked_id', $me);
            })->exists();

            abort_if($isBlocked, 404, 'This profile is not available.');
        }

        $allphoto = photo::where('user_id', $id)->get()->filter(
            fn ($photo) => is_file(public_path($photo->path.$photo->id.$photo->type))
        )->values();
        $allvideo = Video::where('user_id', $id)->latest()->get();
        $albums = Album::where('user_id', $id)->with('photos')->get();
        $friends = $this->friends($id);
        $profile_page = User::where('id', $id)->with('photopro', 'coverpro')->firstOrFail();

        $page = $request->query('page', 1);
        $p_postes = Post::visibleTo(auth()->user())->where('user_id', $id)
            ->with('user.photopro', 'sharedPost.user.photopro', 'commentes.react', 'commentes.replie.userreply.photopro', 'react', 'commentes.user.photopro')
            ->orderBy('created_at', 'desc')
            ->paginate(2, ['*'], 'page', $page);

        $imges = [];
        $videos = [];

        if ($p_postes->isNotEmpty()) {
            foreach ($p_postes as $post) {
                $imges[$post->id] = [];
                $videos[$post->id] = [];
                $im_arr = json_decode($post->image, true);
                $vd_arr = json_decode($post->video, true);
                if ($post->image != null && is_array($im_arr)) {
                    foreach ($im_arr as $key => $img) {
                        $imgess = Photo::where('user_id', $post->user_id)->where('id', $img)->get();
                        if ($imgess->isNotEmpty()) {
                            $imges[$post->id][] = $imgess;
                        }
                    }
                }
                if ($post->video != null && is_array($vd_arr)) {
                    foreach ($vd_arr as $key => $vid) {
                        $videoss = Video::where('user_id', $post->user_id)->where('id', $vid)->get();
                        if ($videoss->isNotEmpty()) {
                            $videos[$post->id][] = $videoss;
                        }
                    }
                }
            }
        }

        $profile = auth()->check() ? auth()->user()->loadMissing('photopro', 'coverpro') : null;

        return view("profile", compact('profile_page', 'p_postes', 'friends', 'imges', 'videos', 'allphoto', 'allvideo', 'albums', 'profile'));
    }
    public function profilenav()
    {
        $id=auth()->id();
    $profile   =  User::where('id',$id)->with('photopro','coverpro')->firstOrFail();
    //$profile->setRelation('photopro', Photo::find(3));

        
        return $profile;
    }

    public function hoverCard($id)
    {
        $viewer = auth()->id();
        $user = User::with('photopro')->findOrFail($id);
        $viewerFriends = $this->approvedFriendIds($viewer);
        $userFriends = $this->approvedFriendIds($user->id);
        $mutualIds = $viewer === $user->id ? [] : array_values(array_intersect($viewerFriends, $userFriends));
        $mutualNames = User::whereIn('id', array_slice($mutualIds, 0, 2))
            ->get()->map(fn ($friend) => trim($friend->first_name.' '.$friend->last_name))->values();

        $friendship = Friend::where(function ($query) use ($viewer, $user) {
            $query->where('user_id', $viewer)->where('friends_id', $user->id);
        })->orWhere(function ($query) use ($viewer, $user) {
            $query->where('user_id', $user->id)->where('friends_id', $viewer);
        })->first();

        $friendStatus = 'none';
        if ($viewer === $user->id) {
            $friendStatus = 'self';
        } elseif ($friendship?->state) {
            $friendStatus = 'friends';
        } elseif ($friendship) {
            $friendStatus = $friendship->user_id === $viewer ? 'outgoing' : 'incoming';
        }

        $photo = $user->photopro;
        $avatar = $photo && is_file(public_path($photo->path.$photo->id.$photo->type))
            ? asset($photo->path.$photo->id.$photo->type)
            : asset('img/Default_avatar_profile.jpg');

        return response()->json([
            'id' => $user->id,
            'name' => trim($user->first_name.' '.$user->last_name),
            'avatar' => $avatar,
            'about' => $user->about ?: 'عضو في مجتمعنا',
            'member_since' => $user->created_at?->translatedFormat('F Y'),
            'mutual_count' => count($mutualIds),
            'mutual_names' => $mutualNames,
            'friend_status' => $friendStatus,
            'profile_url' => url('/profile/'.$user->id),
            'message_url' => url('/messanger/'.$user->id),
        ]);
    }

    private function approvedFriendIds(int $userId): array
    {
        return Friend::where('state', true)
            ->where(fn ($query) => $query->where('user_id', $userId)->orWhere('friends_id', $userId))
            ->get()
            ->map(fn ($friend) => (int) ($friend->user_id === $userId ? $friend->friends_id : $friend->user_id))
            ->unique()->values()->all();
    }
    public function friends($id)
    {
        $me     =   auth()->id();
        $other  =   User::where('id',$id)->firstOrFail();

        $friends =   Friend::where('user_id',$me)->orWhere('friends_id', $me)->get();
        if($me==$id){
            return $friends="yourProfile";
        }else{
            $result = [4, null, null,$friends]; 

            foreach ($friends as $friend) {
                if (($friend->user_id == $id && $friend->friends_id == $me) ||
                    ($friend->user_id == $me && $friend->friends_id == $id)) {
                    if ($friend->state == 1) {
                        $result = [1, $friend->user_id, $friend->friends_id,$friends]; // friend
                    } elseif ($friend->state == 0) {
                        if ($friend->user_id == $me && $friend->friends_id == $id) {
                            $result = [2, $friend->user_id, $friend->friends_id,$friends]; // cancel
                        } elseif ($friend->friends_id == $me && $friend->user_id == $id) {
                            $result = [3, $friend->user_id, $friend->friends_id,$friends]; // accept
                        }
                    }
                    break; 
                }
            }
        }

        return $result;
    }
    public function friend_action(Request $request)
    {
        $validated = $request->validate([
            'user' => 'required|integer|exists:users,id',
            'type' => 'required|string|in:add,cancel,accept,cancel_r,remove',
        ]);

        $me = auth()->id();
        $targetId = (int) $validated['user'];

        abort_if($targetId === (int) $me, 422, 'Cannot perform friendship action on yourself.');

        $isBlocked = \App\Block::where(function ($q) use ($me, $targetId) {
            $q->where('user_id', $me)->where('blocked_id', $targetId);
        })->orWhere(function ($q) use ($me, $targetId) {
            $q->where('user_id', $targetId)->where('blocked_id', $me);
        })->exists();

        abort_if($isBlocked, 403, 'Cannot perform friendship action on a blocked user.');

        $type = $validated['type'];

        if ($type === 'add') {
            $existing = Friend::where(function ($q) use ($me, $targetId) {
                $q->where('user_id', $me)->where('friends_id', $targetId);
            })->orWhere(function ($q) use ($me, $targetId) {
                $q->where('user_id', $targetId)->where('friends_id', $me);
            })->first();

            if (!$existing) {
                Friend::create([
                    'user_id' => $me,
                    'friends_id' => $targetId,
                    'state' => 0,
                ]);

                $targetUser = User::find($targetId);
                if ($targetUser) {
                    $targetUser->notify(new FriendRequestNotification(auth()->user()));
                }
            }
        } elseif ($type === 'cancel') {
            Friend::where('user_id', $me)->where('friends_id', $targetId)->where('state', 0)->delete();
        } elseif ($type === 'accept') {
            Friend::where('friends_id', $me)->where('user_id', $targetId)->update(['state' => 1]);

            $targetUser = User::find($targetId);
            if ($targetUser) {
                $targetUser->notify(new FriendAcceptedNotification(auth()->user()));
            }
        } elseif ($type === 'cancel_r') {
            Friend::where('friends_id', $me)->where('user_id', $targetId)->where('state', 0)->delete();
        } elseif ($type === 'remove') {
            Friend::where(function ($q) use ($me, $targetId) {
                $q->where('user_id', $me)->where('friends_id', $targetId);
            })->orWhere(function ($q) use ($me, $targetId) {
                $q->where('user_id', $targetId)->where('friends_id', $me);
            })->delete();
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => true,
                'message' => 'Action performed successfully.',
                'type' => $type,
            ]);
        }

        return back();
    }
}
