<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use App\Models\OfficeWorkEvaluation;

class OfficeEvaluationController extends Controller
{
    /**
     * Get all office evaluations
     */
    public function getEvaluations(Request $request)
    {
        $user = Auth::user();
        $ownerId = $user ? $user->getEffectiveOwnerId() : 1;

        $evals = OfficeWorkEvaluation::where('user_id', $ownerId)
            ->with('booking')
            ->orderByDesc('created_at')
            ->get();

        $total = $evals->count();
        $onTime = $evals->where('edit_status', 'On Time')->count();
        $onTimePct = $total > 0 ? round(($onTime / $total) * 100) : 100;
        $avgScore = $total > 0 ? round($evals->avg('quality_score'), 1) : 5.0;

        return response()->json([
            'success' => true,
            'evaluations' => $evals,
            'stats' => [
                'total_jobs' => $total,
                'on_time_percentage' => $onTimePct,
                'avg_score' => $avgScore,
            ]
        ]);
    }

    /**
     * Store or update evaluation
     */
    public function storeEvaluation(Request $request)
    {
        $user = Auth::user();
        $ownerId = $user ? $user->getEffectiveOwnerId() : 1;

        $id = $request->input('id');
        $data = [
            'user_id' => $ownerId,
            'booking_id' => $request->input('booking_id'),
            'editor_name' => $request->input('editor_name'),
            'production_staff_name' => $request->input('production_staff_name'),
            'fast_edit_type' => $request->input('fast_edit_type', 'Normal'),
            'edit_status' => $request->input('edit_status', 'On Time'),
            'edit_completion_hours' => $request->input('edit_completion_hours'),
            'print_deadline_date' => $request->input('print_deadline_date'),
            'ready_to_pickup_date' => $request->input('ready_to_pickup_date'),
            'print_status' => $request->input('print_status', 'Ready to Pick Up'),
            'quality_score' => intval($request->input('quality_score', 5)),
            'office_notes' => $request->input('office_notes'),
        ];

        if ($id) {
            $eval = OfficeWorkEvaluation::where('user_id', $ownerId)->findOrFail($id);
            $eval->update($data);
        } else {
            $eval = OfficeWorkEvaluation::create($data);
        }

        return response()->json([
            'success' => true,
            'message' => 'Penilaian kinerja tim office berhasil disimpan!',
            'evaluation' => $eval,
        ]);
    }

    /**
     * Export evaluations as CSV
     */
    public function exportCsv(Request $request)
    {
        $user = Auth::user();
        $ownerId = $user ? $user->getEffectiveOwnerId() : 1;

        $evals = OfficeWorkEvaluation::where('user_id', $ownerId)->with('booking')->orderByDesc('created_at')->get();

        $csv = "No,Tanggal Event,Nama Klien,Editor,Staff Cetak/Produksi,Tipe Edit,Status Kecepatan,Durasi Jam,Deadline Cetak,Ready To Pick Up,Status Cetak,Skor Kualitas,Catatan Office\n";
        foreach ($evals as $idx => $e) {
            $num = $idx + 1;
            $eventDate = $e->booking ? ($e->booking->event_date ? $e->booking->event_date->format('Y-m-d') : '-') : '-';
            $client = $e->booking ? str_replace('"', '""', $e->booking->client_name) : '-';
            $editor = str_replace('"', '""', $e->editor_name);
            $prod = str_replace('"', '""', $e->production_staff_name);
            $type = str_replace('"', '""', $e->fast_edit_type);
            $editStatus = str_replace('"', '""', $e->edit_status);
            $printDeadline = $e->print_deadline_date ? $e->print_deadline_date->format('Y-m-d') : '-';
            $readyPickup = $e->ready_to_pickup_date ? $e->ready_to_pickup_date->format('Y-m-d') : '-';
            $printStatus = str_replace('"', '""', $e->print_status);
            $notes = str_replace('"', '""', $e->office_notes);

            $csv .= "{$num},\"{$eventDate}\",\"{$client}\",\"{$editor}\",\"{$prod}\",\"{$type}\",\"{$editStatus}\",{$e->edit_completion_hours},\"{$printDeadline}\",\"{$readyPickup}\",\"{$printStatus}\",{$e->quality_score},\"{$notes}\"\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="laporan-kinerja-office-' . date('Y-m-d') . '.csv"',
        ]);
    }

    /**
     * Delete evaluation
     */
    public function deleteEvaluation($id)
    {
        $user = Auth::user();
        $ownerId = $user ? $user->getEffectiveOwnerId() : 1;

        $eval = OfficeWorkEvaluation::where(function($q) use ($ownerId) {
            $q->where('user_id', $ownerId)->orWhereNull('user_id');
        })->find($id) ?: OfficeWorkEvaluation::findOrFail($id);
        $eval->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data evaluasi kerja kantor berhasil dihapus',
        ]);
    }
}

