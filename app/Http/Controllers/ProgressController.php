<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;

class ProgressController extends Controller
{
    public function showPublic($uuid)
    {
        $booking = Booking::where('uuid', $uuid)->firstOrFail();
        return view('progress', compact('booking'));
    }
}
