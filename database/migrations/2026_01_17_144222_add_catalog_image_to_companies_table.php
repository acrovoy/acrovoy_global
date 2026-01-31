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
    Schema::table('suppliers', function (Blueprint $table) {
        $table->string('catalog_image')->nullable()->after('logo');
    });
}

public function down()
{
    Schema::table('suppliers', function (Blueprint $table) {
        $table->dropColumn('catalog_image');
    });
}

};
