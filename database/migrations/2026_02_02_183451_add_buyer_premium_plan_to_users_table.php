<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Премиум-план для покупателя
            $table->bigInteger('buyer_premium_plan_id')->unsigned()->nullable()->after('premium_plan_id');
            $table->timestamp('buyer_premium_start')->nullable()->after('buyer_premium_plan_id');
            $table->timestamp('buyer_premium_end')->nullable()->after('buyer_premium_start');

            // Даты действия премиум-плана для поставщика
            $table->timestamp('supplier_premium_start')->nullable()->after('buyer_premium_end');
            $table->timestamp('supplier_premium_end')->nullable()->after('supplier_premium_start');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'buyer_premium_plan_id',
                'buyer_premium_start',
                'buyer_premium_end',
                'supplier_premium_start',
                'supplier_premium_end'
            ]);
        });
    }
};
