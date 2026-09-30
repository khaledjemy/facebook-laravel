<?php

namespace Tests\Feature;

use App\Post;
use App\Commente;
use App\CommentReact;
use App\React;
use App\Replie;
use App\SiteSetting;
use App\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    private function user(array $attributes = []): User
    {
        return User::create(array_merge([
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => uniqid().'@example.com',
            'password' => bcrypt('password123'),
            'is_active' => true,
            'is_admin' => false,
        ], $attributes));
    }

    public function test_admin_panel_requires_an_administrator(): void
    {
        $this->get('/admin')->assertRedirect('/login');
        $this->put('/admin/settings', [])->assertRedirect('/login');

        $member = $this->user();
        $this->actingAs($member)->get('/admin')->assertForbidden();
        $this->actingAs($member)->put('/admin/settings', [])->assertForbidden();
        $this->actingAs($member)->delete('/admin/appearance')->assertForbidden();
        $this->actingAs($this->user(['is_admin' => true]))->get('/admin')->assertOk();
    }

    public function test_guest_branding_and_signup_copy_are_neutral_and_consistent(): void
    {
        $this->withSession(['locale' => 'ar'])->get('/login')
            ->assertOk()
            ->assertSee('تواصل وشارك مع مجتمعك.')
            ->assertDontSee('Facebook')
            ->assertDontSee('Meta ©');

        $this->withSession(['locale' => 'ar'])->get('/reg')
            ->assertOk()
            ->assertSee('إنشاء حساب جديد')
            ->assertSee('البريد الإلكتروني')
            ->assertSee('إنشاء حساب')
            ->assertDontSee('Facebook')
            ->assertDontSee('Creat an account');
    }

    public function test_production_seed_creates_only_the_configured_admin_and_never_resets_password(): void
    {
        $this->app['env'] = 'production';
        config([
            'admin.initial_name' => 'Sale Admin',
            'admin.initial_email' => 'owner@example.com',
            'admin.initial_password' => 'A-long-initial-secret-2026',
        ]);

        (new DatabaseSeeder())->run();

        $admin = User::where('email', 'owner@example.com')->firstOrFail();
        $this->assertTrue($admin->is_admin);
        $this->assertSame(1, User::count());
        $this->assertSame(0, Post::count());

        $admin->update(['password' => Hash::make('rotated-admin-password')]);
        (new DatabaseSeeder())->run();

        $this->assertTrue(Hash::check('rotated-admin-password', $admin->fresh()->password));
        $this->assertSame(1, User::count());
    }

    public function test_production_seed_refuses_to_create_a_default_or_demo_admin(): void
    {
        $this->app['env'] = 'production';
        config([
            'admin.initial_name' => '',
            'admin.initial_email' => '',
            'admin.initial_password' => '',
        ]);

        try {
            (new DatabaseSeeder())->run();
            $this->fail('Production seeding should require explicit administrator credentials.');
        } catch (\InvalidArgumentException $exception) {
            $this->assertStringContainsString('INITIAL_ADMIN_EMAIL', $exception->getMessage());
        }

        $this->assertSame(0, User::count());
        $this->assertSame(0, Post::count());
    }

    public function test_production_seed_refuses_to_silently_accept_a_regular_user_as_initial_admin(): void
    {
        $this->app['env'] = 'production';
        config([
            'admin.initial_name' => 'Sale Admin',
            'admin.initial_email' => ' owner@example.com ',
            'admin.initial_password' => 'A-long-initial-secret-2026',
        ]);
        $member = $this->user(['email' => 'owner@example.com']);

        try {
            (new DatabaseSeeder())->run();
            $this->fail('Production seeding must not leave a configured initial admin as a regular member.');
        } catch (\InvalidArgumentException $exception) {
            $this->assertStringContainsString('not an active administrator', $exception->getMessage());
        }

        $this->assertFalse($member->fresh()->is_admin);
        $this->assertSame(1, User::count());
    }

    public function test_admin_can_update_site_settings(): void
    {
        $admin = $this->user(['is_admin' => true]);

        $this->actingAs($admin)->put('/admin/settings', [
            'site_name' => 'مجتمعي',
            'site_description' => 'منصة اختبار',
            'support_email' => 'support@example.com',
            'max_upload_mb' => 50,
            'posts_per_page' => 20,
            'registration_enabled' => 0,
            'maintenance_mode' => 0,
        ])->assertSessionHas('success');

        $this->assertSame('مجتمعي', SiteSetting::getValue('site_name'));
        $this->assertFalse(SiteSetting::getValue('registration_enabled'));
        $this->assertSame(50, SiteSetting::getValue('max_upload_mb'));
    }

    public function test_disabled_registration_is_enforced(): void
    {
        SiteSetting::putValue('registration_enabled', false, 'boolean');

        $this->get('/login')->assertOk()->assertDontSee('إنشاء حساب جديد');
        $this->followingRedirects()->get('/reg')->assertOk()->assertSee(__('ui.registration_closed'));
        $this->post('/regi', [
            'firstname' => 'New', 'lastname' => 'User',
            'emailsignup' => 'new@example.com',
            'passwordsignup' => 'password123',
            'passwordsignup_confirm' => 'password123',
        ])->assertForbidden();
    }

    public function test_maintenance_mode_preserves_admin_access_and_returns_a_branded_public_page(): void
    {
        SiteSetting::putValue('maintenance_mode', true, 'boolean');

        $this->get('/')->assertStatus(503)->assertSee(__('ui.maintenance_heading'));
        $this->actingAs($this->user(['is_admin' => true]))->get('/admin')->assertOk();
        $this->actingAs($this->user(['is_admin' => false]))->get('/community')->assertStatus(503);
    }

    public function test_admin_can_suspend_user_and_delete_post(): void
    {
        $admin = $this->user(['is_admin' => true]);
        $member = $this->user();
        $post = Post::create(['user_id' => $member->id, 'post_text' => 'Bad post', 'status' => 1]);

        $this->actingAs($admin)->put('/admin/users/'.$member->id, [
            'is_active' => 0, 'is_admin' => 0,
        ])->assertSessionHas('success');
        $this->assertFalse($member->fresh()->is_active);

        $this->actingAs($admin)->delete('/admin/posts/'.$post->id)->assertSessionHas('success');
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_admin_post_removal_cleans_related_social_data_and_keeps_an_audit_record(): void
    {
        $admin = $this->user(['is_admin' => true]);
        $member = $this->user();
        $post = Post::create(['user_id' => $member->id, 'post_text' => 'Bad post', 'status' => 1]);
        $comment = Commente::create(['user_id' => $member->id, 'post_id' => $post->id, 'text_co' => 'Comment']);
        $reply = Replie::create([
            'comment_id' => $comment->id,
            'user_id' => $member->id,
            'userreply_id' => $admin->id,
            'reply' => 'Reply',
        ]);
        React::create(['user_id' => $admin->id, 'post_id' => $post->id, 'type' => 1]);
        CommentReact::create(['user_id' => $admin->id, 'comment_id' => $comment->id, 'type' => 1]);
        $notificationId = (string) Str::uuid();
        DB::table('notifications')->insert([
            'id' => $notificationId,
            'type' => 'App\\Notifications\\NewCommentNotification',
            'notifiable_type' => User::class,
            'notifiable_id' => $member->id,
            'data' => json_encode(['post_id' => $post->id, 'comment_id' => $comment->id]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($admin)->delete('/admin/posts/'.$post->id)->assertSessionHas('success');

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
        $this->assertDatabaseMissing('commentes', ['id' => $comment->id]);
        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);
        $this->assertDatabaseMissing('reacts', ['post_id' => $post->id]);
        $this->assertDatabaseMissing('comment_react', ['comment_id' => $comment->id]);
        $this->assertDatabaseMissing('notifications', ['id' => $notificationId]);
        $this->assertDatabaseHas('admin_activities', [
            'action' => 'post.deleted',
            'subject_type' => Post::class,
            'subject_id' => $post->id,
        ]);
    }

    public function test_admin_cannot_remove_own_access(): void
    {
        $admin = $this->user(['is_admin' => true]);

        $this->actingAs($admin)->put('/admin/users/'.$admin->id, [
            'is_active' => 0, 'is_admin' => 0,
        ])->assertSessionHasErrors('user');

        $this->assertTrue($admin->fresh()->is_admin);
        $this->assertTrue($admin->fresh()->is_active);
    }

    public function test_admin_can_customize_and_reset_appearance(): void
    {
        Storage::fake('public');
        $admin = $this->user(['is_admin' => true]);

        $this->actingAs($admin)->put('/admin/appearance', [
            'primary_color' => '#123456',
            'accent_color' => '#abcdef',
            'page_background' => '#eeeeee',
            'navbar_color' => '#ffffff',
            'font_family' => 'Cairo',
            'card_radius' => 14,
            'login_title' => 'عنوان مخصص',
            'login_subtitle' => 'وصف مخصص',
            'footer_text' => 'My Company © 2026',
            'logo' => UploadedFile::fake()->image('logo.png', 300, 100),
            'login_background' => UploadedFile::fake()->image('background.jpg', 1200, 800),
        ])->assertSessionHas('success');

        $logo = SiteSetting::getValue('logo_path');
        Storage::disk('public')->assertExists($logo);
        $this->assertSame('#123456', SiteSetting::getValue('primary_color'));
        $this->assertSame('Cairo', SiteSetting::getValue('font_family'));
        $this->assertSame(14, SiteSetting::getValue('card_radius'));

        $this->actingAs($admin)->delete('/admin/appearance')->assertSessionHas('success');
        Storage::disk('public')->assertMissing($logo);
        $this->assertSame('#0866ff', SiteSetting::getValue('primary_color'));
        $this->assertSame('', SiteSetting::getValue('logo_path'));
    }
}
