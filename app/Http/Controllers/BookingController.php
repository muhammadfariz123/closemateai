<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    protected function getUser()
    {
        $user = auth()->user() ?? \App\Models\User::first();
        return $user ? $user->getEffectiveOwner() : null;
    }

    protected function ensureSchemaReady()
    {
        static $ready = false;
        if ($ready) return;
        $ready = true;

        try {
            $needsMigration = false;
            if (!Schema::hasTable('bookings')) {
                $needsMigration = true;
            } elseif (!Schema::hasColumn('bookings', 'quotation_id') || !Schema::hasColumn('bookings', 'groom_name') || !Schema::hasColumn('bookings', 'production_tracks')) {
                $needsMigration = true;
            }

            if ($needsMigration) {
                Artisan::call('migrate', ['--force' => true]);
            }
        } catch (\Throwable $e) {
            Log::error('Booking schema migration error: ' . $e->getMessage());
        }
    }

    public function getBookings()
    {
        $this->ensureSchemaReady();

        try {
            $user = $this->getUser();
            if (!$user) return response()->json([]);

            $bookings = Booking::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();
                
            // Fallback for old bookings without UUID or missing total_income
            foreach ($bookings as $b) {
                $changed = false;
                if (empty($b->uuid)) {
                    $b->uuid = (string) \Illuminate\Support\Str::uuid();
                    $changed = true;
                }
                if ((empty($b->total_income) || $b->total_income == 0) && !empty($b->package_price) && $b->package_price > 0) {
                    $b->total_income = (float) $b->package_price;
                    $b->net_profit = (float) ($b->package_price - ($b->total_operational_cost ?? 0));
                    $changed = true;
                }
                if ($changed) {
                    $b->save();
                }
            }

            return response()->json($bookings);
        } catch (\Throwable $e) {
            Log::error('getBookings error: ' . $e->getMessage());
            return response()->json([]);
        }
    }

    public function addBooking(Request $request)
    {
        $this->ensureSchemaReady();

        try {
            $user = $this->getUser();
            if (!$user) return response()->json(['success' => false, 'error' => 'Unauthorized'], 401);

            // If ID is provided, update existing
            if ($request->has('id') && $request->input('id')) {
                $booking = Booking::where('user_id', $user->id)->find($request->input('id'));
                if (!$booking) return response()->json(['success' => false, 'error' => 'Booking not found'], 404);
            } elseif ($request->has('quotation_id') && $request->input('quotation_id')) {
                // Check if booking already exists for this quotation
                $booking = Booking::where('user_id', $user->id)
                    ->where('quotation_id', $request->input('quotation_id'))
                    ->first();
                if (!$booking) {
                    $booking = new Booking();
                    $booking->user_id = $user->id;
                }
            } else {
                $booking = new Booking();
                $booking->user_id = $user->id;
            }
            
            $fields = [
                'quotation_id', 'client_name', 'client_wa_number', 'client_address', 'event_date', 
                'start_time', 'end_time', 'package_name', 'package_price', 'package_qty', 
                'paid_amount', 'discount', 'addons', 'operational_costs', 'total_income', 
                'total_operational_cost', 'net_profit', 'payment_date', 'payment_status', 
                'production_status', 'result_link', 'team_members', 'notes', 'production_deadline',
                'production_tracks',
                // WA Booking Form Fields
                'groom_name', 'bride_name', 'parents_name',
                'akad_time', 'reception_time',
                'event_address', 'maps_link',
                'client_email', 'client_instagram',
                'vendor_mua', 'vendor_henna', 'vendor_busana', 'vendor_hiburan',
                'vendor_mc', 'vendor_dekorasi', 'vendor_wo',
                'sort_foto', 'dp_amount', 'transport_cost', 'total_dp',
                'source', 'chat_id',
            ];

            $numericFields = [
                'package_price', 'package_qty', 'paid_amount', 'discount', 
                'total_income', 'total_operational_cost', 'net_profit',
                'dp_amount', 'transport_cost', 'total_dp',
            ];

            $dateOrTimeFields = [
                'event_date', 'payment_date', 'start_time', 'end_time', 
                'akad_time', 'reception_time', 'production_deadline'
            ];

            $existingColumns = [];
            try {
                $existingColumns = Schema::getColumnListing('bookings');
            } catch (\Throwable $e) {
                $existingColumns = [];
            }
            
            foreach ($fields as $field) {
                if ($request->has($field)) {
                    // Only assign if column actually exists in DB
                    if (!empty($existingColumns) && !in_array($field, $existingColumns)) {
                        continue;
                    }

                    $val = $request->input($field);
                    if (in_array($field, $numericFields) && $val !== null) {
                        if (is_numeric($val)) {
                            $val = (float) $val;
                        } elseif (is_string($val)) {
                            $cleaned = preg_replace('/[^0-9.-]/', '', $val);
                            $val = is_numeric($cleaned) ? (float) $cleaned : 0;
                        } else {
                            $val = 0;
                        }
                    }

                    // Convert empty strings to null for date/time fields to prevent PostgreSQL syntax error
                    if (in_array($field, $dateOrTimeFields)) {
                        if (empty($val) || (is_string($val) && trim($val) === '')) {
                            $val = null;
                        }
                    }

                    $booking->{$field} = $val;
                }
            }
            
            // Ensure not-null required columns in Postgres
            if (empty($booking->client_name)) {
                $booking->client_name = 'Klien Baru';
            }
            if (empty($booking->client_wa_number)) {
                $booking->client_wa_number = '-';
            }

            $booking->save();

            return response()->json(['success' => true, 'booking' => $booking]);
        } catch (\Throwable $e) {
            Log::error('addBooking error: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function deleteBooking($id)
    {
        $this->ensureSchemaReady();

        try {
            $user = $this->getUser();
            if (!$user) return response()->json(['success' => false, 'error' => 'Unauthorized'], 401);

            Booking::where('user_id', $user->id)->where('id', $id)->delete();
            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            Log::error('deleteBooking error: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}
