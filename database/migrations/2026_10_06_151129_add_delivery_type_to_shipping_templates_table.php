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
        Schema::table('shipping_templates', function (Blueprint $table) {
    $table->enum('delivery_type', [
        'self_pickup',
        'curbside_delivery',
        'door_to_door',
        'white_glove',
        'delivery_assembly',
        'delivery_installation',
        'custom',
    ])->default('door_to_door')
      ->comment('Type of delivery service')
      ->after('price_unit');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipping_templates', function (Blueprint $table) {
            //
        });
    }
};
