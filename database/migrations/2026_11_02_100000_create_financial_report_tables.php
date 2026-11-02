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
        // 1. Financial Master Salaries (Divisi Studio & Sosmed)
        if (!Schema::hasTable('financial_master_salaries')) {
            Schema::create('financial_master_salaries', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->string('division')->default('studio'); // 'studio', 'sosmed'
                $table->string('name');
                $table->string('position')->nullable();
                $table->integer('qty')->default(1);
                $table->decimal('price', 14, 2)->default(0);
                $table->decimal('total', 14, 2)->default(0);
                $table->timestamps();
            });
        }

        // 2. Financial Monthly Operations (Fixed monthly operational budgets)
        if (!Schema::hasTable('financial_monthly_operations')) {
            Schema::create('financial_monthly_operations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->string('name');
                $table->integer('qty')->default(1);
                $table->decimal('price', 14, 2)->default(0);
                $table->decimal('total', 14, 2)->default(0);
                $table->string('category')->default('Operasional');
                $table->timestamps();
            });
        }

        // 3. Financial Other Expenses (Operational incidental expenses)
        if (!Schema::hasTable('financial_expenses')) {
            Schema::create('financial_expenses', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->unsignedBigInteger('booking_id')->nullable()->index();
                $table->string('name');
                $table->date('date')->nullable();
                $table->string('description')->nullable();
                $table->decimal('amount', 14, 2)->default(0);
                $table->timestamps();
            });
        }

        // 4. Ensure additional detailed columns on bookings table
        if (Schema::hasTable('bookings')) {
            Schema::table('bookings', function (Blueprint $table) {
                if (!Schema::hasColumn('bookings', 'marketing_name')) {
                    $table->string('marketing_name')->nullable();
                }
                if (!Schema::hasColumn('bookings', 'cost_crew_foto')) {
                    $table->decimal('cost_crew_foto', 14, 2)->default(0);
                }
                if (!Schema::hasColumn('bookings', 'cost_cetak_4r')) {
                    $table->decimal('cost_cetak_4r', 14, 2)->default(0);
                }
                if (!Schema::hasColumn('bookings', 'cost_packaging')) {
                    $table->decimal('cost_packaging', 14, 2)->default(0);
                }
                if (!Schema::hasColumn('bookings', 'cost_album')) {
                    $table->decimal('cost_album', 14, 2)->default(0);
                }
                if (!Schema::hasColumn('bookings', 'cost_cetak_jumbo')) {
                    $table->decimal('cost_cetak_jumbo', 14, 2)->default(0);
                }
                if (!Schema::hasColumn('bookings', 'cost_crew_cinema')) {
                    $table->decimal('cost_crew_cinema', 14, 2)->default(0);
                }
                if (!Schema::hasColumn('bookings', 'laba_cinema')) {
                    $table->decimal('laba_cinema', 14, 2)->default(0);
                }
                if (!Schema::hasColumn('bookings', 'cost_crew_liputan')) {
                    $table->decimal('cost_crew_liputan', 14, 2)->default(0);
                }
                if (!Schema::hasColumn('bookings', 'laba_liputan')) {
                    $table->decimal('laba_liputan', 14, 2)->default(0);
                }
                if (!Schema::hasColumn('bookings', 'cost_crew_konten')) {
                    $table->decimal('cost_crew_konten', 14, 2)->default(0);
                }
                if (!Schema::hasColumn('bookings', 'laba_konten')) {
                    $table->decimal('laba_konten', 14, 2)->default(0);
                }
                if (!Schema::hasColumn('bookings', 'sales_fee')) {
                    $table->decimal('sales_fee', 14, 2)->default(0);
                }
                if (!Schema::hasColumn('bookings', 'crew_fee_details')) {
                    $table->json('crew_fee_details')->nullable();
                }
                if (!Schema::hasColumn('bookings', 'package_features')) {
                    $table->json('package_features')->nullable();
                }
                if (!Schema::hasColumn('bookings', 'extras_detail')) {
                    $table->json('extras_detail')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_expenses');
        Schema::dropIfExists('financial_monthly_operations');
        Schema::dropIfExists('financial_master_salaries');
    }
};
