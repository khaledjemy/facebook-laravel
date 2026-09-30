<?php

namespace Tests\Feature;

use App\Block;
use App\ApiToken;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SettingsAndPrivacyTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_settings()
    {
        $response = $this->get('/settings');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_settings_page()
    {
        $user = User::create([
            'first_name' => 'Khaled',
            'last_name' => 'Gemy',
            'email' => 'khaled@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->actingAs($user)->get('/settings');
        $response->assertStatus(200);
        $response->assertSee('الإعدادات والخصوصية');
        $response->assertSee('khaled@example.com');
    }

    public function test_user_can_update_account_details()
    {
        $user = User::create([
            'first_name' => 'Original',
            'last_name' => 'User',
            'email' => 'original@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->actingAs($user)->post('/settings/account', [
            'first_name' => 'Updated',
            'last_name' => 'Name',
            'email' => 'updated@example.com',
            'about' => 'Bio test description',
        ]);

        $response->assertSessionHas('success');
        $user->refresh();

        $this->assertEquals('Updated', $user->first_name);
        $this->assertEquals('Name', $user->last_name);
        $this->assertEquals('updated@example.com', $user->email);
        $this->assertEquals('Bio test description', $user->about);
    }

    public function test_changing_email_requires_reverification_and_revokes_api_tokens()
    {
        $user = User::create([
            'first_name' => 'Verified',
            'last_name' => 'Member',
            'email' => 'verified@example.com',
            'password' => Hash::make('password123'),
        ]);
        $user->forceFill(['email_verified_at' => now()])->save();
        ApiToken::create([
            'user_id' => $user->id,
            'name' => 'test-device',
            'token_hash' => hash('sha256', 'secret-token'),
            'expires_at' => now()->addDay(),
        ]);

        $response = $this->actingAs($user)->postJson('/settings/account', [
            'first_name' => 'Verified',
            'last_name' => 'Member',
            'email' => 'new-address@example.com',
        ]);

        $response->assertOk()->assertJsonPath('email_verification_required', true);
        $this->assertNull($user->fresh()->email_verified_at);
        $this->assertDatabaseMissing('api_tokens', ['user_id' => $user->id]);
    }

    public function test_account_update_enforces_unique_email()
    {
        User::create([
            'first_name' => 'Existing',
            'last_name' => 'Person',
            'email' => 'existing@example.com',
            'password' => Hash::make('password123'),
        ]);

        $user = User::create([
            'first_name' => 'Current',
            'last_name' => 'Person',
            'email' => 'current@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->actingAs($user)->post('/settings/account', [
            'first_name' => 'Current',
            'last_name' => 'Person',
            'email' => 'existing@example.com',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_user_can_change_password_with_valid_current_password()
    {
        $user = User::create([
            'first_name' => 'Pass',
            'last_name' => 'Test',
            'email' => 'pass@example.com',
            'password' => Hash::make('old_password_123'),
        ]);
        ApiToken::create([
            'user_id' => $user->id,
            'name' => 'old-session',
            'token_hash' => hash('sha256', 'old-secret-token'),
            'expires_at' => now()->addDay(),
        ]);

        $response = $this->actingAs($user)->post('/settings/password', [
            'current_password' => 'old_password_123',
            'password' => 'new_password_456',
            'password_confirmation' => 'new_password_456',
        ]);

        $response->assertSessionHas('success');
        $user->refresh();

        $this->assertTrue(Hash::check('new_password_456', $user->password));
        $this->assertDatabaseMissing('api_tokens', ['user_id' => $user->id]);
    }

    public function test_password_change_fails_if_current_password_is_wrong()
    {
        $user = User::create([
            'first_name' => 'Pass',
            'last_name' => 'Test',
            'email' => 'passwrong@example.com',
            'password' => Hash::make('correct_password'),
        ]);

        $response = $this->actingAs($user)->post('/settings/password', [
            'current_password' => 'wrong_password',
            'password' => 'new_password_456',
            'password_confirmation' => 'new_password_456',
        ]);

        $response->assertSessionHasErrors('current_password');
        $user->refresh();

        $this->assertTrue(Hash::check('correct_password', $user->password));
    }

    public function test_user_can_update_default_post_visibility()
    {
        $user = User::create([
            'first_name' => 'Privacy',
            'last_name' => 'User',
            'email' => 'privacy@example.com',
            'password' => Hash::make('password123'),
            'default_post_visibility' => 'public',
        ]);

        $response = $this->actingAs($user)->post('/settings/privacy', [
            'default_post_visibility' => 'friends',
        ]);

        $response->assertSessionHas('success');
        $user->refresh();

        $this->assertEquals('friends', $user->default_post_visibility);
    }

    public function test_user_can_update_dark_mode_and_preferences()
    {
        $user = User::create([
            'first_name' => 'Dark',
            'last_name' => 'Mode',
            'email' => 'dark@example.com',
            'password' => Hash::make('password123'),
            'dark_mode' => false,
        ]);

        $response = $this->actingAs($user)->postJson('/settings/preferences', [
            'dark_mode' => 1,
            'locale' => 'ar',
        ]);

        $response->assertJson([
            'status' => true,
            'dark_mode' => true,
        ]);

        $user->refresh();
        $this->assertTrue($user->dark_mode);
    }

    public function test_settings_page_lists_blocked_users_and_allows_unblock()
    {
        $user = User::create([
            'first_name' => 'Blocker',
            'last_name' => 'User',
            'email' => 'blocker@example.com',
            'password' => Hash::make('password123'),
        ]);

        $blocked = User::create([
            'first_name' => 'Blocked',
            'last_name' => 'Person',
            'email' => 'blocked@example.com',
            'password' => Hash::make('password123'),
        ]);

        Block::create([
            'user_id' => $user->id,
            'blocked_id' => $blocked->id,
        ]);

        $response = $this->actingAs($user)->get('/settings?tab=blocking');
        $response->assertStatus(200);
        $response->assertSee('Blocked Person');

        $unblockResponse = $this->actingAs($user)->postJson('/users/' . $blocked->id . '/unblock');
        $unblockResponse->assertJson(['status' => true]);

        $this->assertDatabaseMissing('blocks', [
            'user_id' => $user->id,
            'blocked_id' => $blocked->id,
        ]);
    }
}
