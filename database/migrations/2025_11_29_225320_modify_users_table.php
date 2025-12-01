<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->unique()->nullable()->after('email');
            $table->string('national_id', 20)->unique()->nullable()->after('phone');
            $table->enum('role', ['student', 'teacher', 'admin', 'super_admin'])->default('student')->after('national_id');
            $table->enum('status', ['pending', 'active', 'suspended', 'rejected'])->default('pending')->after('role');
            $table->timestamp('phone_verified_at')->nullable()->after('email_verified_at');
            $table->timestamp('nafath_verified_at')->nullable()->after('phone_verified_at');
            $table->string('nafath_transaction_id')->nullable()->after('nafath_verified_at');
            $table->string('profile_photo')->nullable()->after('nafath_transaction_id');
            $table->text('bio')->nullable()->after('profile_photo');
            $table->softDeletes();

            // Add indexes for performance
            $table->index('phone');
            $table->index('national_id');
            $table->index('role');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['phone']);
            $table->dropIndex(['national_id']);
            $table->dropIndex(['role']);
            $table->dropIndex(['status']);

            $table->dropSoftDeletes();
            $table->dropColumn([
                'phone',
                'national_id',
                'role',
                'status',
                'phone_verified_at',
                'nafath_verified_at',
                'nafath_transaction_id',
                'profile_photo',
                'bio',
            ]);
        });
    }
};
