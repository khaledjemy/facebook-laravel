<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_verification_migration_preserves_existing_users_as_verified(): void
    {
        $this->assertTrue(Schema::hasColumn('users', 'email_verified_at'));
        $this->assertTrue(Schema::hasColumn('users', 'remember_token'));
        $this->assertSame(0, User::whereNull('email_verified_at')->count());
    }

    private function unverifiedUser(string $email = 'verify@example.com'): User
    {
        return User::create([
            'first_name' => 'Verify',
            'last_name' => 'Member',
            'email' => $email,
            'password' => Hash::make('Password123!'),
        ]);
    }

    public function test_registration_sends_verification_and_restricts_the_new_account(): void
    {
        Notification::fake();

        $this->post('/regi', [
            'firstname' => 'New',
            'lastname' => 'Member',
            'emailsignup' => '  NEW@Example.com ',
            'passwordsignup' => 'Password123!',
            'passwordsignup_confirm' => 'Password123!',
        ])->assertRedirect(route('verification.notice'));

        $user = User::where('email', 'new@example.com')->firstOrFail();
        $this->assertNull($user->email_verified_at);
        Notification::assertSentTo($user, VerifyEmail::class);

        $this->get('/email/verify')->assertOk()->assertSee('dir="ltr"', false)->assertSee(__('ui.verify_email_title'));
        $this->get('/community')->assertRedirect(route('verification.notice'));
    }

    public function test_verified_registration_notifies_verified_account_to_continue(): void
    {
        Notification::fake();

        $this->post('/regi', [
            'firstname' => 'New',
            'lastname' => 'Member',
            'emailsignup' => 'verified@example.com',
            'passwordsignup' => 'Password123!',
            'passwordsignup_confirm' => 'Password123!',
        ])->assertRedirect(route('verification.notice'));

        $user = User::where('email', 'verified@example.com')->firstOrFail();
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(30),
            ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
        );

        $this->get($verificationUrl)->assertRedirect('/');
        $this->get('/community')->assertOk();
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_registration_rejects_duplicate_email_without_case_sensitivity(): void
    {
        $this->unverifiedUser('duplicate@example.com');

        $this->from('/reg')->post('/regi', [
            'firstname' => 'Duplicate',
            'lastname' => 'Member',
            'emailsignup' => 'DUPLICATE@EXAMPLE.COM',
            'passwordsignup' => 'Password123!',
            'passwordsignup_confirm' => 'Password123!',
        ])->assertRedirect('/reg')->assertSessionHasErrors(['emailsignup']);
    }

    public function test_registration_form_matches_the_selected_language(): void
    {
        $this->get('/reg')->assertOk()->assertSee('First name')->assertSee('Already have an account? Sign in');
        $this->withSession(['locale' => 'ar'])->get('/reg')
            ->assertOk()
            ->assertSee('lang="ar"', false)
            ->assertSee('الاسم الأول')
            ->assertSee('لديك حساب بالفعل؟ سجّل الدخول');
    }

    public function test_signed_verification_link_activates_account_and_allows_access(): void
    {
        $user = $this->unverifiedUser();
        $this->actingAs($user);
        $user->forceFill(['email_verified_at' => null])->save();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(30),
            ['id' => $user->id, 'hash' => sha1($user->getEmailForVerification())]
        );

        $this->get($verificationUrl)->assertRedirect('/');
        $this->assertNotNull($user->fresh()->email_verified_at);
        $this->get('/community')->assertOk();
    }

    public function test_unverified_login_redirects_to_notice_and_resend_sends_notification(): void
    {
        Notification::fake();
        $user = $this->unverifiedUser('resend@example.com');

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'Password123!',
        ])->assertRedirect(route('verification.notice'));

        for ($attempt = 0; $attempt < 6; $attempt++) {
            $this->post('/email/verification-notification')->assertRedirect();
        }
        $this->post('/email/verification-notification')->assertTooManyRequests();
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_password_recovery_does_not_reveal_whether_an_email_exists(): void
    {
        Notification::fake();
        $user = $this->unverifiedUser('reset@example.com');
        $message = __('ui.reset_if_email_exists');

        $this->from('/password/reset')->post('/password/email', ['email' => $user->email])
            ->assertRedirect('/password/reset')
            ->assertSessionHas('status', __('ui.reset_link_sent'))
            ->assertSessionHasNoErrors();

        Notification::assertSentTo($user, ResetPassword::class);

        $this->from('/password/reset')->post('/password/email', ['email' => 'unknown@example.com'])
            ->assertRedirect('/password/reset')
            ->assertSessionHas('status', __('ui.reset_link_sent'))
            ->assertSessionHasNoErrors();
    }
}
