<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_disputes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('reason'); // Причина спора
            $table->enum('action', ['return', 'compensation', 'exchange']); // Тип действия
            $table->string('attachment')->nullable(); // Файл с доказательством
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_disputes');
    }
};
