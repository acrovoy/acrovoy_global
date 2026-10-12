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
        Schema::create('supplier_lead_contacts', function (Blueprint $table) {
            $table->id();

            // Related CRM company
            $table->foreignId('supplier_lead_id')
                ->constrained('supplier_leads')
                ->cascadeOnDelete();

            // Contact information
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('job_title')->nullable();
            $table->string('department')->nullable();

            $table->string('email')->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('whatsapp', 50)->nullable();
            $table->string('telegram', 100)->nullable();
            $table->string('linkedin_url', 2048)->nullable();

            // Contact management
            $table->boolean('is_primary')->default(false)->index();
            $table->boolean('is_decision_maker')->default(false);
            $table->boolean('is_active')->default(true)->index();

            // Internal notes
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['supplier_lead_id', 'is_primary']);
            $table->index(['supplier_lead_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_lead_contacts');
    }
};
