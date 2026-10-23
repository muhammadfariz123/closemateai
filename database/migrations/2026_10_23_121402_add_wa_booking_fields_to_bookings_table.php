<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tambahkan kolom form booking via WhatsApp
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Mempelai & Orang Tua
            if (!Schema::hasColumn('bookings', 'groom_name')) {
                $table->string('groom_name')->nullable()->after('client_name');
            }
            if (!Schema::hasColumn('bookings', 'bride_name')) {
                $table->string('bride_name')->nullable()->after('groom_name');
            }
            if (!Schema::hasColumn('bookings', 'parents_name')) {
                $table->string('parents_name')->nullable()->after('bride_name');
            }

            // Waktu Akad & Resepsi
            if (!Schema::hasColumn('bookings', 'akad_time')) {
                $table->string('akad_time')->nullable()->after('start_time');
            }
            if (!Schema::hasColumn('bookings', 'reception_time')) {
                $table->string('reception_time')->nullable()->after('akad_time');
            }

            // Lokasi Lengkap
            if (!Schema::hasColumn('bookings', 'event_address')) {
                $table->text('event_address')->nullable()->after('client_address');
            }
            if (!Schema::hasColumn('bookings', 'maps_link')) {
                $table->string('maps_link')->nullable()->after('event_address');
            }

            // Kontak Klien
            if (!Schema::hasColumn('bookings', 'client_email')) {
                $table->string('client_email')->nullable()->after('client_wa_number');
            }
            if (!Schema::hasColumn('bookings', 'client_instagram')) {
                $table->string('client_instagram')->nullable()->after('client_email');
            }

            // Vendor Partner
            if (!Schema::hasColumn('bookings', 'vendor_mua')) {
                $table->string('vendor_mua')->nullable();
            }
            if (!Schema::hasColumn('bookings', 'vendor_henna')) {
                $table->string('vendor_henna')->nullable();
            }
            if (!Schema::hasColumn('bookings', 'vendor_busana')) {
                $table->string('vendor_busana')->nullable();
            }
            if (!Schema::hasColumn('bookings', 'vendor_hiburan')) {
                $table->string('vendor_hiburan')->nullable();
            }
            if (!Schema::hasColumn('bookings', 'vendor_mc')) {
                $table->string('vendor_mc')->nullable();
            }
            if (!Schema::hasColumn('bookings', 'vendor_dekorasi')) {
                $table->string('vendor_dekorasi')->nullable();
            }
            if (!Schema::hasColumn('bookings', 'vendor_wo')) {
                $table->string('vendor_wo')->nullable();
            }

            // Sortir & Pembayaran DP
            if (!Schema::hasColumn('bookings', 'sort_foto')) {
                $table->string('sort_foto')->nullable(); // 'Sortir Sendiri' / 'Diserahkan Tim'
            }
            if (!Schema::hasColumn('bookings', 'dp_amount')) {
                $table->decimal('dp_amount', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('bookings', 'transport_cost')) {
                $table->decimal('transport_cost', 15, 2)->default(0);
            }
            if (!Schema::hasColumn('bookings', 'total_dp')) {
                $table->decimal('total_dp', 15, 2)->default(0);
            }

            // Tracking Sumber Booking
            if (!Schema::hasColumn('bookings', 'source')) {
                $table->string('source')->default('manual')->comment('manual / wa_form / web');
            }
            if (!Schema::hasColumn('bookings', 'chat_id')) {
                $table->unsignedBigInteger('chat_id')->nullable()->index();
            }
            if (!Schema::hasColumn('bookings', 'wa_form_filled_at')) {
                $table->timestamp('wa_form_filled_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $cols = [
                'groom_name', 'bride_name', 'parents_name',
                'akad_time', 'reception_time',
                'event_address', 'maps_link',
                'client_email', 'client_instagram',
                'vendor_mua', 'vendor_henna', 'vendor_busana',
                'vendor_hiburan', 'vendor_mc', 'vendor_dekorasi', 'vendor_wo',
                'sort_foto', 'dp_amount', 'transport_cost', 'total_dp',
                'source', 'chat_id', 'wa_form_filled_at',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('bookings', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
