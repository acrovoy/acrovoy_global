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

        // кто владелец кастомного атрибута
        $table->string('owner_type')->nullable()->after('is_custom');
        $table->unsignedBigInteger('owner_id')->nullable()->after('owner_type');

        // индекс для быстрого поиска
        $table->index(['owner_type', 'owner_id']);
    });
}

public function down(): void
{
    Schema::table('attributes', function (Blueprint $table) {
        $table->dropIndex(['owner_type', 'owner_id']);

        $table->dropColumn([
            'owner_type',
            'owner_id',
        ]);
    });
}
};
