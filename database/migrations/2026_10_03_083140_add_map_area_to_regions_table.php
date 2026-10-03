<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('regions', function (Blueprint $table) {
            $table->string('map_area', 30)->nullable()->after('map_y');
        });

        $areas = [
            'north_gaza' => [[72, 8], [82, 16]],
            'gaza_city' => [[62, 24], [64, 30]],
            'middle_area' => [[50, 48], [44, 47]],
            'khan_younis' => [[38, 70], [30, 68]],
            'rafah' => [[26, 90], [17, 82]],
        ];

        foreach ($areas as $area => [[$oldX, $oldY], [$x, $y]]) {
            DB::table('regions')->where('key', $area)->update(['map_area' => $area]);
            DB::table('regions')->where('key', $area)->where('map_x', $oldX)->where('map_y', $oldY)->update(['map_x' => $x, 'map_y' => $y]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('regions', function (Blueprint $table) {
            $table->dropColumn('map_area');
        });
    }
};
