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
        if (Schema::hasColumn('assets', 'terminl_number') && ! Schema::hasColumn('assets', 'terminal_number')) {
            Schema::table('assets', function (Blueprint $table) {
                $table->renameColumn('terminl_number', 'terminal_number');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('assets', 'terminal_number') && ! Schema::hasColumn('assets', 'terminl_number')) {
            Schema::table('assets', function (Blueprint $table) {
                $table->renameColumn('terminal_number', 'terminl_number');
            });
        }
    }
};
