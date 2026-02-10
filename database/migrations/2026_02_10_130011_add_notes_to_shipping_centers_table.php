<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipping_centers', function (Blueprint $table) {
            $table->text('notes')->nullable()->after('is_active')->comment('Optional notes for the shipping route');
        });
    }

    public function down(): void
    {
        Schema::table('shipping_centers', function (Blueprint $table) {
            $table->dropColumn('notes');
        });
    }
};
