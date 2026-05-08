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
        Schema::table('attributes', function (Blueprint $table) {

            $table->foreignId('group_id')
                ->nullable()
                ->after('context')
                ->constrained('attribute_groups')
                ->nullOnDelete();

            // удаляем старое поле
            $table->dropColumn('group_name');
        });
    }

    public function down(): void
    {
        Schema::table('attributes', function (Blueprint $table) {

            $table->string('group_name')->nullable();

            $table->dropConstrainedForeignId('group_id');
        });
    }
};
