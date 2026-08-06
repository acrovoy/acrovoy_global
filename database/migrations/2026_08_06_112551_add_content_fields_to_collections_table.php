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
    Schema::table('collections', function (Blueprint $table) {

        $table->string('subtitle')->nullable()->after('slug');

        $table->text('overview')->nullable()->after('subtitle');

        $table->longText('highlights')->nullable()->after('overview');

        $table->text('ideal_for')->nullable()->after('highlights');

        $table->text('procurement_notes')->nullable()->after('ideal_for');

    });
}

public function down(): void
{
    Schema::table('collections', function (Blueprint $table) {

        $table->dropColumn([
            'subtitle',
            'overview',
            'highlights',
            'ideal_for',
            'procurement_notes',
        ]);

    });
}
};
