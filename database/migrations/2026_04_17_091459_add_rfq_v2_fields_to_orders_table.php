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
        Schema::table('orders', function (Blueprint $table) {

            // убираем старую RFQ-связь, если колонка ещё есть
            if (Schema::hasColumn('orders', 'rfq_offer_id')) {
                $table->dropColumn('rfq_offer_id');
            }

            // новая RFQ архитектура
            if (!Schema::hasColumn('orders', 'offer_version_id')) {
                $table->unsignedBigInteger('offer_version_id')->nullable()->index();
            }

            if (!Schema::hasColumn('orders', 'contract_id')) {
                $table->unsignedBigInteger('contract_id')->nullable()->index();
            }

        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            $table->dropColumn([
                'offer_version_id',
                'contract_id',
            ]);

            $table->unsignedBigInteger('rfq_offer_id')->nullable()->index();
        });
    }
};
