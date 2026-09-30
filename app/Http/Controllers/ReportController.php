<?php

namespace App\Http\Controllers;

use App\ContentReport;
use App\Post;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReportController extends Controller
{
    public function store(Request $request, Post $post)
    {
        abort_if((int) $post->user_id === (int) $request->user()->id, 422, 'لا يمكنك الإبلاغ عن منشورك.');
        abort_unless(Post::visibleTo($request->user())->whereKey($post->id)->exists(), 404);

        $data = $request->validate([
            'reason' => ['required', Rule::in(['spam', 'harassment', 'violence', 'nudity', 'false_information', 'other'])],
            'details' => ['nullable', 'string', 'max:500'],
        ]);

        ContentReport::updateOrCreate(
            ['user_id' => $request->user()->id, 'post_id' => $post->id],
            $data + ['status' => 'pending', 'reviewed_by' => null, 'reviewed_at' => null]
        );

        return response()->json(['status' => true, 'message' => 'تم إرسال البلاغ للمراجعة.']);
    }
}
