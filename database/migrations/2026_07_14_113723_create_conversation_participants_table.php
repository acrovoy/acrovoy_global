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
        Schema::create('conversation_participants', function (Blueprint $table) {

            $table->id();

            $table->foreignId('conversation_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->nullableMorphs('actor');

            $table->nullableMorphs('context');

            $table->string('role')->nullable();

            $table->timestamp('last_read_at')->nullable();

            $table->timestamps();

            $table->unique([
                'conversation_id',
                'context_type',
                'context_id'
            ], 'conversation_context_unique');

           
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversation_participants');
    }
};
