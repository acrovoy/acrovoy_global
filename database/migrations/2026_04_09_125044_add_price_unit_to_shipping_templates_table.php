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
            $table->enum('price_unit', ['per_item','per_kg','per_cubic_meter','flat'])
                  ->default('flat')
                  ->after('price')
                  ->comment('Unit for shipping price: per item, per kg, per cubic meter, flat rate');
        });
    }

    public function down(): void
    {
        Schema::table('shipping_templates', function (Blueprint $table) {
            $table->dropColumn('price_unit');
        });
    }
};
