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
        Schema::create('saved_suppliers', function (Blueprint $table) {
            $table->id();

            // Buyer who saved the supplier
            $table->string('buyer_type');
            $table->unsignedBigInteger('buyer_id');

            // Saved supplier
            $table->foreignId('supplier_id')
                ->constrained('suppliers')
                ->cascadeOnDelete();

            // User who performed the action
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            // Prevent duplicate suppliers for the same buyer
            $table->unique(
                ['buyer_type', 'buyer_id', 'supplier_id'],
                'saved_suppliers_buyer_supplier_unique'
            );

            $table->index(
                ['buyer_type', 'buyer_id'],
                'saved_suppliers_buyer_index'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saved_suppliers');
    }
};
