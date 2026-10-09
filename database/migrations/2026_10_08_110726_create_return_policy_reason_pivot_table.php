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
        Schema::create('return_policy_reason_pivot', function (Blueprint $table) {
            $table->id();

            $table->foreignId('return_policy_id')
                ->constrained('return_policies')
                ->cascadeOnDelete();

            $table->foreignId('reason_id')
                ->constrained('return_policy_reasons')
                ->cascadeOnDelete();

            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->unique(
                ['return_policy_id', 'reason_id'],
                'return_policy_reason_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('return_policy_reason_pivot');
    }
};
