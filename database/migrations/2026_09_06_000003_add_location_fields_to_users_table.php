<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('full_address')->nullable()->after('phone');
            $table->string('city')->nullable()->after('full_address');
            $table->string('commune')->nullable()->after('city');
            $table->string('quartier')->nullable()->after('commune');
            $table->string('cell')->nullable()->after('quartier');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['full_address', 'city', 'commune', 'quartier', 'cell']);
        });
    }
};
