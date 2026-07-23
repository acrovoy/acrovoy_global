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
    Schema::table('conversation_participants', function (Blueprint $table) {
        $table->string('platform_role', 20)
            ->nullable()
            ->after('role');

        $table->index([
            'context_type',
            'context_id',
            'platform_role',
        ], 'conversation_participants_context_platform_idx');
    });
}

public function down(): void
{
    Schema::table('conversation_participants', function (Blueprint $table) {
        $table->dropIndex('conversation_participants_context_platform_idx');
        $table->dropColumn('platform_role');
    });
}
};
