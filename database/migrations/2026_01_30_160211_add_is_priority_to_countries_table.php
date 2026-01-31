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
    Schema::table('countries', function (Blueprint $table) {
        $table->tinyInteger('is_priority')->default(0)->after('is_active');
    });
}

public function down()
{
    Schema::table('countries', function (Blueprint $table) {
        $table->dropColumn('is_priority');
    });
}
};
