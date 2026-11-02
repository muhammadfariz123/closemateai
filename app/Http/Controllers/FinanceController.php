<?php

namespace App\Http\Controllers;

use App\Models\FinancialExpense;
use App\Models\FinancialMasterSalary;
use App\Models\FinancialMonthlyOperation;
use App\Models\StockItem;
use App\Services\FinancialReportSeederService;
use App\Services\SpreadsheetMLReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FinanceController extends Controller
{
    private function getEffectiveOwnerUser()
    {
        $user = Auth::user();
        if (!$user) {
            // Fallback for direct testing/API
            return \App\Models\User::where('role', 'owner')->first() ?: \App\Models\User::first();
        }
        return $user->getEffectiveOwner();
    }

    public function index()
    {
        $owner = $this->getEffectiveOwnerUser();
        if ($owner) {
            FinancialReportSeederService::seedIfEmpty($owner->id);
        }
        return view('finance');
    }

    public function exportExcel(Request $request)
    {
        $owner = $this->getEffectiveOwnerUser();
        $ownerId = $owner ? $owner->id : 1;

        $startDate = $request->query('start');
        $endDate = $request->query('end');

        // Ensure defaults seeded
        FinancialReportSeederService::seedIfEmpty($ownerId);

        $service = new SpreadsheetMLReportService($ownerId, $startDate, $endDate);
        $xml = $service->generateXml();

        $filename = 'laporan-keuangan-penapict-' . date('Y-m-d') . '.xls';

        return response($xml, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0, no-cache, must-revalidate, proxy-revalidate',
        ]);
    }

    public function getExpenses(Request $request)
    {
        $owner = $this->getEffectiveOwnerUser();
        $ownerId = $owner ? $owner->id : 1;

        $expenses = FinancialExpense::where('user_id', $ownerId)
            ->orderBy('date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $expenses,
        ]);
    }

    public function storeExpense(Request $request)
    {
        $owner = $this->getEffectiveOwnerUser();
        $ownerId = $owner ? $owner->id : 1;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'date' => 'nullable|date',
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'booking_id' => 'nullable|integer',
        ]);

        if ($request->filled('id') && is_numeric($request->id)) {
            $expense = FinancialExpense::where('user_id', $ownerId)->where('id', $request->id)->first();
            if ($expense) {
                $expense->update($validated);
                return response()->json(['success' => true, 'data' => $expense]);
            }
        }

        $expense = FinancialExpense::create(array_merge($validated, [
            'user_id' => $ownerId,
            'date' => $validated['date'] ?? date('Y-m-d'),
        ]));

        return response()->json(['success' => true, 'data' => $expense]);
    }

    public function deleteExpense(Request $request, $id)
    {
        $owner = $this->getEffectiveOwnerUser();
        $ownerId = $owner ? $owner->id : 1;

        $expense = FinancialExpense::where('user_id', $ownerId)->where('id', $id)->first();
        if ($expense) {
            $expense->delete();
            return response()->json(['success' => true, 'message' => 'Expense deleted']);
        }

        return response()->json(['success' => false, 'message' => 'Not found'], 404);
    }

    public function getMasterSalaries(Request $request)
    {
        $owner = $this->getEffectiveOwnerUser();
        $ownerId = $owner ? $owner->id : 1;
        FinancialReportSeederService::seedIfEmpty($ownerId);

        $salaries = FinancialMasterSalary::where('user_id', $ownerId)->get();
        return response()->json(['success' => true, 'data' => $salaries]);
    }

    public function saveMasterSalaries(Request $request)
    {
        $owner = $this->getEffectiveOwnerUser();
        $ownerId = $owner ? $owner->id : 1;

        $items = $request->input('items', []);
        foreach ($items as $item) {
            if (!empty($item['id'])) {
                $sal = FinancialMasterSalary::where('user_id', $ownerId)->find($item['id']);
                if ($sal) {
                    $qty = isset($item['qty']) ? (float)$item['qty'] : (float)$sal->qty;
                    $price = isset($item['price']) ? (float)$item['price'] : (float)$sal->price;
                    $sal->update([
                        'name' => $item['name'] ?? $sal->name,
                        'position' => $item['position'] ?? $sal->position,
                        'qty' => $qty,
                        'price' => $price,
                        'total' => $qty * $price,
                    ]);
                }
            } elseif (!empty($item['name'])) {
                $qty = isset($item['qty']) ? (float)$item['qty'] : 1;
                $price = isset($item['price']) ? (float)$item['price'] : 0;
                FinancialMasterSalary::create([
                    'user_id' => $ownerId,
                    'division' => $item['division'] ?? 'studio',
                    'name' => $item['name'],
                    'position' => $item['position'] ?? null,
                    'qty' => $qty,
                    'price' => $price,
                    'total' => $qty * $price,
                ]);
            }
        }

        return response()->json(['success' => true, 'data' => FinancialMasterSalary::where('user_id', $ownerId)->get()]);
    }

    public function getMonthlyOperations(Request $request)
    {
        $owner = $this->getEffectiveOwnerUser();
        $ownerId = $owner ? $owner->id : 1;
        FinancialReportSeederService::seedIfEmpty($ownerId);

        $ops = FinancialMonthlyOperation::where('user_id', $ownerId)->get();
        return response()->json(['success' => true, 'data' => $ops]);
    }

    public function saveMonthlyOperations(Request $request)
    {
        $owner = $this->getEffectiveOwnerUser();
        $ownerId = $owner ? $owner->id : 1;

        $items = $request->input('items', []);
        foreach ($items as $item) {
            if (!empty($item['id'])) {
                $op = FinancialMonthlyOperation::where('user_id', $ownerId)->find($item['id']);
                if ($op) {
                    $qty = isset($item['qty']) ? (float)$item['qty'] : (float)$op->qty;
                    $price = isset($item['price']) ? (float)$item['price'] : (float)$op->price;
                    $op->update([
                        'name' => $item['name'] ?? $op->name,
                        'qty' => $qty,
                        'price' => $price,
                        'total' => $qty * $price,
                    ]);
                }
            } elseif (!empty($item['name'])) {
                $qty = isset($item['qty']) ? (float)$item['qty'] : 1;
                $price = isset($item['price']) ? (float)$item['price'] : 0;
                FinancialMonthlyOperation::create([
                    'user_id' => $ownerId,
                    'name' => $item['name'],
                    'qty' => $qty,
                    'price' => $price,
                    'total' => $qty * $price,
                ]);
            }
        }

        return response()->json(['success' => true, 'data' => FinancialMonthlyOperation::where('user_id', $ownerId)->get()]);
    }
}

