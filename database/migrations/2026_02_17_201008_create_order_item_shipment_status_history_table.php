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
    Schema::create('order_item_shipment_status_history', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('shipment_id');
        $table->string('status');
        $table->unsignedBigInteger('changed_by')->nullable();
        $table->text('comment')->nullable();
        $table->timestamps();

        $table->foreign('shipment_id')->references('id')->on('order_item_shipments')->cascadeOnDelete();
        $table->foreign('changed_by')->references('id')->on('users')->nullOnDelete();
    });
}

public function down(): void
{
    Schema::dropIfExists('order_item_shipment_status_history');
}
};
