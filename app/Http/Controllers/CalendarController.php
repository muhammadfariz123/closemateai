<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;

class CalendarController extends Controller
{
    public function export($token)
    {
        $bookings = Booking::all();

        $ical = "BEGIN:VCALENDAR\r\n";
        $ical .= "VERSION:2.0\r\n";
        $ical .= "PRODID:-//CloseMateAI//Calendar Sync//EN\r\n";

        foreach ($bookings as $booking) {
            $ical .= "BEGIN:VEVENT\r\n";
            $ical .= "UID:booking-" . $booking->id . "@closemateai.id\r\n";
            $ical .= "DTSTAMP:" . gmdate('Ymd\THis\Z', strtotime($booking->created_at ?: now())) . "\r\n";
            
            if ($booking->event_date) {
                $startDate = date('Ymd', strtotime($booking->event_date));
                $endDate = date('Ymd', strtotime($booking->event_date . ' +1 day'));
                
                $ical .= "DTSTART;VALUE=DATE:" . $startDate . "\r\n";
                $ical .= "DTEND;VALUE=DATE:" . $endDate . "\r\n";
            }
            
            $summary = trim(($booking->client_name ?? 'Klien') . ' - ' . ($booking->package_name ?? 'Event'));
            $ical .= "SUMMARY:" . $this->escapeIcalString($summary) . "\r\n";
            
            if ($booking->notes) {
                $ical .= "DESCRIPTION:" . $this->escapeIcalString($booking->notes) . "\r\n";
            }
            if ($booking->client_address) {
                $ical .= "LOCATION:" . $this->escapeIcalString($booking->client_address) . "\r\n";
            }
            $ical .= "END:VEVENT\r\n";
        }

        $ical .= "END:VCALENDAR\r\n";

        return response($ical, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="closemateai_calendar.ics"',
        ]);
    }

    private function escapeIcalString($string)
    {
        $string = preg_replace('/([\,;])/', '\\\$1', $string);
        $string = str_replace(["\r\n", "\n", "\r"], "\\n", $string);
        return $string;
    }
}
