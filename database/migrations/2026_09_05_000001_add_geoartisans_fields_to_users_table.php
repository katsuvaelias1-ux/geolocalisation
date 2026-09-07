<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->nullable()->after('id');
            $table->string('phone', 30)->nullable()->after('email');
            $table->enum('role', ['client', 'artisan', 'admin'])->default('client')->after('phone');
            $table->string('profile_photo')->nullable()->after('role');
            $table->enum('status', ['active', 'suspended'])->default('active')->after('profile_photo');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'phone', 'role', 'profile_photo', 'status']);
        });
    }
};
