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
        Schema::table('premium_seller_plans', function (Blueprint $table) {
            // Поле для типа: buyer или supplier
            $table->enum('target_type', ['buyer', 'supplier'])->default('supplier')->after('name');
        });
    }

    public function down()
    {
        Schema::table('premium_seller_plans', function (Blueprint $table) {
            $table->dropColumn('target_type');
        });
    }
};
