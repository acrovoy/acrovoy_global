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
    Schema::table('product_variant_groups', function (Blueprint $table) {
        $table->string('variant_hash')->unique()->after('id');
        $table->integer('sort_order')->default(0)->index()->after('name');
        $table->longText('metadata')->nullable()->after('sort_order');
    });
}

public function down(): void
{
    Schema::table('product_variant_groups', function (Blueprint $table) {
        $table->dropUnique(['variant_hash']);
        $table->dropColumn([
            'variant_hash',
            'sort_order',
            'metadata'
        ]);
    });
}
};
