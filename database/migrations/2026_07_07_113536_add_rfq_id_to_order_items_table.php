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
    Schema::table('order_items', function (Blueprint $table) {
        $table->foreignId('rfq_id')
            ->nullable()
            ->after('product_id')
            ->constrained('rfqs')
            ->nullOnDelete();
    });
}

public function down(): void
{
    Schema::table('order_items', function (Blueprint $table) {
        $table->dropForeign(['rfq_id']);
        $table->dropColumn('rfq_id');
    });
}
};
