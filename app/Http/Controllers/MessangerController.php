<?php

namespace App\Http\Controllers;

use App\Block;
use App\Friend;
use App\Messanger;
use App\User;
use Illuminate\Http\Request;

class MessangerController extends Controller
{
    public function summary()
    {
        $me = (int) auth()->id();
        $messages = Messanger::with(['sender.photopro', 'receiver.photopro'])
            ->where(fn ($query) => $query->where('my_id', $me)->orWhere('user_id', $me))
            ->latest('id')
            ->limit(100)
            ->get();

        $conversations = $messages->unique(function ($message) use ($me) {
            return (int) $message->my_id === $me ? (int) $message->user_id : (int) $message->my_id;
        })->take(6)->map(function ($message) use ($me) {
            $contact = (int) $message->my_id === $me ? $message->receiver : $message->sender;
            $contactId = (int) $contact->id;
            $unread = Messanger::where('my_id', $contactId)
                ->where('user_id', $me)
                ->where('read', 0)
                ->count();

            $preview = trim((string) $message->message);
            if ($preview === '') {
                $preview = match ($message->attachment_type) {
                    'image' => 'أرسل صورة',
                    'audio' => 'أرسل مقطعًا صوتيًا',
                    default => 'أرسل مرفقًا',
                };
            }

            return [
                'id' => $contactId,
                'name' => trim($contact->first_name.' '.$contact->last_name),
                'avatar' => $contact->avatar_url,
                'preview' => $preview,
                'time_ago' => $message->created_at?->diffForHumans(short: true),
                'unread_count' => $unread,
                'is_mine' => (int) $message->my_id === $me,
                'url' => url('/messanger/'.$contactId),
            ];
        })->values();

        return response()->json([
            'unread_count' => Messanger::where('user_id', $me)->where('read', 0)->count(),
            'conversations' => $conversations,
        ]);
    }

    public function inbox()
    {
        $me = auth()->id();

        $blockedIds = Block::where('user_id', $me)->pluck('blocked_id')
            ->merge(Block::where('blocked_id', $me)->pluck('user_id'))
            ->unique()
            ->values();

        $latestMessage = Messanger::where(function ($q) use ($me) {
                $q->where('my_id', $me)->orWhere('user_id', $me);
            })
            ->when($blockedIds->isNotEmpty(), function ($q) use ($blockedIds) {
                $q->whereNotIn('my_id', $blockedIds)->whereNotIn('user_id', $blockedIds);
            })
            ->latest('id')
            ->first();

        if ($latestMessage) {
            $contactId = (int) $latestMessage->my_id === (int) $me
                ? $latestMessage->user_id
                : $latestMessage->my_id;

            return redirect('/messanger/'.$contactId);
        }

        $contact = User::whereKeyNot($me)
            ->when($blockedIds->isNotEmpty(), fn ($q) => $q->whereNotIn('id', $blockedIds))
            ->orderBy('id')
            ->first();
        abort_unless($contact, 404, 'No contacts are available.');

        return redirect('/messanger/'.$contact->id);
    }

    public function index(Request $request, $id)
    {
        $me = auth()->id();
        abort_if((int) $id === (int) $me, 422, 'You cannot start a conversation with yourself.');
        $user = User::findOrFail($id);

        $isBlocked = Block::where(function ($q) use ($me, $id) {
            $q->where('user_id', $me)->where('blocked_id', $id);
        })->orWhere(function ($q) use ($me, $id) {
            $q->where('user_id', $id)->where('blocked_id', $me);
        })->exists();

        abort_if($isBlocked, 403, 'Communication is blocked with this user.');

        $blockedIds = Block::where('user_id', $me)->pluck('blocked_id')
            ->merge(Block::where('blocked_id', $me)->pluck('user_id'))
            ->unique()
            ->values();

        $recentContactIds = Messanger::where('my_id', $me)
            ->orWhere('user_id', $me)
            ->orderByDesc('id')
            ->limit(50)
            ->get(['my_id', 'user_id'])
            ->map(fn ($m) => (int) ($m->my_id == $me ? $m->user_id : $m->my_id))
            ->reject(fn ($cId) => $blockedIds->contains($cId))
            ->unique()
            ->values()
            ->all();

        if (!in_array((int) $user->id, $recentContactIds, true)) {
            array_unshift($recentContactIds, (int) $user->id);
        }

        $friendIds = Friend::where('state', 1)
            ->where(fn ($q) => $q->where('user_id', $me)->orWhere('friends_id', $me))
            ->limit(20)
            ->get(['user_id', 'friends_id'])
            ->map(fn ($f) => (int) ($f->user_id == $me ? $f->friends_id : $f->user_id))
            ->reject(fn ($fId) => $blockedIds->contains($fId))
            ->all();

        $contactIds = array_values(array_unique(array_merge($recentContactIds, $friendIds)));

        if (count($contactIds) < 10) {
            $fallbackIds = User::whereKeyNot($me)
                ->whereNotIn('id', array_merge($contactIds, $blockedIds->all()))
                ->limit(10)
                ->pluck('id')
                ->all();
            $contactIds = array_merge($contactIds, $fallbackIds);
        }

        $users = User::with('photopro')->whereIn('id', $contactIds)->get();

        // إرسال رسالة
        if ($request->isMethod('post')) {
            $data = $request->validate([
                'text' => 'nullable|string|max:2000',
                'attachment' => 'nullable|file|mimes:jpeg,jpg,png,gif,webp,mp3,wav,ogg,pdf|max:10240',
            ]);

            $text = trim($data['text'] ?? '');
            if ($text === '' && !$request->hasFile('attachment')) {
                return response()->json([
                    'status' => false,
                    'message' => 'Message or attachment is required.'
                ], 422);
            }

            $attachmentPath = null;
            $attachmentType = null;

            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $mime = (string) $file->getMimeType();
                if (str_starts_with($mime, 'image/')) {
                    $attachmentType = 'image';
                } elseif (str_starts_with($mime, 'audio/')) {
                    $attachmentType = 'audio';
                } else {
                    $attachmentType = 'file';
                }

                $ext = $file->getClientOriginalExtension() ?: 'bin';
                $filename = uniqid('chat_', true) . '.' . $ext;
                $dest = public_path('chat_attachments/' . $me);
                if (!is_dir($dest)) {
                    mkdir($dest, 0755, true);
                }
                $file->move($dest, $filename);
                $attachmentPath = 'chat_attachments/' . $me . '/' . $filename;
            }

            $message = Messanger::create([
                'message' => $text,
                'attachment' => $attachmentPath,
                'attachment_type' => $attachmentType,
                'my_id' => $me,
                'user_id' => $user->id,
                'read' => 0
            ]);

            return response()->json([
                'status' => true,
                'id' => $message->id,
                'message' => $message->message,
                'attachment' => $message->attachment ? asset($message->attachment) : null,
                'attachment_type' => $message->attachment_type,
            ]);
        }

        // جلب الرسائل
        $messages = Messanger::with('sender.photopro')
        ->where(function ($q) use ($user, $me) {
            $q->where('my_id', $me)
              ->where('user_id', $user->id);
        })
        ->orWhere(function ($q) use ($user, $me) {
            $q->where('my_id', $user->id)
              ->where('user_id', $me);
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10, ['*'], 'page', $request->query('page', 1));

        if ($request->query('format') === 'json') {
            return response()->json(
                $messages->getCollection()->reverse()->values()->map(function ($message) {
                    if ($message->attachment) {
                        $message->attachment = asset($message->attachment);
                    }

                    return $message;
                })
            );
        }

        return view('messanger', compact('messages','users','user'));
    }

    // seen
    public function seen($id)
    {
        $me = auth()->id();
        User::findOrFail($id);

        Messanger::where('my_id', $id)
            ->where('user_id', $me)
            ->where('read', 0)
            ->update(['read' => 1]);

        return response()->json(['status' => true]);
    }
}
