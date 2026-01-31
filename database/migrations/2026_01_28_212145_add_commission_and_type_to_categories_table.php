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
        Schema::table('categories', function (Blueprint $table) {
            $table->decimal('commission_percent', 5, 2)->default(0)->after('level')
                  ->comment('Комиссия для конечной категории (Level 3)');
            $table->enum('type', ['product', 'rfq'])->default('product')->after('commission_percent')
                  ->comment('Тип категории: товар или проект/RFQ');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('commission_percent');
            $table->dropColumn('type');
        });
    }
};
