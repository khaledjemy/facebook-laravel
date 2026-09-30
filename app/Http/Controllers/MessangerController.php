<?php

namespace App\Http\Controllers;

use App\Block;
use App\Friend;
use App\Messanger;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MessangerController extends Controller
{
    public function summary()
    {
        $me = (int) auth()->id();
        $blockedIds = Block::where('user_id', $me)->pluck('blocked_id')
            ->merge(Block::where('blocked_id', $me)->pluck('user_id'))
            ->unique()
            ->values();
        $messages = Messanger::with(['sender.photopro', 'receiver.photopro'])
            ->where(fn ($query) => $query->where('my_id', $me)->orWhere('user_id', $me))
            ->when($blockedIds->isNotEmpty(), function ($query) use ($blockedIds) {
                $query->whereNotIn('my_id', $blockedIds)->whereNotIn('user_id', $blockedIds);
            })
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

        return redirect('/friends');
    }

    public function index(Request $request, $id)
    {
        $me = auth()->id();
        abort_if((int) $id === (int) $me, 422, 'You cannot start a conversation with yourself.');
        $user = User::findOrFail($id);
        abort_if($user->is_active === false, 404);

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

        $users = User::with('photopro')
            ->whereIn('id', $contactIds)
            ->where(fn ($query) => $query->whereNull('is_active')->orWhere('is_active', true))
            ->get();

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

                $ext = $this->safeAttachmentExtension($mime);
                $filename = bin2hex(random_bytes(16)) . '.' . $ext;
                $attachmentPath = Storage::disk('local')->putFileAs(
                    'private/chat_attachments/' . $me,
                    $file,
                    $filename
                );
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
                'attachment' => $message->attachment ? route('messages.attachment', $message) : null,
                'attachment_type' => $message->attachment_type,
            ]);
        }

        // Opening a conversation means its incoming messages have been seen.
        Messanger::where('my_id', $user->id)
            ->where('user_id', $me)
            ->where('read', 0)
            ->update(['read' => 1]);

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
                        $message->attachment = route('messages.attachment', $message);
                    }

                    return $message;
                })
            );
        }

        return view('messanger', compact('messages','users','user'));
    }

    public function attachment(Messanger $message)
    {
        $userId = (int) auth()->id();
        abort_unless(in_array($userId, [(int) $message->my_id, (int) $message->user_id], true), 404);
        $isBlocked = Block::where(function ($query) use ($message) {
            $query->where('user_id', $message->my_id)->where('blocked_id', $message->user_id);
        })->orWhere(function ($query) use ($message) {
            $query->where('user_id', $message->user_id)->where('blocked_id', $message->my_id);
        })->exists();
        abort_if($isBlocked, 404);

        // New uploads are private. Continue serving old public files only through this
        // participant-checked endpoint while existing installations migrate their data.
        if (str_starts_with((string) $message->attachment, 'private/chat_attachments/')) {
            $disk = Storage::disk('local');
            abort_unless($disk->exists($message->attachment), 404);
            return $disk->response($message->attachment, basename($message->attachment), [
                'Cache-Control' => 'private, no-store',
                'X-Content-Type-Options' => 'nosniff',
            ]);
        }

        $legacyPrefix = 'chat_attachments/' . (int) $message->my_id . '/';
        abort_unless(str_starts_with((string) $message->attachment, $legacyPrefix), 404);
        $filename = substr((string) $message->attachment, strlen($legacyPrefix));
        abort_unless(preg_match('/^[a-f0-9]{32}\.[a-z0-9]{1,8}$/i', $filename), 404);
        $legacyPath = public_path($legacyPrefix . $filename);
        abort_unless(is_file($legacyPath), 404);

        return response()->file($legacyPath, [
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private function safeAttachmentExtension(string $mime): string
    {
        return match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'audio/mpeg' => 'mp3',
            'audio/wav', 'audio/x-wav' => 'wav',
            'audio/ogg' => 'ogg',
            'application/pdf' => 'pdf',
            default => throw new \InvalidArgumentException('Unsupported attachment type.'),
        };
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
