<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('premium_plan_id')->nullable()->after('remember_token');

            // опционально, если хотим связь с таблицей планов
            $table->foreign('premium_plan_id')
                ->references('id')
                ->on('premium_seller_plans')
                ->nullOnDelete(); // если план удалён, поле станет NULL
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['premium_plan_id']);
            $table->dropColumn('premium_plan_id');
        });
    }
};
