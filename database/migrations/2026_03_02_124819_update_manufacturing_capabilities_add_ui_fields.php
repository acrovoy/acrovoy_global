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
        Schema::table('manufacturing_capabilities', function (Blueprint $table) {

    $table->string('icon')->nullable()->after('slug');

    $table->unsignedInteger('sort_order')->default(0)->after('icon');

    $table->boolean('visibility_flag')
        ->default(true)
        ->after('sort_order');

});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
