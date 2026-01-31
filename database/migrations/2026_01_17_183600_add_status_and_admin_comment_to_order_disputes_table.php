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
    Schema::table('order_disputes', function (Blueprint $table) {
        $table->string('status')->default('pending'); // pending, resolved, rejected
        $table->text('admin_comment')->nullable();
    });
}

public function down(): void
{
    Schema::table('order_disputes', function (Blueprint $table) {
        $table->dropColumn(['status', 'admin_comment']);
    });
}
};
