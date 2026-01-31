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
        Schema::table('languages', function (Blueprint $table) {
            $table->string('native_name', 255)->nullable()->after('name');
            $table->string('locale', 10)->nullable()->after('code');
            $table->enum('direction', ['ltr', 'rtl'])->default('ltr')->after('locale');
            $table->enum('priority', ['core', 'high', 'medium', 'low'])->default('medium')->after('direction');
            $table->integer('sort_order')->default(100)->after('priority');
            $table->tinyInteger('is_default')->default(0)->after('is_active');
            $table->string('notes', 255)->nullable()->after('is_default');
        });
    }

    public function down(): void
    {
        Schema::table('languages', function (Blueprint $table) {
            $table->dropColumn([
                'native_name',
                'locale',
                'direction',
                'priority',
                'sort_order',
                'is_default',
                'notes',
            ]);
        });
    }
};
