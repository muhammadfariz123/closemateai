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
        if (Schema::hasTable('invoices')) {
            Schema::table('invoices', function (Blueprint $table) {
                if (!Schema::hasColumn('invoices', 'payments')) {
                    $table->json('payments')->nullable();
                }
                if (!Schema::hasColumn('invoices', 'payment_status')) {
                    $table->string('payment_status')->default('Belum Dibayar');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('invoices')) {
            Schema::table('invoices', function (Blueprint $table) {
                if (Schema::hasColumn('invoices', 'payments')) {
                    $table->dropColumn('payments');
                }
                if (Schema::hasColumn('invoices', 'payment_status')) {
                    $table->dropColumn('payment_status');
                }
            });
        }
    }
};
