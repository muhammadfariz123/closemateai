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
        // Add bank & invoice preferences to users table if not already present
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'bank_name')) {
                $table->string('bank_name')->nullable();
            }
            if (!Schema::hasColumn('users', 'bank_account_number')) {
                $table->string('bank_account_number')->nullable();
            }
            if (!Schema::hasColumn('users', 'bank_account_name')) {
                $table->string('bank_account_name')->nullable();
            }
            if (!Schema::hasColumn('users', 'default_terms')) {
                $table->json('default_terms')->nullable();
            }
        });

        // Create invoices table
        if (!Schema::hasTable('invoices')) {
            Schema::create('invoices', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('booking_id')->nullable();
                $table->string('invoice_number');
                $table->date('invoice_date')->nullable();
                $table->string('vendor_name')->nullable();
                $table->text('vendor_address')->nullable();
                $table->string('client_name')->nullable();
                $table->string('client_wa')->nullable();
                $table->text('client_address')->nullable();
                $table->date('event_date')->nullable();
                $table->string('termin_label')->nullable();
                $table->string('package_name')->nullable();
                $table->decimal('package_price', 15, 2)->default(0);
                $table->integer('package_qty')->default(1);
                $table->decimal('discount', 15, 2)->default(0);
                $table->json('extra_packages')->nullable();
                $table->json('addons')->nullable();
                $table->decimal('subtotal_package', 15, 2)->default(0);
                $table->decimal('total_addon', 15, 2)->default(0);
                $table->decimal('total_discount', 15, 2)->default(0);
                $table->decimal('grand_total', 15, 2)->default(0);
                $table->decimal('dp1_amount', 15, 2)->default(0);
                $table->decimal('dp2_amount', 15, 2)->default(0);
                $table->decimal('total_dp', 15, 2)->default(0);
                $table->decimal('remaining_balance', 15, 2)->default(0);
                $table->date('due_date')->nullable();
                $table->string('bank_name')->nullable();
                $table->string('bank_account_number')->nullable();
                $table->string('bank_account_name')->nullable();
                $table->json('terms_and_conditions')->nullable();
                $table->string('theme')->default('theme-minimalist');
                $table->string('status')->default('saved');
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        } else {
            Schema::table('invoices', function (Blueprint $table) {
                if (!Schema::hasColumn('invoices', 'client_wa')) {
                    $table->string('client_wa')->nullable();
                }
                if (!Schema::hasColumn('invoices', 'termin_label')) {
                    $table->string('termin_label')->nullable();
                }
                if (!Schema::hasColumn('invoices', 'total_discount')) {
                    $table->decimal('total_discount', 15, 2)->default(0);
                }
                if (!Schema::hasColumn('invoices', 'total_dp')) {
                    $table->decimal('total_dp', 15, 2)->default(0);
                }
                if (!Schema::hasColumn('invoices', 'remaining_balance')) {
                    $table->decimal('remaining_balance', 15, 2)->default(0);
                }
                if (!Schema::hasColumn('invoices', 'terms_and_conditions')) {
                    $table->json('terms_and_conditions')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['bank_name', 'bank_account_number', 'bank_account_name', 'default_terms']);
        });
    }
};
