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
        Schema::create('supplier_payment_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')
                ->unique()
                ->constrained('suppliers')
                ->cascadeOnDelete();

            $table->foreignId('default_payment_term_id')
                ->nullable()
                ->constrained('payment_terms')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_payment_settings');
    }
};
