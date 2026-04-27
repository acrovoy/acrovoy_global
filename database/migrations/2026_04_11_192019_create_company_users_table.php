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
        Schema::create('company_users', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // polymorphic company (Supplier / LogisticCompany)
            $table->string('company_type');
            $table->unsignedBigInteger('company_id');

            // role inside company
            $table->string('role')->default('member');

            $table->timestamps();

            // indexes (важно для скорости)
            $table->index(['company_type', 'company_id']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_users');
    }
};
