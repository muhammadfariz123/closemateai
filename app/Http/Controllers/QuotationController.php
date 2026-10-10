<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Quotation;
use App\Models\Booking;
use Illuminate\Support\Str;
use Carbon\Carbon;

class QuotationController extends Controller
{
    // API for vendor dashboard
    public function getQuotations()
    {
        $user = auth()->user() ?? \App\Models\User::first();
        if (!$user) return response()->json([]);

        return response()->json(Quotation::where('user_id', $user->id)->orderBy('created_at', 'desc')->get());
    }

    public function addQuotation(Request $request)
    {
        $user = auth()->user() ?? \App\Models\User::first();
        if (!$user) return response()->json(['success' => false], 401);

        $data = $request->all();
        // Since we had localStorage with string dates or empty dates, handle them
        if (empty($data['event_date'])) $data['event_date'] = null;
        if (empty($data['valid_until'])) $data['valid_until'] = null;
        
        if (empty($data['id']) || str_starts_with($data['id'], 'q_')) {
            $data['id'] = Str::uuid()->toString();
        }

        $data['user_id'] = $user->id;

        $quotation = Quotation::updateOrCreate(
            ['id' => $data['id']],
            $data
        );

        return response()->json($quotation);
    }

    public function deleteQuotation($id)
    {
        $user = auth()->user() ?? \App\Models\User::first();
        if (!$user) return response()->json(['success' => false], 401);

        Quotation::where('user_id', $user->id)->where('id', $id)->delete();
        return response()->json(['success' => true]);
    }

    // Public view for Client
    public function showPublic($id)
    {
        $quotation = Quotation::findOrFail($id);
        
        // Ensure some variables exist even if null
        return view('public_quotation', compact('quotation'));
    }

    // Approve API from client
    public function approveQuotation(Request $request, $id)
    {
        $quotation = Quotation::findOrFail($id);
        
        if ($quotation->status !== 'Disetujui') {
            $quotation->status = 'Disetujui';
            $quotation->save();
            
            // Generate Booking
            $booking = Booking::create([
                'user_id' => $quotation->user_id,
                'client_name' => $quotation->client,
                'client_wa_number' => $quotation->phone,
                'event_date' => $quotation->event_date,
                'package_name' => 'Penawaran ' . $quotation->q_no,
                'package_price' => $quotation->grandTotal,
                'package_qty' => 1,
                'discount' => 0,
                'total_income' => $quotation->grandTotal,
                'net_profit' => $quotation->grandTotal,
                'payment_status' => 'DP 1',
                'production_status' => 'Pre-Event',
                'paid_amount' => 0,
                'client_address' => $quotation->venue,
                'notes' => 'Dibuat dari penawaran ' . $quotation->q_no . ' \u00B7 Lokasi: ' . ($quotation->venue ?? ''),
                'addons' => [],
                'operational_costs' => [],
                'team_members' => [],
            ]);
        }
        
        return response()->json(['success' => true]);
    }
}
