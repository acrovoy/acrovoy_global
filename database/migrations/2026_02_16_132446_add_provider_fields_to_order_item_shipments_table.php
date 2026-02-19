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
            $table->string('provider_type')->nullable()->after('shipping_price');
            $table->unsignedBigInteger('provider_id')->nullable()->after('provider_type');

            $table->index(['provider_type', 'provider_id'], 'shipment_provider_index');
        });
    }

    public function down(): void
    {
        Schema::table('order_item_shipments', function (Blueprint $table) {
            $table->dropIndex('shipment_provider_index');
            $table->dropColumn(['provider_type', 'provider_id']);
        });
    }
};
