<?php

namespace Tests\Feature\Auth;

use App\Models\OtpVerification;
use App\Models\StudentDocument;
use App\Models\User;
use Tests\TestCase;

class ApiRegistrationDocumentStringsTest extends TestCase
{
    private string $phone = '0507654321';
    private string $email = 'api-string-docs@example.com';
    private string $nationalId = '1088776655';

    protected function setUp(): void
    {
        parent::setUp();
        $this->cleanUp();
    }

    protected function tearDown(): void
    {
        $this->cleanUp();
        parent::tearDown();
    }

    private function cleanUp(): void
    {
        User::withTrashed()
            ->where('email', $this->email)
            ->orWhere('national_id', $this->nationalId)
            ->get()
            ->each(function (User $user) {
                StudentDocument::withTrashed()->where('user_id', $user->id)->forceDelete();
                $user->forceDelete();
            });

        OtpVerification::where('phone', $this->phone)->delete();
    }

    private function verifiedOtp(): void
    {
        OtpVerification::create([
            'phone' => $this->phone,
            'otp' => '123456',
            'type' => 'registration',
            'status' => 'sent',
            'sent_at' => now(),
            'expires_at' => now()->addMinutes(5),
            'verified_at' => now(),
        ]);
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'phone' => $this->phone,
            'national_id' => $this->nationalId,
            'name' => 'Test Student',
            'email' => $this->email,
            'password' => 'Passw0rd!23',
            'password_confirmation' => 'Passw0rd!23',
            'date_of_birth' => '2000-01-01',
            'gender' => 'male',
            'nationality' => 'Saudi',
            'specialization' => 'Computer Science',
            'specialization_type' => 'bachelor',
            'date_of_graduation' => '2022-01-01',
            'national_id_front' => 'student-documents/id-front.jpg',
            'national_id_back' => 'https://cdn.example.com/docs/id-back.png?v=2',
            'certificate' => 'student-documents/cert.pdf',
            'is_confirm_user' => 1,
            'is_terms' => 1,
        ], $overrides);
    }

    public function test_documents_are_accepted_as_plain_strings(): void
    {
        $this->verifiedOtp();

        $this->postJson('/api/v1/auth/register', $this->payload())
            ->assertCreated()
            ->assertJson(['success' => true]);

        $user = User::where('email', $this->email)->firstOrFail();

        $this->assertDatabaseHas('student_documents', [
            'user_id' => $user->id,
            'document_type' => 'national_id_front',
            'file_path' => 'student-documents/id-front.jpg',
            'original_name' => 'id-front.jpg',
            'mime_type' => 'image/jpeg',
        ]);

        // A full URL keeps its path but drops the query string from the name.
        $this->assertDatabaseHas('student_documents', [
            'user_id' => $user->id,
            'document_type' => 'national_id_back',
            'file_path' => 'https://cdn.example.com/docs/id-back.png?v=2',
            'original_name' => 'id-back.png',
            'mime_type' => 'image/png',
        ]);

        $this->assertDatabaseHas('student_documents', [
            'user_id' => $user->id,
            'document_type' => 'certificate',
            'file_path' => 'student-documents/cert.pdf',
            'mime_type' => 'application/pdf',
        ]);
    }

    public function test_document_strings_are_still_required(): void
    {
        $this->verifiedOtp();

        $this->postJson('/api/v1/auth/register', $this->payload([
            'national_id_front' => '',
            'certificate' => null,
        ]))
            ->assertStatus(422)
            ->assertJsonValidationErrors(['national_id_front', 'certificate']);
    }
}
