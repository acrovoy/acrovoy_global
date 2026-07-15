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
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();

            $table->uuid('uuid')->unique();

            $table->string('conversation_type', 30);

            $table->nullableMorphs('subject');

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // FK добавим отдельной миграцией
            $table->unsignedBigInteger('last_message_id')->nullable();

            $table->timestamp('last_message_at')->nullable();

            $table->timestamps();

            $table->index('conversation_type');
            $table->index('last_message_at');
            
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
