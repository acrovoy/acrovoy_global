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
    Schema::table('shipping_dimensions', function (Blueprint $table) {
        $table->string('supplier_type')
            ->nullable()
            ->after('package_type');

        $table->unsignedBigInteger('supplier_id')
            ->nullable()
            ->after('supplier_type');

        $table->index(
            ['supplier_type', 'supplier_id'],
            'shipping_dim_supplier_idx'
        );
    });
}

public function down(): void
{
    Schema::table('shipping_dimensions', function (Blueprint $table) {
        $table->dropIndex('shipping_dim_supplier_idx');
        $table->dropColumn([
            'supplier_type',
            'supplier_id',
        ]);
    });
}
};
