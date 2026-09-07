<?php

namespace Tests\Feature\Student;

use App\Models\StudentDocument;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileDocumentUploadTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'document_test', 'database.connections.document_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        DB::purge('document_test');
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->softDeletes();
        });
        Schema::create('student_documents', function (Blueprint $table) {
            $table->id();
            foreach ((new StudentDocument)->getFillable() as $column) {
                $table->string($column)->nullable();
            }
            $table->timestamps();
            $table->softDeletes();
        });
        DB::table('users')->insert(['id' => 1]);
        Storage::fake('public');
    }

    private function student(): User
    {
        $user = new User;
        $user->forceFill(['id' => 1, 'role' => 'student', 'status' => 'active']);
        return $user;
    }

    public function test_student_can_upload_each_document_for_their_own_account(): void
    {
        foreach (['national_id_front', 'national_id_back', 'certificate'] as $type) {
            $this->actingAs($this->student())->post(route('student.profile.documents.upload'), [
                'document_type' => $type,
                'user_id' => 99,
                'document' => UploadedFile::fake()->create('document.pdf', 20, 'application/pdf'),
            ])->assertRedirect(route('student.profile'))->assertSessionHas('document_success');

            $document = StudentDocument::where('document_type', $type)->firstOrFail();
            $this->assertEquals(1, $document->user_id);
            $this->assertSame('pending', $document->status);
            Storage::disk('public')->assertExists($document->file_path);
        }
        $this->assertSame(3, StudentDocument::count());
    }

    public function test_replacement_resets_review_and_removes_previous_file(): void
    {
        Storage::disk('public')->put('old.pdf', 'old');
        $document = StudentDocument::create(['user_id' => 1, 'document_type' => 'certificate',
            'file_path' => 'old.pdf', 'status' => 'approved', 'reviewed_by' => 9,
            'reviewed_at' => now(), 'rejection_reason' => 'previous review']);
        $this->actingAs($this->student())->post(route('student.profile.documents.upload'), [
            'document_type' => 'certificate',
            'document' => UploadedFile::fake()->image('certificate.png'),
        ])->assertRedirect(route('student.profile'));

        $document->refresh();
        $this->assertSame(1, StudentDocument::count());
        $this->assertSame('pending', $document->status);
        $this->assertNull($document->reviewed_by);
        $this->assertNull($document->reviewed_at);
        $this->assertNull($document->rejection_reason);
        Storage::disk('public')->assertMissing('old.pdf');
        Storage::disk('public')->assertExists($document->file_path);
    }

    public function test_invalid_uploads_are_rejected_without_storing_files(): void
    {
        foreach ([
            ['document_type' => 'other', 'document' => UploadedFile::fake()->image('id.jpg')],
            ['document_type' => 'certificate', 'document' => UploadedFile::fake()->create('file.txt', 1, 'text/plain')],
            ['document_type' => 'certificate', 'document' => UploadedFile::fake()->create('large.pdf', 5121, 'application/pdf')],
            ['document_type' => 'certificate'],
        ] as $data) {
            $this->actingAs($this->student())->postJson(route('student.profile.documents.upload'), $data)
                ->assertUnprocessable();
        }
        $this->assertSame(0, StudentDocument::count());
        $this->assertSame([], Storage::disk('public')->allFiles());
    }

    public function test_guests_cannot_upload_documents(): void
    {
        $this->postJson(route('student.profile.documents.upload'), [])->assertUnauthorized();
    }
}
