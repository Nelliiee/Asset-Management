<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('buildings')) {
            return;
        }

        if (! Schema::hasColumn('buildings', 'code')) {
            Schema::table('buildings', function (Blueprint $table) {
                $table->string('code')->nullable()->after('name');
            });
        }

        $rows = DB::table('buildings')
            ->where(function ($query) {
                $query->whereNull('code')
                    ->orWhere('code', '');
            })
            ->get(['id', 'name']);

        foreach ($rows as $row) {
            $base = Str::slug((string) ($row->name ?: 'building-' . $row->id));
            $code = $base ?: 'building-' . $row->id;
            $suffix = 1;

            while (DB::table('buildings')->where('code', $code)->where('id', '!=', $row->id)->exists()) {
                $code = $base . '-' . $suffix;
                $suffix++;
            }

            DB::table('buildings')
                ->where('id', $row->id)
                ->update(['code' => $code]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('buildings') || ! Schema::hasColumn('buildings', 'code')) {
            return;
        }

        Schema::table('buildings', function (Blueprint $table) {
            $table->dropColumn('code');
        });
    }
};
