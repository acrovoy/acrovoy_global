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
        /**
         * =========================
         * RFQ OFFER ITEMS
         * =========================
         */
        Schema::table('rfq_offer_items', function (Blueprint $table) {

            // если колонка уже есть — НЕ трогаем
            if (!Schema::hasColumn('rfq_offer_items', 'rfq_offer_id')) {
                $table->unsignedBigInteger('rfq_offer_id')->after('id');
            }

            // безопасно добавляем index (если нет FK — не падает)
            $table->index('rfq_offer_id');
        });

        /**
         * =========================
         * RFQ OFFER PARTICIPANTS
         * =========================
         */
        Schema::table('rfq_offer_participants', function (Blueprint $table) {

            if (!Schema::hasColumn('rfq_offer_participants', 'rfq_offer_id')) {
                $table->unsignedBigInteger('rfq_offer_id')->after('id');
            }

            $table->index('rfq_offer_id');
        });
    }

    public function down(): void
    {
        Schema::table('rfq_offer_items', function (Blueprint $table) {
            if (Schema::hasColumn('rfq_offer_items', 'rfq_offer_id')) {
                $table->dropColumn('rfq_offer_id');
            }
        });

        Schema::table('rfq_offer_participants', function (Blueprint $table) {
            if (Schema::hasColumn('rfq_offer_participants', 'rfq_offer_id')) {
                $table->dropColumn('rfq_offer_id');
            }
        });
    }
};
