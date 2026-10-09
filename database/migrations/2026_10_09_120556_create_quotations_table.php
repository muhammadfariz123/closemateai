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
        Schema::create('quotations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('q_no');
            $table->string('status')->default('Draft');
            $table->string('client');
            $table->string('phone')->nullable();
            $table->date('event_date')->nullable();
            $table->string('venue')->nullable();
            $table->date('valid_until')->nullable();
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('grandTotal', 15, 2)->default(0);
            $table->text('tnc')->nullable();
            $table->text('internal_notes')->nullable();
            $table->json('items')->nullable();
            $table->json('termins')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
