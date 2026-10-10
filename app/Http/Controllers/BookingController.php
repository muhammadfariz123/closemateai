<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;

class BookingController extends Controller
{
    public function getBookings()
    {
        $user = auth()->user() ?? \App\Models\User::first();
        if (!$user) return response()->json([]);

        $bookings = Booking::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($bookings);
    }

    public function addBooking(Request $request)
    {
        $user = auth()->user() ?? \App\Models\User::first();
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
            'production_status', 'result_link', 'team_members', 'notes', 'production_deadline'
        ];
        
        foreach ($fields as $field) {
            if ($request->has($field)) {
                $booking->{$field} = $request->input($field);
            }
        }
        
        $booking->save();

        return response()->json(['success' => true, 'booking' => $booking]);
    }

    public function deleteBooking($id)
    {
        $user = auth()->user() ?? \App\Models\User::first();
        if (!$user) return response()->json(['success' => false], 401);

        Booking::where('user_id', $user->id)->where('id', $id)->delete();
        return response()->json(['success' => true]);
    }
}
