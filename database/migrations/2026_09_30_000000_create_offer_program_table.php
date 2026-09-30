<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offer_program', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('program_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['offer_id', 'program_id']);
        });

        // Move existing single-program offers into the pivot
        $now = now();
        DB::table('offers')->whereNotNull('program_id')->orderBy('id')->each(function ($offer) use ($now) {
            DB::table('offer_program')->insert([
                'offer_id'   => $offer->id,
                'program_id' => $offer->program_id,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offer_program');
    }
};
