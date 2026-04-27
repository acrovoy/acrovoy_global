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
        Schema::create('rfq_offer_version_items', function (Blueprint $table) {

            $table->id();

            // версия оффера
            $table->foreignId('offer_version_id')
                ->constrained('rfq_offer_versions')
                ->cascadeOnDelete();

            // оригинальный offer_item
            $table->foreignId('offer_item_id')
                ->nullable()
                ->constrained('rfq_offer_items')
                ->nullOnDelete();

            // requirement reference
            $table->foreignId('requirement_id')
                ->constrained('rfq_requirements')
                ->cascadeOnDelete();

            // snapshot fields
            $table->decimal('unit_price', 15, 4)->nullable();

            $table->integer('quantity')->nullable();

            $table->string('currency', 10)->nullable();

            $table->integer('lead_time_days')->nullable();

            $table->integer('moq')->nullable();

            $table->text('notes')->nullable();

            // audit snapshot
            $table->timestamps();


            /*
             INDEXES
            */

            $table->index('offer_version_id');

            $table->index('requirement_id');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rfq_offer_version_items');
    }
};
