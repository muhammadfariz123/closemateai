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
        // 1. Theme Color & Public Booking Token on users
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'theme_color')) {
                $table->string('theme_color', 20)->default('#4472c4')->after('business_name');
            }
            if (!Schema::hasColumn('users', 'public_booking_token')) {
                $table->string('public_booking_token', 64)->nullable()->unique()->after('webhook_secret');
            }
        });

        // 2. Theme color on hris_settings
        if (Schema::hasTable('hris_settings')) {
            Schema::table('hris_settings', function (Blueprint $table) {
                if (!Schema::hasColumn('hris_settings', 'theme_color')) {
                    $table->string('theme_color', 20)->default('#4472c4')->after('studio_name');
                }
            });
        }

        // 3. Client Reviews for Field Crew (Rating Bintang ala Grab)
        if (!Schema::hasTable('client_reviews')) {
            Schema::create('client_reviews', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->unsignedBigInteger('booking_id')->nullable()->index();
                $table->string('booking_uuid', 64)->nullable()->index();
                $table->string('client_name');
                $table->string('client_phone')->nullable();
                $table->string('field_crew_names')->nullable();
                $table->integer('rating_field_crew')->default(5);
                $table->integer('rating_punctuality')->default(5);
                $table->integer('rating_hospitality')->default(5);
                $table->integer('rating_overall')->default(5);
                $table->text('positive_tags')->nullable();
                $table->text('review_comment')->nullable();
                $table->boolean('is_public')->default(true);
                $table->timestamps();
            });
        }

        // 4. Office Work Evaluations (Fast Edit, Print Deadline to Ready To Pick Up)
        if (!Schema::hasTable('office_work_evaluations')) {
            Schema::create('office_work_evaluations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->index();
                $table->unsignedBigInteger('booking_id')->nullable()->index();
                $table->string('editor_name')->nullable();
                $table->string('production_staff_name')->nullable();
                $table->string('fast_edit_type')->default('Normal'); // Normal, Same Day Edit, Fast Edit 24 Jam, Rush
                $table->string('edit_status')->default('On Time'); // On Time, Ahead of Schedule, Late
                $table->decimal('edit_completion_hours', 5, 2)->nullable();
                $table->date('print_deadline_date')->nullable();
                $table->date('ready_to_pickup_date')->nullable();
                $table->string('print_status')->default('Ready to Pick Up'); // On Schedule, Delayed, Ready to Pick Up, Picked Up
                $table->integer('quality_score')->default(5);
                $table->text('office_notes')->nullable();
                $table->timestamps();
            });
        }

        // 5. Booking extra columns for analytics & marketing tracking
        Schema::table('bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('bookings', 'lead_source')) {
                $table->string('lead_source')->default('WhatsApp')->after('source');
            }
            if (!Schema::hasColumn('bookings', 'marketing_name')) {
                $table->string('marketing_name')->nullable()->after('lead_source');
            }
            if (!Schema::hasColumn('bookings', 'hpp_amount')) {
                $table->decimal('hpp_amount', 14, 2)->default(0)->after('package_price');
            }
            if (!Schema::hasColumn('bookings', 'add_on_items')) {
                $table->text('add_on_items')->nullable()->after('hpp_amount');
            }
            if (!Schema::hasColumn('bookings', 'add_on_amount')) {
                $table->decimal('add_on_amount', 14, 2)->default(0)->after('add_on_items');
            }
            if (!Schema::hasColumn('bookings', 'discount_amount')) {
                $table->decimal('discount_amount', 14, 2)->default(0)->after('add_on_amount');
            }
            if (!Schema::hasColumn('bookings', 'profit_amount')) {
                $table->decimal('profit_amount', 14, 2)->default(0)->after('discount_amount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('office_work_evaluations');
        Schema::dropIfExists('client_reviews');
    }
};

