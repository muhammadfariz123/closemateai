<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('bookings')) {
            try {
                DB::table('bookings')
                    ->whereNull('payment_status')
                    ->orWhere('payment_status', '')
                    ->update(['payment_status' => 'DP 1']);

                DB::table('bookings')
                    ->whereNull('production_status')
                    ->orWhere('production_status', '')
                    ->update(['production_status' => 'Pre-Event']);

                DB::table('bookings')
                    ->whereNull('client_name')
                    ->orWhere('client_name', '')
                    ->update(['client_name' => 'Klien Baru']);

                DB::table('bookings')
                    ->whereNull('client_wa_number')
                    ->orWhere('client_wa_number', '')
                    ->update(['client_wa_number' => '-']);
            } catch (\Throwable $e) {
                // Ignore if error occurs
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
