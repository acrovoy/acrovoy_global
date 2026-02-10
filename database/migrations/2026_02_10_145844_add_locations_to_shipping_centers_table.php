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
        Schema::table('shipping_centers', function (Blueprint $table) {
    $table->foreignId('origin_location_id')->nullable()->constrained('locations');
    $table->foreignId('destination_location_id')->nullable()->constrained('locations');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipping_centers', function (Blueprint $table) {
            //
        });
    }
};
