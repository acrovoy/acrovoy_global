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
        Schema::table('shipping_template_location', function (Blueprint $table) {
            $table->string('location_type')->default('delivery')->after('location_id');
        });
    }

    public function down(): void
    {
        Schema::table('shipping_template_location', function (Blueprint $table) {
            $table->dropColumn('location_type');
        });
    }
};
