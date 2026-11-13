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
        Schema::table('hris_employees', function (Blueprint $table) {
            if (!Schema::hasColumn('hris_employees', 'nik')) {
                $table->string('nik', 32)->nullable()->after('email');
            }
            if (!Schema::hasColumn('hris_employees', 'gender')) {
                $table->string('gender', 20)->default('Laki-laki')->after('phone');
            }
            if (!Schema::hasColumn('hris_employees', 'birth_date')) {
                $table->date('birth_date')->nullable()->after('gender');
            }
            if (!Schema::hasColumn('hris_employees', 'address')) {
                $table->text('address')->nullable()->after('birth_date');
            }
            if (!Schema::hasColumn('hris_employees', 'shift')) {
                $table->string('shift', 64)->default('Shift Pagi (08:00 - 17:00 WIB)')->after('position');
            }
        });

        // Also check if hris_payrolls has published_at or pdf_path
        Schema::table('hris_payrolls', function (Blueprint $table) {
            if (!Schema::hasColumn('hris_payrolls', 'late_deductions_count')) {
                $table->integer('late_deductions_count')->default(0)->after('late_deductions');
            }
            if (!Schema::hasColumn('hris_payrolls', 'unexcused_deductions')) {
                $table->decimal('unexcused_deductions', 14, 2)->default(0)->after('late_deductions_count');
            }
            if (!Schema::hasColumn('hris_payrolls', 'other_deductions')) {
                $table->decimal('other_deductions', 14, 2)->default(0)->after('unexcused_deductions');
            }
            if (!Schema::hasColumn('hris_payrolls', 'published_at')) {
                $table->timestamp('published_at')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hris_employees', function (Blueprint $table) {
            $table->dropColumn(['nik', 'gender', 'birth_date', 'address', 'shift']);
        });

        Schema::table('hris_payrolls', function (Blueprint $table) {
            $table->dropColumn(['late_deductions_count', 'unexcused_deductions', 'other_deductions', 'published_at']);
        });
    }
};
