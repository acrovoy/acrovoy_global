<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            // добавляем поля
            $table->string('name')->unique()->after('id');
            $table->string('slug')->unique()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            // откат
            $table->dropUnique(['name']);
            $table->dropUnique(['slug']);
            $table->dropColumn(['name', 'slug']);
        });
    }
};
