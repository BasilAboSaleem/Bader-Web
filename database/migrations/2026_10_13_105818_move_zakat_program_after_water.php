<?php

use App\Support\PublicNavigation;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Show the Zakat program second, right after Water, keeping every other program in its current order.
     */
    public function up(): void
    {
        $keys = DB::table('programs')->orderBy('order')->orderBy('id')->pluck('key')->all();

        if (! in_array('zakat', $keys, true) || ! in_array('water', $keys, true)) {
            return;
        }

        $keys = array_values(array_diff($keys, ['zakat']));
        array_splice($keys, array_search('water', $keys, true) + 1, 0, ['zakat']);

        foreach ($keys as $position => $key) {
            DB::table('programs')->where('key', $key)->update(['order' => $position + 1]);
        }

        Cache::forget(PublicNavigation::MENU_CACHE_KEY);
    }

    /**
     * The previous order is not restored; the team can reorder programs from the dashboard.
     */
    public function down(): void
    {
        //
    }
};
