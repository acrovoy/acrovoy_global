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
        Schema::create('supplier_lead_categories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('supplier_lead_id')
                ->constrained('supplier_leads')
                ->cascadeOnDelete();

            $table->foreignId('category_id')
                ->constrained('categories')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(
                ['supplier_lead_id', 'category_id'],
                'supplier_lead_categories_lead_category_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_lead_categories');
    }
};
