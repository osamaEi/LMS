<?php

namespace Tests\Feature\Auth;

use App\Models\OtpVerification;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TestNationalIdOtpBypassTest extends TestCase
{
    private string $nationalId = '1234567891';
    private string $phone = '0598765432';
    private string $email = 'bypass-by-nid@example.com';

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.oursms.base_url' => 'https://api.oursms.com',
            'services.oursms.api_key' => 'test-key',
            'services.oursms.sender_id' => 'Academy',
            'services.oursms.test_phones' => '',
            'services.oursms.test_national_ids' => $this->nationalId,
            'services.oursms.test_code' => '123456',
        ]);

        $this->cleanUp();

        User::create([
            'name' => 'Bypass Tester',
            'email' => $this->email,
            'phone' => $this->phone,
            'national_id' => $this->nationalId,
            'password' => Hash::make('OldPassw0rd!'),
            'role' => 'student',
            'status' => 'active',
        ]);
    }

    protected function tearDown(): void
    {
        $this->cleanUp();
        parent::tearDown();
    }

    private function cleanUp(): void
    {
        User::withTrashed()->where('national_id', $this->nationalId)
            ->orWhere('email', $this->email)
            ->get()->each->forceDelete();

        OtpVerification::where('phone', $this->phone)->delete();
    }

    public function test_forgot_password_for_test_national_id_never_calls_the_provider(): void
    {
        Http::fake(['api.oursms.com/*' => Http::response(['jobId' => 'job-1'])]);

        $this->postJson('/api/v1/auth/forgot-password', ['phone' => $this->phone])
            ->assertOk()
            ->assertJson(['success' => true]);

        Http::assertNothingSent();

        $this->assertDatabaseHas('otp_verifications', [
            'phone' => $this->phone,
            'type' => 'password_reset',
            'otp' => '123456',
            'provider_reference' => 'test-bypass',
        ]);
    }

    public function test_password_can_be_reset_without_otp_or_confirmation(): void
    {
        $user = User::where('national_id', $this->nationalId)->firstOrFail();
        $user->createToken('existing-session');

        $this->postJson('/api/v1/auth/reset-password', [
            'national_id' => $this->nationalId,
            'password' => 'BrandNewPass!23',
        ])->assertOk()->assertJson(['success' => true]);

        $this->assertTrue(Hash::check('BrandNewPass!23', $user->fresh()->password));
        $this->assertSame(0, $user->tokens()->count());
    }

    public function test_reset_password_requires_national_id_instead_of_phone(): void
    {
        $this->postJson('/api/v1/auth/reset-password', [
            'phone' => $this->phone,
            'otp' => '123456',
            'password' => 'BrandNewPass!23',
        ])->assertUnprocessable()->assertJsonValidationErrors('national_id');
    }

    public function test_a_normal_account_still_uses_the_real_provider(): void
    {
        config(['services.oursms.test_national_ids' => '']);
        Http::fake(['api.oursms.com/*' => Http::response(['jobId' => 'job-1'])]);

        $this->postJson('/api/v1/auth/forgot-password', ['phone' => $this->phone])
            ->assertOk();

        Http::assertSent(fn ($request) => $request['dests'] === ['966598765432']);
    }

    public function test_forgot_password_normalizes_phone_without_leading_zero(): void
    {
        Http::fake();
        $this->postJson('/api/v1/auth/forgot-password', ['phone' => substr($this->phone, 1)])
            ->assertOk()->assertJsonPath('data.phone', '059*****32');
        $this->assertDatabaseHas('otp_verifications', [
            'phone' => $this->phone,
            'type' => 'password_reset',
        ]);
    }

    public function test_forgot_password_requires_a_valid_phone(): void
    {
        Http::fake();
        foreach ([[], ['national_id' => $this->nationalId], ['email' => $this->email], ['phone' => '12345']] as $payload) {
            $this->postJson('/api/v1/auth/forgot-password', $payload)
                ->assertUnprocessable()->assertJsonValidationErrors('phone');
        }
        Http::assertNothingSent();
    }

    public function test_forgot_password_uses_phone_when_other_identifiers_are_supplied(): void
    {
        Http::fake();
        $this->postJson('/api/v1/auth/forgot-password', [
            'phone' => $this->phone,
            'national_id' => '9999999999',
            'email' => 'another@example.com',
        ])->assertOk()->assertJsonPath('data.phone', '059*****32');
    }

    public function test_wrong_code_is_still_rejected_for_a_test_account(): void
    {
        $this->assertFalse(
            app(OtpService::class)->verify($this->phone, '999999', 'password_reset')
        );
    }
}
