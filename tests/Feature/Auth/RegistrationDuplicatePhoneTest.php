<?php

namespace Tests\Feature\Auth;

use App\Services\OtpService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class RegistrationDuplicatePhoneTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        $this->withoutMiddleware();
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('phone')->unique();
            $table->string('national_id');
            $table->string('email')->nullable();
            $table->softDeletes();
        });
        $this->mock(OtpService::class)->shouldNotReceive('send');
    }

    public function test_deleted_accounts_block_otp_for_both_phone_formats(): void
    {
        foreach (['0506464261', '506464261'] as $storedPhone) {
            DB::table('users')->delete();
            DB::table('users')->insert([
                'phone' => $storedPhone,
                'national_id' => '1111111111',
                'deleted_at' => now(),
            ]);
            foreach (['/register/otp/send', '/api/v1/auth/register/otp/send'] as $url) {
                $this->postJson($url, ['phone' => '0506464261', 'national_id' => '2222222222'])
                    ->assertStatus(422)->assertJson(['success' => false]);
            }
        }
    }

    public function test_deleted_account_blocks_final_web_submission(): void
    {
        DB::table('users')->insert([
            'phone' => '0506464261', 'national_id' => '1111111111', 'deleted_at' => now(),
        ]);
        $this->withSession([
            'register_phone' => '0506464261',
            'register_national_id' => '2222222222',
            'register_sms_verified' => true,
        ])->postJson('/register/complete', [
            'phone' => '0506464261', 'national_id' => '2222222222',
            'name' => 'Test Student', 'email' => 'duplicate@example.test',
            'password' => 'password123', 'password_confirmation' => 'password123',
            'date_of_birth' => '1995-01-01', 'gender' => 'female',
            'nationality' => 'Palestinian', 'specialization' => 'Support',
            'specialization_type' => 'bachelor', 'date_of_graduation' => '2025-09-24',
            'is_confirm_user' => true, 'is_terms' => true,
        ])->assertStatus(422)->assertJson([
            'success' => false, 'message' => 'رقم الجوال مسجل مسبقًا',
        ]);
        $this->assertSame(1, DB::table('users')->count());
    }
}
