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
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('origin_region_id')->nullable()->after('country_id');
            $table->unsignedBigInteger('origin_city_id')->nullable()->after('origin_region_id');
            $table->string('origin_address')->nullable()->after('origin_city_id');
            $table->string('origin_contact_name')->nullable()->after('origin_address');
            $table->string('origin_contact_phone')->nullable()->after('origin_contact_name');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'origin_region_id',
                'origin_city_id',
                'origin_address',
                'origin_contact_name',
                'origin_contact_phone',
            ]);
        });
    }
};
