<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('collectionables', function (Blueprint $table) {

            $table->id();

            $table->foreignId('collection_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('collectionable_type', 40);

            $table->unsignedBigInteger('collectionable_id');

            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

        });

        DB::statement('
            CREATE INDEX collectionables_lookup
            ON collectionables (collectionable_type, collectionable_id)
        ');
    }

    public function down(): void
    {
        Schema::dropIfExists('collectionables');
    }
};
