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
        Schema::create('messages', function (Blueprint $table) {

            $table->id();

            $table->uuid('uuid')->unique();

            $table->foreignId('conversation_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->nullableMorphs('sender');

            $table->string('message_type', 30)->default('text');

            $table->longText('message')->nullable();

            $table->longText('payload')->nullable();

            $table->foreignId('reply_to_message_id')
                ->nullable()
                ->constrained('messages')
                ->nullOnDelete();

            $table->timestamp('edited_at')->nullable();

            $table->softDeletes();

            $table->timestamps();

            $table->index([
                'conversation_id',
                'created_at'
            ]);

            $table->index('message_type');

            
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
