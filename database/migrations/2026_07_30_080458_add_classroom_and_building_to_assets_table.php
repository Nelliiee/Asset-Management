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
        Schema::table('assets', function (Blueprint $table) {
            $table->foreignId('classroom_id')->nullable()->after('status')->constrained('classrooms')->onDelete('cascade');
            $table->foreignId('building_id')->nullable()->after('classroom_id')->constrained('buildings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropConstrainedForeignId('classroom_id');
            $table->dropConstrainedForeignId('building_id');
        });
    }
};
