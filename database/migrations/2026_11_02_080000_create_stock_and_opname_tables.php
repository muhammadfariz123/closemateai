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
        // 1. Stock Items
        if (!Schema::hasTable('stock_items')) {
            Schema::create('stock_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->string('code')->nullable();
                $table->string('name');
                $table->string('category')->default('Perlengkapan');
                $table->string('unit')->default('unit');
                $table->integer('stock_system')->default(0);
                $table->integer('min_stock')->default(5);
                $table->decimal('price', 14, 2)->default(0);
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // 2. Stock Opnames
        if (!Schema::hasTable('stock_opnames')) {
            Schema::create('stock_opnames', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->unsignedBigInteger('inspector_id')->index();
                $table->unsignedBigInteger('stock_item_id')->index();
                $table->integer('system_stock')->default(0);
                $table->integer('physical_stock')->default(0);
                $table->integer('difference')->default(0);
                $table->text('notes')->nullable();
                $table->string('status')->default('pending'); // 'pending', 'approved', 'rejected'
                $table->unsignedBigInteger('verified_by')->nullable();
                $table->timestamp('verified_at')->nullable();
                $table->text('rejection_reason')->nullable();
                $table->timestamps();
            });
        }

        // 3. Stock Transactions (In, Out, Purchase, Opname Adjustment)
        if (!Schema::hasTable('stock_transactions')) {
            Schema::create('stock_transactions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->unsignedBigInteger('stock_item_id')->nullable()->index();
                $table->string('item_name')->nullable();
                $table->string('type'); // 'in', 'out', 'purchase', 'opname_adjustment'
                $table->integer('qty')->default(0);
                $table->decimal('unit_price', 14, 2)->default(0);
                $table->decimal('total_price', 14, 2)->default(0);
                $table->string('receipt_file')->nullable();
                $table->text('notes')->nullable();
                $table->string('status')->default('approved'); // 'pending', 'approved', 'rejected'
                $table->unsignedBigInteger('created_by')->index();
                $table->unsignedBigInteger('verified_by')->nullable();
                $table->timestamp('verified_at')->nullable();
                $table->text('rejection_reason')->nullable();
                $table->timestamps();
            });
        }

        // 4. Production Balances
        if (!Schema::hasTable('production_balances')) {
            Schema::create('production_balances', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->unique()->index();
                $table->decimal('balance', 14, 2)->default(0);
                $table->timestamps();
            });
        }

        // 5. Production Balance Logs
        if (!Schema::hasTable('production_balance_logs')) {
            Schema::create('production_balance_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->string('type'); // 'topup', 'purchase', 'adjustment'
                $table->decimal('amount', 14, 2)->default(0);
                $table->decimal('balance_after', 14, 2)->default(0);
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('reference_id')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_balance_logs');
        Schema::dropIfExists('production_balances');
        Schema::dropIfExists('stock_transactions');
        Schema::dropIfExists('stock_opnames');
        Schema::dropIfExists('stock_items');
    }
};

