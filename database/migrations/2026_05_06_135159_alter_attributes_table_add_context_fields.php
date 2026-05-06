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

    $table->string('entity_type')->nullable()->after('code');
    $table->string('group_name')->nullable()->after('entity_type');

    $table->boolean('is_system')->default(false)->after('is_required');
    $table->boolean('is_offerable')->default(true)->after('is_filterable');
    $table->boolean('is_custom')->default(false)->after('is_offerable');

    // ❗ ВАЖНО: НЕ json
    $table->longText('meta')->nullable();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
