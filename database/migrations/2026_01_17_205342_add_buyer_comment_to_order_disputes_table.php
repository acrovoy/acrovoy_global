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
    Schema::table('order_disputes', function (Blueprint $table) {
        $table->text('buyer_comment')->nullable()->after('admin_comment');
    });
}

public function down()
{
    Schema::table('order_disputes', function (Blueprint $table) {
        $table->dropColumn('buyer_comment');
    });
}
};
