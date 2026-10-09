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
        Schema::create('return_policies', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('code')->nullable();

            $table->unsignedBigInteger('owner_id');
            $table->string('owner_type');

            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);

            $table->unsignedInteger('return_window_days')->nullable();

            $table->string('return_shipping_payer')->nullable();
            // buyer / supplier / depends_on_reason

            $table->decimal('restocking_fee_percent', 5, 2)
                ->nullable();

            $table->boolean('restocking_fee_enabled')->default(false);

            $table->boolean('custom_products_returnable')->default(false);

            $table->timestamps();

            $table->index(['owner_type', 'owner_id']);
            $table->index(['is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('return_policies');
    }
};
