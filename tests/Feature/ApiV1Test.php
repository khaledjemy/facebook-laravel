<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiV1Test extends TestCase
{
    use RefreshDatabase;

    private function user(): User
    {
        return User::create(['first_name'=>'Demo','last_name'=>'User','email'=>'api@example.com','password'=>bcrypt('Secret123!')]);
    }

    public function test_public_feed_returns_json(): void
    {
        $this->getJson('/api/v1/feed')->assertOk()->assertJsonStructure(['data','current_page']);
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
}
