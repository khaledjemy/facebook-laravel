<?php

namespace Tests\Feature;

use App\LiveStream;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LiveStreamingTest extends TestCase
{
    use RefreshDatabase;

    private int $userNumber = 0;

    private function user(): User
    {
        $this->userNumber++;
        return User::create([
            'first_name' => 'Live',
            'last_name' => 'User',
            'email' => 'live'.$this->userNumber.'@example.com',
            'password' => bcrypt('Secret123!'),
        ]);
    }

    public function test_user_can_create_start_comment_and_finish_a_live_stream(): void
    {
        $user = $this->user();

        $response = $this->actingAs($user)->post('/live', [
            'title' => 'اختبار البث',
            'description' => 'وصف البث',
            'visibility' => 'public',
        ]);

        $stream = LiveStream::firstOrFail();
        $response->assertRedirect(route('live.show', $stream));
        $this->actingAs($user)->post("/live/{$stream->id}/start")->assertOk()->assertJson(['status' => 'live']);
        $this->actingAs($user)->post("/live/{$stream->id}/comments", ['body' => 'تعليق مباشر'])->assertOk();
        $this->actingAs($user)->post("/live/{$stream->id}/finish")->assertOk()->assertJson(['status' => 'ended']);
        $this->assertDatabaseHas('live_comments', ['live_stream_id' => $stream->id, 'body' => 'تعليق مباشر']);
        $this->assertDatabaseHas('live_streams', ['id' => $stream->id, 'status' => 'ended']);
    }

    public function test_private_live_stream_is_hidden_from_other_users(): void
    {
        $owner = $this->user();
        $viewer = $this->user();
        $stream = LiveStream::create(['user_id' => $owner->id, 'title' => 'خاص', 'visibility' => 'only_me', 'status' => 'live']);

        $this->actingAs($viewer)->get("/live/{$stream->id}")->assertForbidden();
        $this->actingAs($viewer)->post("/live/{$stream->id}/join")->assertForbidden();
        $this->actingAs($viewer)->get("/live/{$stream->id}/status")->assertForbidden();
        $this->actingAs($viewer)->get("/live/{$stream->id}/comments")->assertForbidden();
        $this->actingAs($viewer)->post("/live/{$stream->id}/comments", ['body' => 'مرفوض'])->assertForbidden();
    }

    public function test_viewer_count_is_not_duplicated_in_same_session(): void
    {
        $owner = $this->user();
        $viewer = $this->user();
        $stream = LiveStream::create(['user_id' => $owner->id, 'title' => 'عام', 'visibility' => 'public', 'status' => 'live']);

        $this->actingAs($viewer)->post("/live/{$stream->id}/join")->assertOk()->assertJson(['viewer_count' => 1]);
        $this->actingAs($viewer)->post("/live/{$stream->id}/join")->assertOk()->assertJson(['viewer_count' => 1]);
        $this->actingAs($viewer)->post("/live/{$stream->id}/leave")->assertOk()->assertJson(['viewer_count' => 0]);
    }
}
