<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;

class BookingController extends Controller
{
    protected function getUser()
    {
        $user = auth()->user() ?? \App\Models\User::first();
        return $user ? $user->getEffectiveOwner() : null;
    }

    public function getBookings()
    {
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
    }

    public function addBooking(Request $request)
    {
        $user = $this->getUser();
        if (!$user) return response()->json(['success' => false], 401);

        // If ID is provided, update existing
        if ($request->has('id') && $request->input('id')) {
            $booking = Booking::where('user_id', $user->id)->find($request->input('id'));
            if (!$booking) return response()->json(['success' => false], 404);
        } else {
            $booking = new Booking();
            $booking->user_id = $user->id;
        }
        
        $fields = [
            'client_name', 'client_wa_number', 'client_address', 'event_date', 
            'start_time', 'end_time', 'package_name', 'package_price', 'package_qty', 
            'paid_amount', 'discount', 'addons', 'operational_costs', 'total_income', 
            'total_operational_cost', 'net_profit', 'payment_date', 'payment_status', 
            'production_status', 'result_link', 'team_members', 'notes', 'production_deadline',
            'production_tracks'
        ];

        $numericFields = [
            'package_price', 'package_qty', 'paid_amount', 'discount', 
            'total_income', 'total_operational_cost', 'net_profit'
        ];
        
        foreach ($fields as $field) {
            if ($request->has($field)) {
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
                $booking->{$field} = $val;
            }
        }
        
        $booking->save();

        return response()->json(['success' => true, 'booking' => $booking]);
    }

    public function deleteBooking($id)
    {
        $user = $this->getUser();
        if (!$user) return response()->json(['success' => false], 401);

        Booking::where('user_id', $user->id)->where('id', $id)->delete();
        return response()->json(['success' => true]);
    }
}
