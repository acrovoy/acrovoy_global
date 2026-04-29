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
        Schema::table('rfq_offer_items', function (Blueprint $table) {

            // Добавляем поля участника
            $table->string('participant_type')->after('rfq_offer_id');
            $table->unsignedBigInteger('participant_id')->after('participant_type');

            // Индекс для полиморфной связи
            $table->index(['participant_type', 'participant_id'], 'rfq_offer_items_participant_index');
        });

        /*
        |--------------------------------------------------------------------------
        | Если нужно — можно восстановить данные (если ты делал временное поле)
        |--------------------------------------------------------------------------
        |
        | Если supplier_id уже удалён — этот блок не нужен
        | Если перенос делал заранее — тоже не нужен
        |
        */
    }

    public function down(): void
    {
        Schema::table('rfq_offer_items', function (Blueprint $table) {

            // Удаляем индекс
            $table->dropIndex('rfq_offer_items_participant_index');

            // Удаляем поля
            $table->dropColumn([
                'participant_type',
                'participant_id',
            ]);
        });
    }
};
