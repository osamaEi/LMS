<?php

namespace Tests\Feature\Auth;

use App\Models\OtpVerification;
use App\Services\OtpService;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class RegistrationSmsVerificationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(ValidateCsrfToken::class);

        if (!Schema::hasTable('otp_verifications')) {
            Schema::create('otp_verifications', function (Blueprint $table) {
                $table->id();
                $table->string('phone', 20);
                $table->string('otp', 6);
                $table->string('type')->default('registration');
                $table->string('status')->default('sent');
                $table->timestamp('sent_at')->nullable();
                $table->string('provider_reference')->nullable();
                $table->text('failure_reason')->nullable();
                $table->timestamp('verified_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->integer('attempts')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasColumn('otp_verifications', 'status')) {
            Schema::table('otp_verifications', function (Blueprint $table) {
                $table->string('status')->default('sent');
                $table->timestamp('sent_at')->nullable();
                $table->string('provider_reference')->nullable();
                $table->text('failure_reason')->nullable();
            });
        }
    }

    protected function tearDown(): void
    {
        OtpVerification::where('phone', '0501234567')->delete();
        parent::tearDown();
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

        $this->assertDatabaseHas('otp_verifications', [
            'phone' => '0501234567',
            'status' => 'sent',
            'provider_reference' => 'job-1',
        ]);
        $this->assertNotNull(OtpVerification::where('phone', '0501234567')->value('sent_at'));
    }

    public function test_registration_otp_can_be_verified_in_the_same_session(): void
    {
        OtpVerification::create([
            'phone' => '0501234567',
            'otp' => '123456',
            'type' => 'registration',
            'status' => 'sent',
            'sent_at' => now(),
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

        $this->assertNotNull(OtpVerification::where('phone', '0501234567')->value('verified_at'));
    }

    public function test_registration_completion_requires_sms_verification(): void
    {
        $this->postJson('/register/complete', $this->validRegistrationData())
            ->assertForbidden()
            ->assertJson(['success' => false]);
    }

    public function test_api_otp_verification_marks_phone_verified_for_registration(): void
    {
        config(['services.oursms.test_phones' => '0501234567', 'services.oursms.test_code' => '123456']);

        OtpVerification::create([
            'phone' => '0501234567',
            'otp' => '123456',
            'type' => 'registration',
            'status' => 'sent',
            'expires_at' => now()->addMinutes(5),
            'created_at' => now()->subHours(2),
        ]);

        $this->postJson('/api/v1/auth/verify-otp', [
            'phone' => '+966501234567',
            'otp' => '123456',
        ])->assertOk()->assertJsonPath('data.verified', true)
            ->assertJsonPath('data.phone', '0501234567');

        $this->assertNotNull(OtpVerification::where('phone', '0501234567')->value('verified_at'));
        $this->assertTrue(app(OtpService::class)->isVerified('0501234567', 'registration'));
        $this->assertFalse(app(OtpService::class)->isVerified('0501234567', 'password_reset'));

        $this->travel(61)->minutes();
        $this->assertFalse(app(OtpService::class)->isVerified('0501234567', 'registration'));
        $this->travelBack();
    }

    public function test_fixed_registration_code_verifies_any_phone_without_sending_otp(): void
    {
        config(['services.oursms.test_phones' => '', 'services.oursms.test_national_ids' => '']);
        OtpVerification::where('phone', '0501234567')->delete();

        foreach (['/api/v1/auth/register/otp/verify', '/api/v1/auth/verify-otp'] as $endpoint) {
            $this->postJson($endpoint, ['phone' => '0501234567', 'otp' => '123456'])
                ->assertOk()->assertJsonPath('data.verified', true);
            $this->assertTrue(app(OtpService::class)->isVerified('0501234567', 'registration'));
        }

        $this->assertFalse(app(OtpService::class)->verify('0501234567', '123456', 'password_reset'));
        $this->assertFalse(app(OtpService::class)->verify('0501234567', '654321', 'registration'));
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
