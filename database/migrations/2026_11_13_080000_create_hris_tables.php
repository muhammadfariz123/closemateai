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
        // 1. HRIS Settings & Studio Geofence
        if (!Schema::hasTable('hris_settings')) {
            Schema::create('hris_settings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->string('studio_name')->default('Penapict Photography Studio');
                $table->text('studio_address')->nullable();
                $table->decimal('studio_lat', 10, 7)->default(-6.2000000);
                $table->decimal('studio_lng', 10, 7)->default(106.8166667);
                $table->integer('radius_meters')->default(100);
                $table->string('work_start_time')->default('08:30');
                $table->string('work_end_time')->default('17:30');
                $table->integer('late_tolerance_minutes')->default(15);
                $table->decimal('overtime_rate_per_hour', 12, 2)->default(25000);
                $table->decimal('late_penalty_per_hour', 12, 2)->default(20000);
                $table->timestamps();
            });
        }

        // 2. HRIS Employees
        if (!Schema::hasTable('hris_employees')) {
            Schema::create('hris_employees', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->unsignedBigInteger('auth_user_id')->nullable()->index();
                $table->string('employee_code')->index();
                $table->string('name');
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->string('division')->default('Fotografer'); // Fotografer, Videografer, Editor, Produksi, Marketing, Admin, HR
                $table->string('position')->default('Staff');
                $table->string('role')->default('karyawan'); // karyawan, supervisor, admin, hr, owner
                $table->decimal('base_salary', 14, 2)->default(3500000);
                $table->decimal('allowance', 14, 2)->default(500000);
                $table->integer('leave_balance')->default(12);
                $table->string('status')->default('active'); // active, inactive
                $table->text('avatar_url')->nullable();
                $table->timestamps();
            });
        }

        // 3. HRIS Attendances (Selfie + GPS + Geofence)
        if (!Schema::hasTable('hris_attendances')) {
            Schema::create('hris_attendances', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->unsignedBigInteger('employee_id')->index();
                $table->date('date')->index();
                $table->timestamp('clock_in')->nullable();
                $table->timestamp('clock_out')->nullable();
                $table->text('clock_in_photo')->nullable();
                $table->text('clock_out_photo')->nullable();
                $table->decimal('clock_in_lat', 10, 7)->nullable();
                $table->decimal('clock_in_lng', 10, 7)->nullable();
                $table->decimal('clock_out_lat', 10, 7)->nullable();
                $table->decimal('clock_out_lng', 10, 7)->nullable();
                $table->decimal('distance_meters', 10, 2)->nullable();
                $table->string('status')->default('hadir'); // hadir, terlambat, izin, sakit, cuti, alfa
                $table->integer('late_minutes')->default(0);
                $table->boolean('is_marketing_bypass')->default(false);
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // 4. HRIS Leaves & Permissions
        if (!Schema::hasTable('hris_leaves')) {
            Schema::create('hris_leaves', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->unsignedBigInteger('employee_id')->index();
                $table->string('type')->default('Cuti Tahunan'); // Cuti Tahunan, Izin Sakit, Keperluan Pribadi, Melahirkan, Lainnya
                $table->date('start_date');
                $table->date('end_date');
                $table->integer('total_days')->default(1);
                $table->text('reason');
                $table->text('attachment')->nullable();
                $table->string('status')->default('pending'); // pending, approved, rejected
                $table->string('approved_by')->nullable();
                $table->text('admin_notes')->nullable();
                $table->timestamps();
            });
        }

        // 5. HRIS Overtime Requests
        if (!Schema::hasTable('hris_overtimes')) {
            Schema::create('hris_overtimes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->unsignedBigInteger('employee_id')->index();
                $table->date('date')->index();
                $table->string('start_time')->default('18:00');
                $table->string('end_time')->default('21:00');
                $table->decimal('duration_hours', 5, 2)->default(3.00);
                $table->string('project_name')->nullable(); // e.g. Wedding Event, Graduation Shoot, Editing Batch
                $table->text('description')->nullable();
                $table->decimal('estimated_pay', 12, 2)->default(0);
                $table->string('status')->default('pending'); // pending, approved, rejected
                $table->string('approved_by')->nullable();
                $table->text('admin_notes')->nullable();
                $table->timestamps();
            });
        }

        // 6. HRIS Payrolls
        if (!Schema::hasTable('hris_payrolls')) {
            Schema::create('hris_payrolls', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->unsignedBigInteger('employee_id')->index();
                $table->string('period_month', 7)->index(); // YYYY-MM e.g. 2026-11
                $table->decimal('base_salary', 14, 2)->default(0);
                $table->decimal('allowance', 14, 2)->default(0);
                $table->decimal('overtime_pay', 14, 2)->default(0);
                $table->decimal('bonus', 14, 2)->default(0);
                $table->decimal('deductions', 14, 2)->default(0);
                $table->decimal('late_deductions', 14, 2)->default(0);
                $table->decimal('net_salary', 14, 2)->default(0);
                $table->integer('total_present')->default(0);
                $table->integer('total_late')->default(0);
                $table->integer('total_leave')->default(0);
                $table->decimal('total_overtime_hours', 5, 2)->default(0);
                $table->string('status')->default('draft'); // draft, paid
                $table->date('payment_date')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // 7. HRIS Announcements
        if (!Schema::hasTable('hris_announcements')) {
            Schema::create('hris_announcements', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->string('title');
                $table->text('content');
                $table->string('category')->default('Umum'); // Umum, Jadwal Event Foto, SOP Studio, Kebijakan
                $table->boolean('is_pinned')->default(false);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hris_announcements');
        Schema::dropIfExists('hris_payrolls');
        Schema::dropIfExists('hris_overtimes');
        Schema::dropIfExists('hris_leaves');
        Schema::dropIfExists('hris_attendances');
        Schema::dropIfExists('hris_employees');
        Schema::dropIfExists('hris_settings');
    }
};
