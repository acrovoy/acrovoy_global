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
        Schema::table('project_items', function (Blueprint $table) {
            // Сначала удаляем внешние ключи, если они существуют
            if (Schema::hasColumn('project_items', 'product_id')) {
                $table->dropForeign(['product_id']); // удаляем FK
                $table->dropColumn('product_id');    // потом сам столбец
            }

            if (Schema::hasColumn('project_items', 'supplier_id')) {
                $table->dropForeign(['supplier_id']); 
                $table->dropColumn('supplier_id');
            }

            // Проверяем необходимые поля и создаём, если нет
            if (!Schema::hasColumn('project_items', 'product_name')) {
                $table->string('product_name')->nullable();
            }
            if (!Schema::hasColumn('project_items', 'quantity')) {
                $table->integer('quantity')->nullable();
            }
            if (!Schema::hasColumn('project_items', 'price')) {
                $table->decimal('price', 12, 2)->nullable();
            }
            if (!Schema::hasColumn('project_items', 'lead_time_days')) {
                $table->integer('lead_time_days')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('project_items', function (Blueprint $table) {
            // Восстанавливаем удалённые столбцы с FK
            if (!Schema::hasColumn('project_items', 'product_id')) {
                $table->foreignId('product_id')->nullable()->constrained('products');
            }
            if (!Schema::hasColumn('project_items', 'supplier_id')) {
                $table->foreignId('supplier_id')->nullable()->constrained('suppliers');
            }
        });
    }
};
