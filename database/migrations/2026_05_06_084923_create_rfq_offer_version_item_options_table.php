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
        Schema::create('rfq_offer_version_item_options', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('offer_version_item_id');
            $table->unsignedBigInteger('option_id');

            $table->timestamps();

            // 🔥 UNIQUE — чтобы не было дублей
            $table->unique([
                'offer_version_item_id',
                'option_id'
            ], 'rfq_offer_item_option_unique');

            // 🔥 Индексы
            $table->index('offer_version_item_id');
            $table->index('option_id');

            // 🔥 Foreign keys
            $table->foreign('offer_version_item_id')
                ->references('id')
                ->on('rfq_offer_version_items')
                ->cascadeOnDelete();

            $table->foreign('option_id')
                ->references('id')
                ->on('attribute_options')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rfq_offer_version_item_options');
    }
};
