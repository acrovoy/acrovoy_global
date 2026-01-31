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
    Schema::table('rfq_offers', function (Blueprint $table) {
        $table->unsignedBigInteger('shipping_template_id')->nullable()->after('delivery_days');
        $table->foreign('shipping_template_id')
              ->references('id')
              ->on('shipping_templates')
              ->onDelete('set null');
    });
}

public function down(): void
{
    Schema::table('rfq_offers', function (Blueprint $table) {
        $table->dropForeign(['shipping_template_id']);
        $table->dropColumn('shipping_template_id');
    });
}
};
