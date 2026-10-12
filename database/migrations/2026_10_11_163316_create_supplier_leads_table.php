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
        Schema::create('supplier_leads', function (Blueprint $table) {
            $table->id();

            // Company information
            $table->string('company_name');
            $table->string('legal_name')->nullable();
            $table->string('slug')->nullable()->unique();
            $table->string('website', 2048)->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 50)->nullable();

            // Location
            $table->string('country_id')->nullable();
            $table->string('city')->nullable();
            $table->text('address')->nullable();

            // Company classification
            $table->string('company_type', 32)->default('manufacturer');

            // Acquisition pipeline
            $table->string('acquisition_status', 32)->default('new')->index();
            $table->string('priority', 16)->default('normal')->index();
            $table->string('source', 32)->nullable();

            // Acquisition management
            $table->foreignId('assigned_to')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('created_by')
                ->constrained('users')
                ->restrictOnDelete();

            // Link to existing Acrovoy supplier profile
            $table->foreignId('supplier_id')
                ->nullable()
                ->unique()
                ->constrained('suppliers')
                ->nullOnDelete();

            // Internal notes
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Search and filtering
            $table->index('company_name');
            $table->index('country_id');
            $table->index('company_type');
            $table->index(['acquisition_status', 'priority']);
            $table->index(['assigned_to', 'acquisition_status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_leads');
    }
};
