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
        Schema::create('asset_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classroom_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->unique();
            $table->timestamps();
        });

        Schema::table('assets', function (Blueprint $table) {
            $table->foreignId('asset_group_id')
                ->nullable()
                ->after('building_id')
                ->constrained('asset_groups')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropConstrainedForeignId('asset_group_id');
        });

        Schema::dropIfExists('asset_groups');
    }
};
