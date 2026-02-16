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
        Schema::table('order_item_shipments', function (Blueprint $table) {
            // ===== Origin (погрузка) =====
            $table->unsignedBigInteger('origin_country_id')->nullable()->after('shippable_id');
            $table->unsignedBigInteger('origin_region_id')->nullable()->after('origin_country_id');
            $table->unsignedBigInteger('origin_city_id')->nullable()->after('origin_region_id');
            $table->string('origin_address')->nullable()->after('origin_city_id');
            $table->string('origin_contact_name')->nullable()->after('origin_address');
            $table->string('origin_contact_phone')->nullable()->after('origin_contact_name');

            // ===== Destination (выгрузка) =====
            $table->unsignedBigInteger('destination_country_id')->nullable()->after('origin_contact_phone');
            $table->unsignedBigInteger('destination_region_id')->nullable()->after('destination_country_id');
            $table->unsignedBigInteger('destination_city_id')->nullable()->after('destination_region_id');
            $table->string('destination_address')->nullable()->after('destination_city_id');
            $table->string('destination_contact_name')->nullable()->after('destination_address');
            $table->string('destination_contact_phone')->nullable()->after('destination_contact_name');
        });
    }

    public function down(): void
    {
        Schema::table('order_item_shipments', function (Blueprint $table) {
            $table->dropColumn([
                'origin_country_id',
                'origin_region_id',
                'origin_city_id',
                'origin_address',
                'origin_contact_name',
                'origin_contact_phone',
                'destination_country_id',
                'destination_region_id',
                'destination_city_id',
                'destination_address',
                'destination_contact_name',
                'destination_contact_phone',
            ]);
        });
    }
};
