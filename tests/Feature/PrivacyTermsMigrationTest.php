<?php

namespace Tests\Feature;

use App\Models\Page;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PrivacyTermsMigrationTest extends TestCase
{
    public function test_migration_updates_and_creates_pages_with_non_nullable_english_content(): void
    {
        config(['database.default' => 'policy_test', 'database.connections.policy_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        DB::purge('policy_test');
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            foreach (['title_ar', 'title_en', 'content_ar', 'content_en', 'category'] as $column) {
                $table->text($column);
            }
            $table->boolean('is_published');
            $table->integer('version');
            $table->timestamp('published_at');
            $table->timestamps();
        });
        Page::create([
            'slug' => 'privacy-policy', 'title_ar' => 'Old', 'title_en' => 'Old',
            'content_ar' => 'Old', 'content_en' => 'Old translation', 'category' => 'legal',
            'is_published' => true, 'version' => 1, 'published_at' => now(),
        ]);

        (require database_path('migrations/2026_09_21_000003_update_privacy_and_terms_pages.php'))->up();

        $this->assertSame(2, Page::count());
        $privacy = Page::where('slug', 'privacy-policy')->firstOrFail();
        $this->assertEquals(2, $privacy->version);
        $this->assertSame('', $privacy->content_en);
        $this->assertStringContainsString('سبتمبر 2026', $privacy->content_ar);
        app()->setLocale('en');
        $this->assertSame($privacy->content_ar, $privacy->content);
        $terms = Page::where('slug', 'terms')->firstOrFail();
        $this->assertEquals(1, $terms->version);
        $this->assertSame('', $terms->content_en);
        $this->assertSame($terms->content_ar, $terms->content);
    }
}
