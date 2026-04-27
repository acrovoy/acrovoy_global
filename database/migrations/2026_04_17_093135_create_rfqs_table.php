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
        Schema::create('rfqs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            // OWNER (USER WHO CREATES RFQ)
            $table->foreignId('buyer_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // ACTOR (audit)
            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('title')->nullable();
            $table->text('description')->nullable();

            $table->string('type');
            $table->string('status');

            $table->timestamp('published_at')->nullable();
            $table->timestamp('closed_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['buyer_id']);
            $table->index(['status']);
            $table->index(['type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rfqs');
    }
};
