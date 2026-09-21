<?php

namespace App\Services;

use App\Models\ConsentRecord;
use App\Models\Page;
use App\Models\Program;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConsentService
{
    public const VERSION = '2026-09-21';
    public const BASIC = 'أقر وأوافق على شروط وأحكام الاستخدام وسياسة الخصوصية الخاصة بـ أكاديمية الارتقاء للتدريب العالي، وأوافق على معالجة بياناتي الشخصية لأغراض تقديم الخدمات التدريبية وإصدار الشهادات.';
    public const MARKETING = 'أوافق على استقبال الرسائل الترويجية والعروض الخاصة بالدورات والبرامج القادمة عبر البريد الإلكتروني أو الرسائل النصية (SMS).';
    public const CERTIFICATE = 'أقر بصحة البيانات المدخلة (الاسم ورقم الهوية)، وأوافق على مشاركة البيانات اللازمة مع الجهات المعتمدة لأغراض توثيق وإصدار الشهادة التدريبية.';

    public function register(Request $request, array $attributes): User
    {
        $request->validate([
            'is_terms' => 'required|accepted',
            'marketing_consent' => 'sometimes|boolean',
        ]);
        return DB::transaction(function () use ($request, $attributes) {
            $user = User::create($attributes);
            $policies = Page::whereIn('slug', ['terms', 'privacy-policy'])
                ->where('is_published', true)
                ->get(['slug', 'version', 'title_ar', 'content_ar', 'title_en', 'content_en'])
                ->toArray();

            $this->record($request, $user, 'basic', true, self::BASIC, ['policies' => $policies]);
            $this->record($request, $user, 'marketing', $request->boolean('marketing_consent'), self::MARKETING);

            return $user;
        });
    }

    public function enroll(Request $request, User $user, Program $program): void
    {
        $request->validate(['certificate_consent' => 'required|accepted'], [
            'certificate_consent.accepted' => 'يجب الموافقة على مشاركة البيانات اللازمة لإصدار الشهادة.',
            'certificate_consent.required' => 'يجب الموافقة على مشاركة البيانات اللازمة لإصدار الشهادة.',
        ]);

        DB::transaction(function () use ($request, $user, $program) {
            $user = User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            if ($user->program_id) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'program_id' => 'أنت مسجل بالفعل في برنامج دراسي',
                ]);
            }
            $this->recordCertificate($request, $user, $program);
            $user->update([
                'program_id' => $program->id,
                'program_status' => 'pending',
                'current_term_number' => 1,
            ]);
        });
    }

    public function hasCertificateConsent(User $user, Program $program): bool
    {
        return $user->consentRecords()->where('program_id', $program->id)
            ->where('type', 'certificate')->where('accepted', true)
            ->where('statement_version', self::VERSION)->get()
            ->contains(fn ($record) => ($record->context['name'] ?? null) === $user->name
                && ($record->context['national_id'] ?? null) === $user->national_id);
    }

    public function ensurePaymentConsent(Request $request, \App\Models\Payment $payment): void
    {
        $program = $payment->program;
        if (!$program || $this->hasCertificateConsent($request->user(), $program)) {
            return;
        }
        $request->validate(['certificate_consent' => 'required|accepted'], [
            'certificate_consent.required' => 'يجب الموافقة على مشاركة البيانات اللازمة لإصدار الشهادة.',
            'certificate_consent.accepted' => 'يجب الموافقة على مشاركة البيانات اللازمة لإصدار الشهادة.',
        ]);
        $this->recordCertificate($request, $request->user(), $program);
    }

    private function recordCertificate(Request $request, User $user, Program $program): void
    {
        $this->record($request, $user, 'certificate', true, self::CERTIFICATE, [
            'program_name' => $program->name,
            'name' => $user->name,
            'national_id' => $user->national_id,
        ], $program);
    }

    public function record(Request $request, User $user, string $type, bool $accepted, string $statement, array $context = [], ?Program $program = null): ConsentRecord
    {
        return ConsentRecord::create([
            'user_id' => $user->id,
            'program_id' => $program?->id,
            'type' => $type,
            'accepted' => $accepted,
            'statement' => $statement,
            'statement_version' => self::VERSION,
            'context' => $context,
            'ip_address' => $request->ip(),
            'recorded_at' => now(),
        ]);
    }
}
