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
        Schema::create('rfq_offer_versions', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('rfq_offer_id');

            $table->unsignedInteger('version_number');

            $table->decimal('total_price', 15, 2)->nullable();

            $table->text('comment')->nullable();

            $table->boolean('is_counter')->default(false);

            $table->unsignedBigInteger('created_by');

            $table->timestamp('accepted_at')->nullable();

            $table->timestamps();


            /**
             * INDEXES
             */

            $table->index('rfq_offer_id');

            $table->index('version_number');

            $table->index('accepted_at');


            /**
             * FOREIGN KEYS
             */

            $table->foreign('rfq_offer_id')
                ->references('id')
                ->on('rfq_offers')
                ->cascadeOnDelete();

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();


            /**
             * UNIQUE VERSION PER OFFER
             */

            $table->unique([
                'rfq_offer_id',
                'version_number'
            ]);
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('rfq_offer_versions');
    }
};
