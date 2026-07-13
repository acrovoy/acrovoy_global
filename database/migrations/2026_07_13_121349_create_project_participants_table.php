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
        Schema::create('project_participants', function (Blueprint $table) {
    $table->id();

    $table->foreignId('project_id')
        ->constrained()
        ->cascadeOnDelete();

    $table->string('participant_type');
    $table->unsignedBigInteger('participant_id');

    $table->string('status')->default('invited');

    $table->timestamp('invited_at')->nullable();
    $table->timestamp('viewed_at')->nullable();
    $table->timestamp('accepted_at')->nullable();
    $table->timestamp('declined_at')->nullable();

    $table->timestamps();

    $table->unique([
        'project_id',
        'participant_type',
        'participant_id'
    ], 'project_participant_unique');

    $table->index([
        'participant_type',
        'participant_id'
    ]);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_participants');
    }
};
