<?php

namespace Tests\Feature\Homework;

use App\Models\{ProgramClass, Subject, Homework, Session, SubjectFile, SessionFile};
use App\Services\ClassContentService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema};

class ClassContentTest extends HomeworkTestCase
{
    protected function buildSchema(): void
    {
        config(['database.default' => 'class_content_test', 'database.connections.class_content_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        DB::purge('class_content_test');
        parent::buildSchema();
        Schema::table('terms', fn (Blueprint $t) => $t->unsignedBigInteger('class_id')->nullable());
        Schema::table('homeworks', fn (Blueprint $t) => $t->unsignedBigInteger('class_id')->nullable());
        Schema::create('subject_files', function (Blueprint $t) {
            $t->id(); $t->unsignedBigInteger('subject_id')->nullable();
            $t->unsignedBigInteger('program_id')->nullable(); $t->timestamps();
        });
        Schema::create('session_files', function (Blueprint $t) {
            $t->id(); $t->unsignedBigInteger('session_id'); $t->string('type');
            $t->timestamps(); $t->softDeletes();
        });
    }

    public function test_class_content_includes_shared_resources_but_excludes_other_classes(): void
    {
        $class = ProgramClass::create(['name' => 'A', 'program_id' => 1]);
        $other = ProgramClass::create(['name' => 'B', 'program_id' => 1]);
        $ownSubject = Subject::create(['program_id' => 1, 'class_id' => $class->id]);
        $otherSubject = Subject::create(['program_id' => 1, 'class_id' => $other->id]);
        $term = DB::table('terms')->insertGetId(['class_id' => $class->id, 'program_id' => 1]);
        $sharedSubject = Subject::create(['program_id' => 1]);
        DB::table('term_subject')->insert(['term_id' => $term, 'subject_id' => $sharedSubject->id]);
        $ownFile = SubjectFile::create(['subject_id' => $ownSubject->id]);
        $termFile = SubjectFile::create(['subject_id' => $sharedSubject->id]);
        $globalFile = SubjectFile::create(['program_id' => 1]);
        SubjectFile::create(['subject_id' => $otherSubject->id, 'program_id' => 1]);
        SubjectFile::create(['program_id' => 2]);
        $session = Session::create(['class_id' => $class->id, 'program_id' => 1]);
        $otherSession = Session::create(['class_id' => $other->id, 'subject_id' => $sharedSubject->id]);
        $sessionFile = SessionFile::create(['session_id' => $session->id, 'type' => 'pdf']);
        SessionFile::create(['session_id' => $otherSession->id, 'type' => 'pdf']);
        SessionFile::create(['session_id' => $session->id, 'type' => 'zoom']);
        $ownHomework = Homework::create(['class_id' => $class->id, 'program_id' => 1]);
        $sharedHomework = Homework::create(['subject_id' => $sharedSubject->id]);
        $globalHomework = Homework::create(['program_id' => 1]);
        $sessionHomework = Homework::create(['session_id' => $session->id]);
        Homework::create(['class_id' => $other->id, 'subject_id' => $sharedSubject->id, 'program_id' => 1]);
        Homework::create(['session_id' => $otherSession->id, 'program_id' => 1]);
        Homework::create(['subject_id' => $otherSubject->id, 'program_id' => 1]);
        Homework::create(['program_id' => 2]);

        $service = app(ClassContentService::class);
        $this->assertEqualsCanonicalizing([$ownFile->id, $termFile->id, $globalFile->id], $service->files($class)->pluck('id')->all());
        $this->assertSame([$sessionFile->id], $service->sessionFiles($class)->pluck('id')->all());
        $this->assertEqualsCanonicalizing([$ownHomework->id, $sharedHomework->id, $globalHomework->id, $sessionHomework->id], $service->homeworks($class)->pluck('id')->all());
        $session->delete();
        $this->assertSame(0, $service->sessionFiles($class)->count());
        $this->assertFalse($service->homeworks($class)->whereKey($sessionHomework->id)->exists());
    }
}
