<?php

namespace Tests\Feature;

use App\AdminActivity;
use App\ApiToken;
use App\ContentReport;
use App\Post;
use App\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ModerationAndPasswordResetTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $email, bool $admin = false): User
    {
        return User::create(['first_name' => 'Test', 'last_name' => 'User', 'email' => $email, 'password' => bcrypt('password123'), 'is_active' => true, 'is_admin' => $admin]);
    }

    public function test_user_can_report_a_visible_post_only_once(): void
    {
        $owner = $this->user('owner@example.com');
        $reporter = $this->user('reporter@example.com');
        $post = Post::create(['user_id' => $owner->id, 'post_text' => 'Reported', 'status' => 1, 'visibility' => 'public']);

        $this->actingAs($reporter)->postJson("/posts/{$post->id}/report", ['reason' => 'spam', 'details' => 'Repeated links'])->assertOk();
        $this->actingAs($reporter)->postJson("/posts/{$post->id}/report", ['reason' => 'other'])->assertOk();

        $this->assertSame(1, ContentReport::count());
        $this->assertDatabaseHas('content_reports', ['post_id' => $post->id, 'user_id' => $reporter->id, 'reason' => 'other', 'status' => 'pending']);
    }

    public function test_admin_can_review_report_and_action_is_logged(): void
    {
        $admin = $this->user('admin@example.com', true);
        $owner = $this->user('owner@example.com');
        $reporter = $this->user('reporter@example.com');
        $post = Post::create(['user_id' => $owner->id, 'post_text' => 'Reported', 'status' => 1]);
        $report = ContentReport::create(['user_id' => $reporter->id, 'post_id' => $post->id, 'reason' => 'spam']);

        $this->actingAs($admin)->put("/admin/reports/{$report->id}", ['status' => 'reviewed'])->assertSessionHas('success');

        $this->assertDatabaseHas('content_reports', ['id' => $report->id, 'status' => 'reviewed', 'reviewed_by' => $admin->id]);
        $this->assertDatabaseHas('admin_activities', ['admin_id' => $admin->id, 'action' => 'report.reviewed']);
    }

    public function test_password_reset_link_can_be_requested(): void
    {
        Notification::fake();
        $user = $this->user('reset@example.com');

        $this->get('/password/reset')->assertOk();
        $this->post('/password/email', ['email' => $user->email])->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_password_reset_token_changes_password_and_cannot_be_reused(): void
    {
        Notification::fake();
        $user = $this->user('reset-flow@example.com');
        ApiToken::create([
            'user_id' => $user->id,
            'name' => 'old-session',
            'token_hash' => hash('sha256', 'old-reset-token'),
            'expires_at' => now()->addDay(),
        ]);
        $token = null;

        $this->from('/password/reset')->post('/password/email', ['email' => $user->email])->assertRedirect('/password/reset');
        Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use (&$token) {
            $token = $notification->token;

            return true;
        });
        $this->assertNotNull($token);

        $payload = [
            'token' => $token,
            'email' => $user->email,
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ];
        $this->post('/password/reset', $payload)->assertRedirect(route('login'));
        $this->assertTrue(Hash::check('NewPassword123!', $user->fresh()->password));
        $this->assertDatabaseMissing('api_tokens', ['user_id' => $user->id]);
        $this->post('/password/reset', $payload)->assertSessionHasErrors('email');
        $this->assertTrue(Hash::check('NewPassword123!', $user->fresh()->password));
    }
}
