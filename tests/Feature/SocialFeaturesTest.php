<?php

namespace Tests\Feature;

use App\Post;
use App\React;
use App\SavedPost;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class SocialFeaturesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    private function user(string $email='user@example.com'): User
    {
        return User::create(['first_name'=>'Test','last_name'=>'User','email'=>$email,'password'=>bcrypt('Secret123!')]);
    }

    public function test_private_pages_redirect_guests_to_login(): void
    {
        foreach(['/community','/friends','/saved','/memories','/messanger/1'] as $url) $this->get($url)->assertRedirect('/login');
    }

    public function test_user_can_save_and_unsave_a_post(): void
    {
        $user=$this->user(); $post=Post::create(['user_id'=>$user->id,'post_text'=>'Save me','status'=>1]);
        $this->actingAs($user)->postJson('/saved/'.$post->id)->assertOk()->assertJson(['saved'=>true]);
        $this->assertDatabaseHas('saved_posts',['user_id'=>$user->id,'post_id'=>$post->id]);
        $this->actingAs($user)->postJson('/saved/'.$post->id)->assertOk()->assertJson(['saved'=>false]);
        $this->assertDatabaseMissing('saved_posts',['user_id'=>$user->id,'post_id'=>$post->id]);
    }

    public function test_reaction_uses_authenticated_user_and_validates_type(): void
    {
        $user=$this->user(); $other=$this->user('other@example.com'); $post=Post::create(['user_id'=>$other->id,'post_text'=>'React','status'=>1]);
        $this->actingAs($user)->postJson('/like',['post_id'=>$post->id,'type_id'=>2,'liked'=>false,'user_id'=>$other->id])->assertOk();
        $this->assertDatabaseHas('reacts',['post_id'=>$post->id,'user_id'=>$user->id,'type'=>2]);
        $this->getJson('/post/'.$post->id.'/reactions')->assertOk()->assertJsonFragment([
            'name' => 'Test User', 'type' => 'Love', 'type_id' => 2, 'emoji' => '❤️',
        ]);
        $this->actingAs($user)->postJson('/like',['post_id'=>$post->id,'type_id'=>99,'liked'=>false])->assertUnprocessable();
    }

    public function test_language_switch_is_limited_to_supported_locales(): void
    {
        $this->get('/language/ar')->assertRedirect();
        $this->assertSame('ar',session('locale'));
        $this->get('/language/fr')->assertNotFound();
    }

    public function test_authenticated_main_pages_render(): void
    {
        $this->seed();
        $demo = User::where('email', 'demo@example.com')->firstOrFail();

        $pages = [
            '/', '/profile/'.$demo->id, '/post/1', '/messanger/2',
            '/community', '/pages', '/groups', '/watch', '/friends',
            '/saved', '/memories', '/search?q=Demo',
        ];

        foreach ($pages as $page) {
            $this->actingAs($demo)->get($page)->assertOk();
        }
    }

    public function test_websocket_ticket_is_authenticated_and_signed(): void
    {
        $this->getJson('/websocket-ticket')->assertUnauthorized();
        $user = $this->user();
        $ticket = $this->actingAs($user)->getJson('/websocket-ticket')->assertOk()->json();

        $this->assertSame((string) $user->id, $ticket['user_id']);
        $this->assertGreaterThan(time(), $ticket['expires']);
        $this->assertSame(
            hash_hmac('sha256', $ticket['user_id'].'|'.$ticket['expires'], (string) config('services.websocket_secret')),
            $ticket['signature']
        );
    }

    public function test_registration_requires_matching_password_and_unique_email(): void
    {
        $existing = $this->user('taken@example.com');

        $this->post('/regi', [
            'firstname' => 'New', 'lastname' => 'User', 'emailsignup' => $existing->email,
            'passwordsignup' => 'Password123!', 'passwordsignup_confirm' => 'Different123!',
        ])->assertSessionHasErrors(['emailsignup', 'passwordsignup_confirm']);

        $this->post('/regi', [
            'firstname' => 'New', 'lastname' => 'User', 'emailsignup' => 'NEW@example.com',
            'passwordsignup' => 'Password123!', 'passwordsignup_confirm' => 'Password123!',
        ])->assertRedirect();

        $created = User::where('email', 'new@example.com')->firstOrFail();
        $this->assertTrue(Hash::check('Password123!', $created->password));
    }

    public function test_mixed_media_upload_uses_safe_extensions(): void
    {
        Queue::fake();
        $user = User::forceCreate([
            'id' => 999, 'first_name' => 'Upload', 'last_name' => 'Tester',
            'email' => 'upload@example.com', 'password' => bcrypt('Password123!'),
        ]);
        $imageTemp = tempnam(sys_get_temp_dir(), 'social-image-');
        $videoTemp = tempnam(sys_get_temp_dir(), 'social-video-');
        file_put_contents($imageTemp, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII='));
        file_put_contents($videoTemp, hex2bin('000000186674797069736F6D0000020069736F6D69736F32'));

        try {
            $this->actingAs($user)->post('/posts', [
                'post_text' => 'Mixed upload',
                'files' => [
                    new UploadedFile($imageTemp, 'avatar.png', null, null, true),
                    new UploadedFile($videoTemp, 'clip.mp4', null, null, true),
                ],
            ])->assertOk();

            $this->assertDatabaseHas('photos', ['user_id' => 999, 'type' => '.png']);
            $this->assertDatabaseHas('videos', ['user_id' => 999, 'type' => '.mp4']);
        } finally {
            File::deleteDirectory(public_path('images/users/999'));
            File::deleteDirectory(public_path('video/users/999'));
            if (is_file($imageTemp)) unlink($imageTemp);
            if (is_file($videoTemp)) unlink($videoTemp);
        }
    }

    public function test_user_can_update_profile_and_cover_images(): void
    {
        $user = $this->user();

        try {
            $profile = $this->actingAs($user)->postJson('/make-profile-picture', [
                'files' => UploadedFile::fake()->createWithContent('profile.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=')),
                'cover' => false,
            ])->assertOk()->assertJsonStructure(['profile']);

            $cover = $this->actingAs($user)->postJson('/make-profile-picture', [
                'files' => UploadedFile::fake()->createWithContent('cover.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=')),
                'cover' => true,
            ])->assertOk()->assertJsonStructure(['cover']);

            $user->refresh();
            $this->assertNotNull($user->profile_photo_id);
            $this->assertNotNull($user->cover_photo_id);
            $this->assertNotSame($user->profile_photo_id, $user->cover_photo_id);
        } finally {
            File::deleteDirectory(public_path('images/users/'.$user->id));
        }
    }
}
