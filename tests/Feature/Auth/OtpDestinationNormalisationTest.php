<?php

namespace Tests\Feature\Auth;

use App\Models\OtpVerification;
use App\Services\OtpService;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Tests\TestCase;

class OtpDestinationNormalisationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.oursms.base_url' => 'https://api.oursms.com',
            'services.oursms.api_key' => 'test-key',
            'services.oursms.sender_id' => 'Academy',
            'services.oursms.test_phones' => '',
        ]);
    }

    protected function tearDown(): void
    {
        OtpVerification::whereIn('phone', [
            '0512345678', '512345678', '+966512345678', '00966512345678', '0412345678',
        ])->delete();

        parent::tearDown();
    }

    public static function phoneFormats(): array
    {
        return [
            'local trunk zero' => ['0512345678'],
            'bare local' => ['512345678'],
            'plus country code' => ['+966512345678'],
            'international access prefix' => ['00966512345678'],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('phoneFormats')]
    public function test_every_accepted_format_reaches_oursms_as_one_msisdn(string $phone): void
    {
        Http::fake(['api.oursms.com/*' => Http::response(['jobId' => 'job-1'])]);

        app(OtpService::class)->send($phone, 'password_reset');

        Http::assertSent(fn ($request) => $request['dests'] === ['966512345678']);
    }

    public function test_unroutable_number_is_rejected_before_calling_the_provider(): void
    {
        Http::fake(['api.oursms.com/*' => Http::response(['jobId' => 'job-1'])]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid Saudi mobile number');

        try {
            app(OtpService::class)->send('0412345678', 'password_reset');
        } finally {
            Http::assertNothingSent();
        }
    }

    public function test_failed_send_is_recorded_on_the_otp_row(): void
    {
        Http::fake(['api.oursms.com/*' => Http::response(['jobId' => 'job-1'])]);

        try {
            app(OtpService::class)->send('0412345678', 'password_reset');
        } catch (RuntimeException) {
            // expected
        }

        $this->assertDatabaseHas('otp_verifications', [
            'phone' => '0412345678',
            'status' => 'failed',
        ]);
    }
}
