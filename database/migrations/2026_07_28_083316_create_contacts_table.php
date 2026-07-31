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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();

            // Полиморфная связь (User, Company, Branch и т.д.)
            $table->string('contactable_type');
            $table->unsignedBigInteger('contactable_id');

            // Тип контакта (phone, email, telegram, whatsapp...)
            $table->string('type', 50);

            // Значение контакта
            $table->string('value');

            // Подпись (Sales, Support, Office...)
            $table->string('label', 100)->nullable();

            // Основной контакт
            $table->boolean('is_primary')->default(false);

            // Показывать публично
            $table->boolean('is_public')->default(true);

            // Подтвержден ли контакт
            $table->timestamp('verified_at')->nullable();

            // Порядок отображения
            $table->unsignedSmallInteger('sort_order')->default(0);

            // Дополнительные данные (хранятся как JSON-строка)
            $table->longText('meta')->nullable();

            $table->timestamps();

            $table->index(['contactable_type', 'contactable_id']);
            $table->index('type');
            $table->index(['contactable_type', 'contactable_id', 'is_primary']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
