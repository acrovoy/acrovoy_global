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
        Schema::table('order_item_shipments', function (Blueprint $table) {
            $table->unsignedBigInteger('changed_by')->nullable()->after('status')
                  ->comment('User ID who changed the status; null if automatic');
            // Если нужен FK на users:
            // $table->foreign('changed_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('order_item_shipments', function (Blueprint $table) {
            $table->dropColumn('changed_by');
        });
    }
};
