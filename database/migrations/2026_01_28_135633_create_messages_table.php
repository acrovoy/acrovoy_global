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
            $table->foreignId('thread_id')->constrained('messages_threads')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // кто отправил
            $table->enum('role', ['buyer','manufacturer', 'admin']); // роль отправителя
            $table->text('text');
            $table->timestamps(); // created_at = время отправки
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
