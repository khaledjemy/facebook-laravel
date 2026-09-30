<?php

namespace Tests\Feature;

use App\User;
use App\Commente;
use App\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiV1Test extends TestCase
{
    use RefreshDatabase;

    private function user(): User
    {
        $user = User::create([
            'first_name'=>'Demo',
            'last_name'=>'User',
            'email'=>'api@example.com',
            'password'=>bcrypt('Secret123!'),
            'is_active'=>true,
        ]);
        $user->forceFill(['email_verified_at' => now()])->save();

        return $user;
    }

    public function test_public_feed_returns_json(): void
    {
        $this->getJson('/api/v1/feed')->assertOk()->assertJsonStructure(['data','current_page']);
    }

    public function test_public_profile_and_post_responses_only_include_public_user_fields(): void
    {
        $author = $this->user();
        $author->update(['is_admin' => true, 'dark_mode' => true, 'default_post_visibility' => 'friends']);
        $post = Post::create([
            'user_id' => $author->id,
            'post_text' => 'Public content',
            'status' => 1,
            'visibility' => 'public',
        ]);
        $comment = Commente::create([
            'user_id' => $author->id,
            'post_id' => $post->id,
            'text_co' => 'Public comment',
        ]);

        $this->getJson('/api/v1/users/'.$author->id)
            ->assertOk()
            ->assertJsonPath('id', $author->id)
            ->assertJsonMissingPath('email')
            ->assertJsonMissingPath('is_admin')
            ->assertJsonMissingPath('is_active')
            ->assertJsonMissingPath('email_verified_at')
            ->assertJsonMissingPath('dark_mode')
            ->assertJsonMissingPath('default_post_visibility');

        $this->getJson('/api/v1/feed')
            ->assertOk()
            ->assertJsonPath('data.0.user.id', $author->id)
            ->assertJsonMissingPath('data.0.user.email')
            ->assertJsonMissingPath('data.0.user.is_admin')
            ->assertJsonMissingPath('data.0.user.is_active');

        $this->getJson('/api/v1/posts/'.$post->id)
            ->assertOk()
            ->assertJsonPath('comments.0.id', $comment->id)
            ->assertJsonMissingPath('user.email')
            ->assertJsonMissingPath('comments.0.user.email')
            ->assertJsonMissingPath('comments.0.user.is_admin');
    }

    public function test_public_api_hides_suspended_profiles(): void
    {
        $user = $this->user();
        $user->update(['is_active' => false]);

        $this->getJson('/api/v1/users/'.$user->id)->assertNotFound();
        $this->getJson('/api/v1/users')->assertOk()->assertJsonMissing(['id' => $user->id]);
    }

    public function test_protected_api_rejects_missing_token(): void
    {
        $this->getJson('/api/v1/me')->assertUnauthorized();
        $this->postJson('/api/v1/posts',['post_text'=>'Hello'])->assertUnauthorized();
    }

    public function test_token_login_create_post_and_logout_flow(): void
    {
        $user=$this->user();
        $login=$this->postJson('/api/v1/login',['email'=>$user->email,'password'=>'Secret123!','device_name'=>'test'])->assertOk()->assertJsonStructure(['token','token_type','expires_in']);
        $token=$login->json('token');
        $headers=['Authorization'=>'Bearer '.$token];
        $this->withHeaders($headers)->getJson('/api/v1/me')->assertOk()->assertJsonPath('id',$user->id);
        $this->withHeaders($headers)->postJson('/api/v1/posts',['post_text'=>'Created through API'])->assertCreated()->assertJsonPath('user_id',$user->id);
        $this->withHeaders($headers)->postJson('/api/v1/logout')->assertOk();
        $this->withHeaders($headers)->getJson('/api/v1/me')->assertUnauthorized();
    }

    public function test_api_validates_credentials_and_post_length(): void
    {
        $user=$this->user();
        $this->postJson('/api/v1/login',['email'=>$user->email,'password'=>'wrong'])->assertUnprocessable();
        $token=$this->postJson('/api/v1/login',['email'=>$user->email,'password'=>'Secret123!'])->json('token');
        $this->withHeader('Authorization','Bearer '.$token)->postJson('/api/v1/posts',['post_text'=>str_repeat('x',256)])->assertUnprocessable();
    }

    public function test_api_login_requires_an_active_verified_account(): void
    {
        $unverified = $this->user();
        $unverified->forceFill(['email_verified_at' => null])->save();
        $this->postJson('/api/v1/login', ['email' => $unverified->email, 'password' => 'Secret123!'])
            ->assertForbidden();

        $unverified->forceFill(['email_verified_at' => now(), 'is_active' => false])->save();
        $this->postJson('/api/v1/login', ['email' => $unverified->email, 'password' => 'Secret123!'])
            ->assertForbidden();

        $this->assertDatabaseCount('api_tokens', 0);
    }

    public function test_api_revokes_an_existing_token_after_account_suspension(): void
    {
        $user = $this->user();
        $token = $this->postJson('/api/v1/login', ['email' => $user->email, 'password' => 'Secret123!'])
            ->assertOk()->json('token');
        $user->update(['is_active' => false]);

        $this->withHeader('Authorization', 'Bearer '.$token)->getJson('/api/v1/me')->assertForbidden();
        $this->assertDatabaseCount('api_tokens', 0);
    }

    public function test_api_login_is_rate_limited(): void
    {
        $email = 'rate-'.bin2hex(random_bytes(6)).'@example.com';

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->postJson('/api/v1/login', ['email' => $email, 'password' => 'wrong'])
                ->assertUnprocessable();
        }

        $this->postJson('/api/v1/login', ['email' => $email, 'password' => 'wrong'])
            ->assertTooManyRequests();
    }
}
