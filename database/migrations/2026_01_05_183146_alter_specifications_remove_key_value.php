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
        Schema::table('specifications', function (Blueprint $table) {
            // Удаляем текстовые поля (они переезжают в translations)
            if (Schema::hasColumn('specifications', 'key')) {
                $table->dropColumn('key');
            }

            if (Schema::hasColumn('specifications', 'value')) {
                $table->dropColumn('value');
            }
        });
    }

    public function down(): void
    {
        Schema::table('specifications', function (Blueprint $table) {
            // Возвращаем обратно (на случай rollback)
            $table->string('key')->after('product_id');
            $table->string('value')->after('key');
        });
    }
};
