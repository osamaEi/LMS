<?php

namespace Tests\Feature\Teacher;

use App\Models\Session;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ZoomLinkTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['database.default' => 'zoom_link_test', 'database.connections.zoom_link_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        DB::purge('zoom_link_test');

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('role');
            $table->string('status');
            $table->string('zoom_join_url', 500)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        User::create([
            'name' => 'Teacher', 'role' => 'teacher', 'status' => 'active',
            'zoom_join_url' => 'https://zoom.us/j/123456789',
        ]);

        (require database_path('migrations/2026_10_12_000001_expand_users_zoom_join_url.php'))->up();
    }

    public function test_long_link_is_saved_exactly_and_used_by_sessions(): void
    {
        $teacher = User::firstOrFail();
        $this->assertSame('https://zoom.us/j/123456789', $teacher->zoom_join_url);
        $url = 'https://zoom.us/j/123456789?pwd=';
        $url .= str_repeat('a', 8192 - strlen($url));

        $this->actingAs($teacher)->from('/teacher/dashboard')
            ->patch(route('teacher.zoom-link.update'), ['zoom_join_url' => $url])
            ->assertRedirect('/teacher/dashboard')
            ->assertSessionHasNoErrors();

        $this->assertSame($url, $teacher->fresh()->zoom_join_url);
        $session = new Session;
        $session->setRelation('teacher', $teacher->fresh());
        $this->assertSame($url, $session->zoom_join_url);
    }

    public function test_empty_link_clears_saved_link(): void
    {
        $teacher = User::firstOrFail();

        $this->actingAs($teacher)
            ->patch(route('teacher.zoom-link.update'), ['zoom_join_url' => ''])
            ->assertSessionHasNoErrors();

        $this->assertNull($teacher->fresh()->zoom_join_url);
    }

    public function test_oversized_link_is_rejected_without_changing_saved_link(): void
    {
        $teacher = User::firstOrFail();

        $this->actingAs($teacher)
            ->patch(route('teacher.zoom-link.update'), ['zoom_join_url' => str_repeat('a', 8193)])
            ->assertSessionHasErrors('zoom_join_url');

        $this->assertSame('https://zoom.us/j/123456789', $teacher->fresh()->zoom_join_url);
    }
}
