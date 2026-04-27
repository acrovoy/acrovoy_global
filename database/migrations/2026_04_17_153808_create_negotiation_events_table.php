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
        Schema::create('negotiation_events', function (Blueprint $table) {
    $table->id();

    $table->foreignId('rfq_id')
        ->constrained('rfqs')
        ->cascadeOnDelete();

    $table->nullableMorphs('subject'); 
    // уже создаёт index автоматически

    $table->foreignId('user_id')
        ->nullable()
        ->constrained('users');

    $table->string('type');

    $table->longText('payload')->nullable();

    $table->timestamps();

    // ❌ УДАЛЕНО:
    // $table->index(['subject_type', 'subject_id']);

    // оставляем только это
    $table->index(['rfq_id', 'type']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('negotiation_events');
    }
};
