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
        Schema::create('product_return_policies', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            $table->foreignId('return_policy_id')
                ->nullable()
                ->constrained('return_policies')
                ->nullOnDelete();

            $table->boolean('use_supplier_default')->default(true);

            $table->boolean('returnable')->nullable();

            $table->unsignedInteger('return_window_days')->nullable();

            $table->string('return_shipping_payer')->nullable();

            $table->boolean('restocking_fee_enabled')->nullable();

            $table->decimal('restocking_fee_percent', 5, 2)->nullable();

            $table->boolean('custom_products_returnable')->nullable();

            $table->text('additional_information')->nullable();

            $table->timestamps();

            $table->unique('product_id');

            $table->index('return_policy_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_return_policies');
    }
};
