<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ProductionHealthTest extends TestCase
{
    public function test_health_check_rejects_a_mail_transport_without_real_delivery_settings(): void
    {
        config([
            'mail.default' => 'smtp',
            'mail.from.address' => 'hello@example.com',
            'mail.mailers.smtp.host' => 'smtp.mailtrap.io',
            'mail.mailers.smtp.port' => 2525,
            'mail.mailers.smtp.username' => null,
            'mail.mailers.smtp.password' => null,
        ]);

        Artisan::call('app:health');

        $this->assertStringContainsString('FAIL  Mail transport configured', Artisan::output());
    }

    public function test_health_check_accepts_smtp_with_a_sender_and_credentials(): void
    {
        config([
            'mail.default' => 'smtp',
            'mail.from.address' => 'noreply@customer.example',
            'mail.mailers.smtp.host' => 'smtp.customer.example',
            'mail.mailers.smtp.port' => 587,
            'mail.mailers.smtp.username' => 'smtp-user',
            'mail.mailers.smtp.password' => 'smtp-secret',
        ]);

        Artisan::call('app:health');

        $this->assertStringContainsString('PASS  Mail transport configured', Artisan::output());
    }
}
