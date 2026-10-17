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
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')
                ->default(0)
                ->after('status');

            $table->index(['supplier_id', 'sort_order']);
        });

        // Preserve the current newest-first order for existing products.
        $products = DB::table('products')
            ->select('id', 'supplier_id')
            ->whereNull('original_product_id')
            ->where('status', '!=', 'archived')
            ->orderBy('supplier_id')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        $currentSupplierId = null;
        $position = 0;

        foreach ($products as $product) {
            if ((int) $currentSupplierId !== (int) $product->supplier_id) {
                $currentSupplierId = $product->supplier_id;
                $position = 0;
            }

            DB::table('products')
                ->where('id', $product->id)
                ->update([
                    'sort_order' => ++$position,
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['supplier_id', 'sort_order']);
            $table->dropColumn('sort_order');
        });
    }
};
