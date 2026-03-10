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
    Schema::create('product_variant_items', function (Blueprint $table) {
        $table->id();

        $table->unsignedBigInteger('variant_group_id')->index();
        $table->unsignedBigInteger('product_id')->index()->nullable();

        $table->string('title')->nullable();

        $table->longText('attribute_value_json')->nullable();
        $table->unsignedBigInteger('media_id')->nullable()->index();

        $table->integer('sort_order')->default(0)->index();

        $table->longText('metadata')->nullable();

        $table->timestamps();

        // indexes (optional but recommended)
        $table->index(['variant_group_id', 'sort_order']);
    });
}

public function down(): void
{
    Schema::dropIfExists('product_variant_items');
}
};
