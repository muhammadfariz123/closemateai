<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use App\Models\ClientReview;
use App\Models\User;

class ClientReviewController extends Controller
{
    /**
     * Show public rating page for client (Star rating ala Grab/Gojek)
     */
    public function showPublic($uuid)
    {
        $booking = Booking::where('uuid', $uuid)->first();
        if (!$booking) {
            $booking = Booking::where('id', $uuid)->first();
        }

        if (!$booking) {
            abort(404, 'Data booking tidak ditemukan.');
        }

        $owner = User::find($booking->user_id);
        $crewNames = is_array($booking->team_members) ? implode(', ', $booking->team_members) : ($booking->team_members ?: 'Tim Lapangan');

        return view('public_review', compact('booking', 'owner', 'crewNames'));
    }

    /**
     * Submit client rating
     */
    public function submitReview(Request $request, $uuid)
    {
        $booking = Booking::where('uuid', $uuid)->first();
        if (!$booking) {
            $booking = Booking::where('id', $uuid)->first();
        }

        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Booking tidak ditemukan'], 404);
        }

        $tags = $request->input('positive_tags');
        if (is_array($tags)) {
            $tags = implode(', ', $tags);
        }

        $review = ClientReview::create([
            'user_id' => $booking->user_id,
            'booking_id' => $booking->id,
            'booking_uuid' => $booking->uuid,
            'client_name' => $request->input('client_name', $booking->client_name),
            'client_phone' => $request->input('client_phone', $booking->client_phone),
            'field_crew_names' => $request->input('field_crew_names'),
            'rating_field_crew' => intval($request->input('rating_field_crew', 5)),
            'rating_punctuality' => intval($request->input('rating_punctuality', 5)),
            'rating_hospitality' => intval($request->input('rating_hospitality', 5)),
            'rating_overall' => intval($request->input('rating_overall', 5)),
            'positive_tags' => $tags,
            'review_comment' => $request->input('review_comment'),
            'is_public' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Terima kasih atas ulasan dan penilaian bintang yang Anda berikan!',
            'review' => $review,
        ]);
    }

    /**
     * Admin: Store review manually from dashboard
     */
    public function storeManual(Request $request)
    {
        $user = Auth::user();
        $ownerId = $user ? $user->getEffectiveOwnerId() : 1;

        $tags = $request->input('positive_tags', []);
        if (is_string($tags)) {
            $tags = array_filter(array_map('trim', explode(',', $tags)));
        }

        $review = ClientReview::create([
            'user_id' => $ownerId,
            'client_name' => $request->input('client_name', 'Klien Studio'),
            'client_phone' => $request->input('client_phone', '-'),
            'field_crew_names' => $request->input('field_crew_names', 'Tim Lapangan'),
            'rating_field_crew' => intval($request->input('rating_field_crew', 5)),
            'rating_punctuality' => intval($request->input('rating_punctuality', 5)),
            'rating_hospitality' => intval($request->input('rating_hospitality', 5)),
            'rating_overall' => intval($request->input('rating_overall', 5)),
            'positive_tags' => $tags,
            'review_comment' => $request->input('review_comment', 'Pelayanan sangat memuaskan'),
            'is_public' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ulasan kepuasan klien berhasil disimpan!',
            'review' => $review,
        ]);
    }

    /**
     * Admin: Get all reviews
     */
    public function getReviews(Request $request)
    {
        $user = Auth::user();
        $ownerId = $user ? $user->getEffectiveOwnerId() : 1;

        $reviews = ClientReview::where('user_id', $ownerId)
            ->with('booking')
            ->orderByDesc('created_at')
            ->get();

        $avgOverall = round($reviews->avg('rating_overall') ?: 5, 1);
        $avgPunctuality = round($reviews->avg('rating_punctuality') ?: 5, 1);
        $avgHospitality = round($reviews->avg('rating_hospitality') ?: 5, 1);

        return response()->json([
            'success' => true,
            'reviews' => $reviews,
            'stats' => [
                'total_reviews' => $reviews->count(),
                'avg_overall' => $avgOverall,
                'avg_punctuality' => $avgPunctuality,
                'avg_hospitality' => $avgHospitality,
            ]
        ]);
    }

    /**
     * Admin: Export reviews CSV
     */
    public function exportCsv(Request $request)
    {
        $user = Auth::user();
        $ownerId = $user ? $user->getEffectiveOwnerId() : 1;

        $reviews = ClientReview::where('user_id', $ownerId)->orderByDesc('created_at')->get();

        $csv = "No,Tanggal,Nama Klien,No WhatsApp,Kru Lapangan,Rating Kru,Ketepatan Waktu,Keramahan,Rating Keseluruhan,Kelebihan/Tags,Ulasan/Komentar\n";
        foreach ($reviews as $idx => $r) {
            $num = $idx + 1;
            $date = $r->created_at ? $r->created_at->format('Y-m-d H:i') : '-';
            $client = str_replace('"', '""', $r->client_name);
            $phone = str_replace('"', '""', $r->client_phone);
            $crew = str_replace('"', '""', $r->field_crew_names);
            $tags = str_replace('"', '""', $r->positive_tags);
            $comment = str_replace('"', '""', $r->review_comment);

            $csv .= "{$num},\"{$date}\",\"{$client}\",\"{$phone}\",\"{$crew}\",{$r->rating_field_crew},{$r->rating_punctuality},{$r->rating_hospitality},{$r->rating_overall},\"{$tags}\",\"{$comment}\"\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="laporan-penilaian-klien-' . date('Y-m-d') . '.csv"',
        ]);
    }

    /**
     * Delete review
     */
    public function deleteReview($id)
    {
        $user = Auth::user();
        $ownerId = $user ? $user->getEffectiveOwnerId() : 1;

        $review = ClientReview::where(function($q) use ($ownerId) {
            $q->where('user_id', $ownerId)->orWhereNull('user_id');
        })->find($id) ?: ClientReview::findOrFail($id);
        $review->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ulasan berhasil dihapus',
        ]);
    }
}

