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

    // удаляем старые строки
    $table->dropColumn(['origin_country', 'destination_country']);

    // добавляем связи
    $table->foreignId('origin_country_id')->constrained('countries');
    $table->foreignId('destination_country_id')->constrained('countries');

});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ids', function (Blueprint $table) {
            //
        });
    }
};
