<?php

namespace Tests;

use App\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function actingAs(Authenticatable $user, $guard = null)
    {
        // Existing test fixtures represent established accounts unless a test explicitly clears this.
        if ($user instanceof User && $user->exists && !$user->hasVerifiedEmail()) {
            $user->forceFill(['email_verified_at' => now()])->save();
        }

        return parent::actingAs($user, $guard);
    }
}
