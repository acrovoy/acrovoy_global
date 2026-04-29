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
        Schema::create('rfq_offer_items', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('rfq_offer_id');
            $table->unsignedBigInteger('rfq_id');

            // optional link to supplier (for clarity, not required if you use morph elsewhere)
            $table->unsignedBigInteger('supplier_id')->nullable();

            // optional catalog identity (if you reuse items)
            $table->string('name')->nullable();
            $table->text('description')->nullable();

            // financial base (if item is reusable or template)
            $table->decimal('base_price', 12, 2)->nullable();

            $table->string('currency', 10)->default('USD');

            $table->timestamps();

            /*
            |----------------------------------------
            | INDEXES
            |----------------------------------------
            */
            $table->index('rfq_offer_id');
            $table->index('rfq_id');
            $table->index('supplier_id');

            /*
            |----------------------------------------
            | FOREIGN KEYS
            |----------------------------------------
            */
            $table->foreign('rfq_offer_id')
                ->references('id')
                ->on('rfq_offers')
                ->cascadeOnDelete();

            $table->foreign('rfq_id')
                ->references('id')
                ->on('rfqs')
                ->cascadeOnDelete();

            $table->foreign('supplier_id')
                ->references('id')
                ->on('suppliers')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rfq_offer_items');
    }
};
