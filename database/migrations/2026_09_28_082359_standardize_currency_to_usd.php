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
        Schema::table('campaigns', function (Blueprint $table) {
            $table->string('currency_ar')->default('دولار أمريكي')->change();
            $table->string('currency_en')->default('USD')->change();
        });

        Schema::table('donations', function (Blueprint $table) {
            $table->string('currency_ar', 50)->default('دولار أمريكي')->change();
            $table->string('currency_en', 50)->default('USD')->change();
        });

        DB::table('campaigns')->update([
            'currency_ar' => 'دولار أمريكي',
            'currency_en' => 'USD',
        ]);

        DB::table('donations')->update([
            'currency_ar' => 'دولار أمريكي',
            'currency_en' => 'USD',
        ]);

        $locations = [
            'hq_location_ar' => 'غزة، فلسطين',
            'hq_location_en' => 'Gaza, Palestine',
            'field_location_ar' => 'غزة، فلسطين',
            'field_location_en' => 'Gaza, Palestine',
            'contact_address_ar' => 'غزة، فلسطين',
            'contact_address_en' => 'Gaza, Palestine',
        ];

        foreach ($locations as $key => $value) {
            $updatedAt = now();
            $setting = DB::table('settings')->where('key', $key);

            if ($setting->exists()) {
                $setting->update(['value' => $value, 'updated_at' => $updatedAt]);
            } else {
                DB::table('settings')->insert([
                    'key' => $key,
                    'value' => $value,
                    'group' => str_starts_with($key, 'contact_') ? 'contact' : 'general',
                    'created_at' => $updatedAt,
                    'updated_at' => $updatedAt,
                ]);
            }
        }

        DB::table('settings')
            ->where('key', 'contact_phone')
            ->where('value', 'like', '+968%')
            ->update(['value' => '', 'updated_at' => now()]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Currency and location values are standardized business data and cannot be safely restored.
    }
};
