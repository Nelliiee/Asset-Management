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
        if (! Schema::hasTable('classrooms')) {
            return;
        }

        if (! Schema::hasColumn('classrooms', 'room_number')) {
            Schema::table('classrooms', function (Blueprint $table) {
                $table->string('room_number')->nullable()->after('building_id');
            });
        }

        if (! Schema::hasColumn('classrooms', 'code')) {
            Schema::table('classrooms', function (Blueprint $table) {
                $table->string('code')->nullable()->after('room_number');
            });
        }

        $rows = DB::table('classrooms')
            ->where(function ($query) {
                $query->whereNull('room_number')
                    ->orWhere('room_number', '');
            })
            ->get(['id', 'building_id']);

        foreach ($rows as $row) {
            $roomNumber = 'Room '.($row->id ?? '1');
            $buildingName = DB::table('buildings')->where('id', $row->building_id)->value('name');
            if ($buildingName) {
                $roomNumber = $buildingName.' - '.$roomNumber;
            }

            $code = Str::slug($roomNumber);
            $suffix = 1;

            while (DB::table('classrooms')->where('code', $code)->where('id', '!=', $row->id)->exists()) {
                $code = Str::slug($roomNumber).'-'.$suffix;
                $suffix++;
            }

            DB::table('classrooms')
                ->where('id', $row->id)
                ->update([
                    'room_number' => $roomNumber,
                    'code' => $code,
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('classrooms')) {
            return;
        }

        if (Schema::hasColumn('classrooms', 'code')) {
            Schema::table('classrooms', function (Blueprint $table) {
                $table->dropColumn('code');
            });
        }

        if (Schema::hasColumn('classrooms', 'room_number')) {
            Schema::table('classrooms', function (Blueprint $table) {
                $table->dropColumn('room_number');
            });
        }
    }
};
