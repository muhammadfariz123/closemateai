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
        return response()->json(Quotation::orderBy('created_at', 'desc')->get());
    }

    public function addQuotation(Request $request)
    {
        $data = $request->all();
        // Since we had localStorage with string dates or empty dates, handle them
        if (empty($data['event_date'])) $data['event_date'] = null;
        if (empty($data['valid_until'])) $data['valid_until'] = null;
        
        if (empty($data['id']) || str_starts_with($data['id'], 'q_')) {
            $data['id'] = Str::uuid()->toString();
        }

        $quotation = Quotation::updateOrCreate(
            ['id' => $data['id']],
            $data
        );

        return response()->json($quotation);
    }

    public function deleteQuotation($id)
    {
        Quotation::where('id', $id)->delete();
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
            
            // Get first user for default user_id if needed
            $user = \App\Models\User::first();
            
            // Generate Booking
            $booking = Booking::create([
                'user_id' => $user ? $user->id : null,
                'client_name' => $quotation->client,
                'client_wa_number' => $quotation->phone,
                'event_date' => $quotation->event_date,
                'package_name' => 'Penawaran ' . $quotation->q_no,
                'package_price' => $quotation->grandTotal,
                'payment_status' => 'Belum Lunas',
                'production_status' => 'Persiapan',
                'paid_amount' => 0,
                'client_address' => $quotation->venue,
            ]);
        }
        
        return response()->json(['success' => true]);
    }
}
