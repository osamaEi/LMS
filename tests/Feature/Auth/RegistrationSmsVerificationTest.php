<?php

namespace Tests\Feature\Auth;

use App\Models\OtpVerification;
use App\Services\OtpService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Tests\TestCase;

class RegistrationSmsVerificationTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ValidateCsrfToken::class);
    }

    public function test_otp_is_sent_through_oursms_with_a_saudi_destination(): void
    {
        config([
            'services.oursms.base_url' => 'https://api.oursms.com',
            'services.oursms.api_key' => 'test-key',
            'services.oursms.sender_id' => 'Academy',
        ]);
        Http::fake(['api.oursms.com/*' => Http::response(['jobId' => 'job-1'])]);

        app(OtpService::class)->send('0501234567');

        Http::assertSent(fn ($request) =>
            $request->url() === 'https://api.oursms.com/msgs/sms'
            && $request->hasHeader('Authorization', 'Bearer test-key')
            && $request['src'] === 'Academy'
            && $request['dests'] === ['966501234567']
            && $request['msgClass'] === 'transactional'
            && $request['secure'] === true
        );
    }

    public function test_registration_otp_can_be_verified_in_the_same_session(): void
    {
        OtpVerification::create([
            'phone' => '0501234567',
            'otp' => '123456',
            'type' => 'registration',
            'expires_at' => now()->addMinutes(5),
        ]);

        $this->withSession([
            'register_phone' => '0501234567',
            'register_national_id' => '1234567890',
            'register_sms_verified' => false,
        ])->postJson('/register/otp/verify', ['otp' => '123456'])
            ->assertOk()
            ->assertJson(['success' => true])
            ->assertSessionHas('register_sms_verified', true);
    }

    public function test_registration_completion_requires_sms_verification(): void
    {
        $this->postJson('/register/complete', $this->validRegistrationData())
            ->assertForbidden()
            ->assertJson(['success' => false]);
    }

    private function validRegistrationData(): array
    {
        return [
            'phone' => '0501234567',
            'national_id' => '1234567890',
            'name' => 'Test Student',
            'email' => 'student@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'date_of_birth' => '2000-01-01',
            'gender' => 'male',
            'nationality' => 'Saudi',
            'specialization' => 'Computer Science',
            'specialization_type' => 'bachelor',
            'date_of_graduation' => '2022-01-01',
            'national_id_front' => \Illuminate\Http\UploadedFile::fake()->image('front.jpg'),
            'national_id_back' => \Illuminate\Http\UploadedFile::fake()->image('back.jpg'),
            'certificate' => \Illuminate\Http\UploadedFile::fake()->create('certificate.pdf', 100, 'application/pdf'),
            'is_confirm_user' => true,
            'is_terms' => true,
        ];
    }
}
