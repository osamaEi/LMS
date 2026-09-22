<?php

namespace Tests\Feature\Auth;

use App\Models\ConsentRecord;
use App\Models\Page;
use App\Models\Program;
use App\Models\User;
use App\Services\ConsentService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ConsentRecordingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'consent_test', 'database.connections.consent_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '', 'foreign_key_constraints' => true,
        ]]);
        DB::purge('consent_test');
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('national_id')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('student_code')->nullable();
            $table->string('role')->default('student');
            $table->string('status')->default('active');
            $table->unsignedBigInteger('program_id')->nullable();
            $table->string('program_status')->nullable();
            $table->integer('current_term_number')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('status')->default('active');
            $table->decimal('price')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug');
            $table->integer('version');
            $table->boolean('is_published')->default(true);
            foreach (['title_ar', 'title_en', 'content_ar', 'content_en'] as $column) {
                $table->text($column)->nullable();
            }
            $table->timestamps();
        });
        (require database_path('migrations/2026_09_21_000002_create_consent_records_table.php'))->up();
        Page::create(['slug' => 'terms', 'version' => 1, 'title_ar' => 'الشروط', 'content_ar' => 'Original terms', 'is_published' => true]);
    }

    private function request(array $input): Request
    {
        return Request::create('/register/complete', 'POST', $input, [], [], ['REMOTE_ADDR' => '2001:db8::7']);
    }

    private function attributes(): array
    {
        return ['name' => 'Student', 'email' => 'consent@example.test', 'national_id' => '1234567890', 'student_code' => 'CONSENT-1', 'role' => 'student'];
    }

    public function test_registration_saves_declined_marketing_and_frozen_policy_with_server_ip(): void
    {
        $user = app(ConsentService::class)->register($this->request(['is_terms' => 1, 'ip_address' => '192.0.2.99']), $this->attributes());
        $basic = $user->consentRecords()->where('type', 'basic')->firstOrFail();
        $this->assertTrue($basic->accepted);
        $this->assertSame(ConsentService::BASIC, $basic->statement);
        $this->assertSame('2001:db8::7', $basic->ip_address);
        $this->assertNotNull($basic->recorded_at);
        $this->assertFalse($user->consentRecords()->where('type', 'marketing')->firstOrFail()->accepted);
        Page::query()->update(['content_ar' => 'New terms', 'version' => 2]);
        $this->assertSame('Original terms', $basic->fresh()->context['policies'][0]['content_ar']);
        $this->assertSame(1, $basic->fresh()->context['policies'][0]['version']);
    }

    public function test_explicit_marketing_choice_is_recorded(): void
    {
        $user = app(ConsentService::class)->register($this->request(['is_terms' => 1, 'marketing_consent' => 1]), $this->attributes());
        $this->assertTrue($user->consentRecords()->where('type', 'marketing')->firstOrFail()->accepted);
    }

    public function test_registration_persists_server_verified_phone_timestamp(): void
    {
        $verifiedAt = now()->startOfSecond();
        $user = app(ConsentService::class)->register(
            $this->request(['is_terms' => 1, 'phone_verified_at' => '2000-01-01']),
            array_merge($this->attributes(), ['phone_verified_at' => $verifiedAt]),
        );
        $this->assertTrue($user->fresh()->phone_verified_at->equalTo($verifiedAt));
        $this->assertNull($user->fresh()->email_verified_at);
    }

    public function test_basic_consent_cannot_be_omitted(): void
    {
        try {
            app(ConsentService::class)->register($this->request(['marketing_consent' => 1]), $this->attributes());
            $this->fail('Registration must reject absent basic consent');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('is_terms', $e->errors());
            $this->assertSame(0, User::count());
            $this->assertSame(0, ConsentRecord::count());
        }
    }

    public function test_registration_rolls_back_when_consent_storage_fails(): void
    {
        Schema::drop('consent_records');
        try {
            app(ConsentService::class)->register($this->request(['is_terms' => 1]), $this->attributes());
            $this->fail('Expected database failure');
        } catch (\Illuminate\Database\QueryException $e) {
            $this->assertSame(0, User::count());
        }
    }

    public function test_web_and_api_enrollment_require_certificate_consent(): void
    {
        $user = User::create($this->attributes());
        $program = Program::create(['name_ar' => 'دورة']);
        $this->withoutMiddleware();
        foreach (['/student/enroll-program', '/api/v1/student/enroll-program'] as $url) {
            $this->actingAs($user)->postJson($url, ['program_id' => $program->id])
                ->assertUnprocessable()->assertJsonValidationErrors('certificate_consent');
            $this->assertNull($user->fresh()->program_id);
        }
        $this->assertSame(0, ConsentRecord::count());
    }

    public function test_certificate_consent_is_scoped_to_program_and_identity(): void
    {
        $user = User::create($this->attributes());
        $program = Program::create(['name_ar' => 'دورة']);
        $other = Program::create(['name_ar' => 'دورة أخرى']);
        $service = app(ConsentService::class);
        $service->enroll($this->request(['certificate_consent' => 1]), $user, $program);
        $this->assertSame($program->id, $user->fresh()->program_id);
        $this->assertTrue($service->hasCertificateConsent($user, $program));
        $this->assertFalse($service->hasCertificateConsent($user, $other));
        $user->update(['name' => 'Changed name']);
        $this->assertFalse($service->hasCertificateConsent($user, $program));
        $record = ConsentRecord::where('type', 'certificate')->firstOrFail();
        $this->assertSame('Student', $record->context['name']);
        $this->assertSame(ConsentService::CERTIFICATE, $record->statement);
    }

    public function test_api_enrollment_saves_acceptance_and_program_together(): void
    {
        $user = User::create($this->attributes());
        $program = Program::create(['name_ar' => 'دورة']);
        $this->withoutMiddleware()->actingAs($user)
            ->withServerVariables(['REMOTE_ADDR' => '192.0.2.17'])
            ->postJson('/api/v1/student/enroll-program', [
                'program_id' => $program->id, 'certificate_consent' => true,
            ])->assertOk()->assertJsonPath('success', true);
        $this->assertSame($program->id, $user->fresh()->program_id);
        $this->assertDatabaseHas('consent_records', [
            'user_id' => $user->id, 'program_id' => $program->id,
            'type' => 'certificate', 'accepted' => true, 'ip_address' => '192.0.2.17',
        ]);
    }

    public function test_payment_requires_acceptance_for_admin_assigned_program_and_reuses_it(): void
    {
        $user = User::create($this->attributes());
        $program = Program::create(['name_ar' => 'دورة']);
        $payment = new \App\Models\Payment(['user_id' => $user->id, 'program_id' => $program->id]);
        $payment->setRelation('program', $program);
        $service = app(ConsentService::class);
        $request = $this->request([]);
        $request->setUserResolver(fn () => $user);
        try {
            $service->ensurePaymentConsent($request, $payment);
            $this->fail('Payment requires certificate consent');
        } catch (ValidationException $e) {
            $this->assertArrayHasKey('certificate_consent', $e->errors());
            $this->assertSame(0, ConsentRecord::count());
        }
        $request->merge(['certificate_consent' => 1]);
        $service->ensurePaymentConsent($request, $payment);
        $this->assertSame(1, ConsentRecord::count());
        $request->request->remove('certificate_consent');
        $service->ensurePaymentConsent($request, $payment);
        $this->assertSame(1, ConsentRecord::count());
    }

    public function test_metadata_keeps_marketing_optional_and_all_defaults_unchecked(): void
    {
        $this->getJson('/api/v1/consents')->assertOk()
            ->assertJsonPath('data.registration.0.required', true)
            ->assertJsonPath('data.registration.0.default', false)
            ->assertJsonPath('data.registration.1.required', false)
            ->assertJsonPath('data.registration.1.default', false)
            ->assertJsonPath('data.enrollment.required', true)
            ->assertJsonPath('data.enrollment.default', false);
    }
}
