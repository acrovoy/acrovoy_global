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
        Schema::create('rfq_participants', function (Blueprint $table) {
            $table->id();

            $table->foreignId('rfq_id')
                ->constrained('rfqs')
                ->cascadeOnDelete();

            // SUPPLIER LINK (ВАЖНО)
            $table->foreignId('supplier_id')
                ->constrained('suppliers')
                ->cascadeOnDelete();

            $table->string('status')->default('invited');
            // invited / accepted / declined

            $table->timestamp('invited_at')->nullable();
            $table->timestamp('responded_at')->nullable();

            $table->timestamps();

            $table->unique(['rfq_id', 'supplier_id']);
            $table->index(['rfq_id']);
            $table->index(['supplier_id']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rfq_participants');
    }
};
