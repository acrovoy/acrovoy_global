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
        Schema::create('supplier_lead_activities', function (Blueprint $table) {
            $table->id();

            // Related CRM company
            $table->foreignId('supplier_lead_id')
                ->constrained('supplier_leads')
                ->cascadeOnDelete();

            // Administrator who performed the activity
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Activity information
            $table->string('type', 32)->index();

            $table->string('subject')->nullable();
            $table->text('description')->nullable();

            // Status tracking
            $table->string('old_status', 32)->nullable();
            $table->string('new_status', 32)->nullable();

            // Follow-up scheduling
            $table->timestamp('follow_up_at')->nullable()->index();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['supplier_lead_id', 'created_at']);
            $table->index(['supplier_lead_id', 'type']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_lead_activities');
    }
};
