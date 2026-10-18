<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\StudentController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class StudentDirectoryFiltersTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'directory', 'database.connections.directory' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        DB::purge('directory');
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            foreach (['name', 'role', 'status', 'email', 'phone', 'national_id', 'student_code'] as $column) {
                $table->string($column)->nullable();
            }
            $table->integer('program_id')->nullable();
            $table->integer('class_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->softDeletes();
        });
        Schema::create('program_classes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('program_id');
            $table->softDeletes();
        });
        Schema::create('student_programs', function (Blueprint $table) {
            $table->integer('student_id');
            $table->integer('program_id');
            $table->integer('class_id');
        });
        DB::table('programs')->insert(['id' => 1, 'name_ar' => 'برنامج']);
        DB::table('program_classes')->insert(['id' => 1, 'program_id' => 1, 'name' => 'كلاس']);
        foreach ([1, 2, 3, 4] as $id) {
            DB::table('users')->insert([
                'id' => $id, 'name' => 'نورة '.$id, 'role' => $id === 4 ? 'teacher' : 'student',
                'status' => $id === 3 ? 'suspended' : 'active', 'student_code' => 'STU-00'.$id,
                'created_at' => '2026-10-18 12:00:00',
                'program_id' => $id === 1 ? 1 : null, 'class_id' => $id === 1 ? 1 : null,
            ]);
        }
        DB::table('student_programs')->insert(['student_id' => 2, 'program_id' => 1, 'class_id' => 1]);
    }

    private function results(array $filters): array
    {
        return (new StudentController)->index(Request::create('/admin/students', 'GET', $filters))->getData();
    }

    public function test_combined_filters_include_legacy_and_pivot_membership_and_arabic_search(): void
    {
        $data = $this->results([
            'search' => 'نورة', 'status' => 'active', 'program_id' => 1, 'class_id' => 1,
            'registered_from' => '2026-10-18', 'registered_to' => '2026-10-18',
            'sort' => 'name', 'per_page' => 30,
        ]);
        $this->assertSame([1, 2], $data['students']->pluck('id')->all());
        $this->assertSame(30, $data['students']->perPage());
        $this->assertSame(0, $data['stats']['inactive']);
        $this->assertSame(1, $data['stats']['suspended']);
        $this->assertSame(0, $this->results(['registered_from' => '2026-10-19'])['students']->total());
    }

    public function test_student_code_search_and_suspended_filter(): void
    {
        $this->assertSame([2], $this->results(['search' => 'STU-002'])['students']->pluck('id')->all());
        $this->assertSame([3], $this->results(['status' => 'suspended'])['students']->pluck('id')->all());
    }

    public function test_invalid_date_range_is_rejected(): void
    {
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $this->results(['registered_from' => '2026-10-19', 'registered_to' => '2026-10-18']);
    }
}
