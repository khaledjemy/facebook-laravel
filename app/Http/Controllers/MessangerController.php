<?php

namespace App\Http\Controllers;

use App\Messanger;
use App\User;
use Illuminate\Http\Request;

class MessangerController extends Controller
{
    public function inbox()
    {
        $contact = User::whereKeyNot(auth()->id())->orderBy('id')->first();
        abort_unless($contact, 404, 'No contacts are available.');

        return redirect('/messanger/'.$contact->id);
    }

    public function index(Request $request, $id)
    {
        $me = auth()->id();
        abort_if((int) $id === (int) $me, 422, 'You cannot start a conversation with yourself.');
        $user = User::findOrFail($id);
        $users = User::whereKeyNot($me)->get();

        // إرسال رسالة
        if ($request->isMethod('post')) {
            $data = $request->validate(['text' => 'required|string|max:2000']);

            $message = Messanger::create([
                'message'=> trim($data['text']),
                'my_id'=> $me,
                'user_id'  => $user->id,
                'read'   => 0
            ]);

            return response()->json([
                'status' => true,
                'id' => $message->id
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

        if ($request->ajax()) {
            return response()->json($messages->getCollection()->reverse()->values());
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
