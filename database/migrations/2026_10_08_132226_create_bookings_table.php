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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('client_name');
            $table->string('client_wa_number');
            $table->text('client_address')->nullable();
            
            $table->date('event_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            
            $table->string('package_name')->nullable();
            $table->decimal('package_price', 15, 2)->default(0);
            $table->integer('package_qty')->default(1);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            
            $table->json('addons')->nullable();
            $table->json('operational_costs')->nullable();
            
            $table->decimal('total_income', 15, 2)->default(0);
            $table->decimal('total_operational_cost', 15, 2)->default(0);
            $table->decimal('net_profit', 15, 2)->default(0);
            
            $table->date('payment_date')->nullable();
            $table->string('payment_status')->default('DP 1');
            $table->string('production_status')->default('Pre-Event');
            $table->string('result_link')->nullable();
            
            $table->json('team_members')->nullable();
            $table->text('notes')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
