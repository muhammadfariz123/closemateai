<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Booking;
use App\Models\InvoicePackage;
use App\Models\SystemActivity;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class InvoiceController extends Controller
{
    protected function getUser()
    {
        return auth()->user() ?? \App\Models\User::first();
    }

    protected function ensureSchemaReady()
    {
        static $ready = false;
        if ($ready) return;
        $ready = true;

        try {
            if (config('database.default') === 'sqlite') {
                $dbPath = config('database.connections.sqlite.database');
                if ($dbPath) {
                    $dir = dirname($dbPath);
                    if (!is_dir($dir)) {
                        @mkdir($dir, 0777, true);
                    }
                    if (!file_exists($dbPath)) {
                        @touch($dbPath);
                    }
                    @chmod($dir, 0777);
                    @chmod($dbPath, 0666);
                }
            }

            $needsMigration = false;
            if (!Schema::hasTable('invoices')) {
                $needsMigration = true;
            }
            if (!Schema::hasColumn('users', 'bank_name')) {
                $needsMigration = true;
            }
            if (Schema::hasTable('invoices') && !Schema::hasColumn('invoices', 'payments')) {
                $needsMigration = true;
            }

            if ($needsMigration) {
                Artisan::call('migrate', ['--force' => true]);
            }
        } catch (\Throwable $e) {
            Log::error('Schema auto-migration error: ' . $e->getMessage());
        }
    }

    // List all invoices for vendor
    public function getInvoices()
    {
        $this->ensureSchemaReady();
        try {
            $user = $this->getUser();
            if (!$user) return response()->json([]);

            $invoices = Invoice::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json($invoices);
        } catch (\Throwable $e) {
            Log::error('getInvoices error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Get single invoice
    public function getInvoice($id)
    {
        $user = $this->getUser();
        if (!$user) return response()->json(['error' => 'Unauthorized'], 401);

        $invoice = Invoice::where('user_id', $user->id)->where('id', $id)->first();
        if (!$invoice) return response()->json(['error' => 'Not found'], 404);

        return response()->json($invoice);
    }

    // Save or update invoice with automatic calculations
    public function addInvoice(Request $request)
    {
        $this->ensureSchemaReady();
        try {
            $user = $this->getUser();
            if (!$user) return response()->json(['success' => false, 'error' => 'Unauthorized'], 401);

            $id = $request->input('id');
            if (empty($id) || str_starts_with($id, 'inv_temp_')) {
                $id = (string) Str::uuid();
            }

            // Server-side calculation
            $pkgPrice = (float) $this->parseNumber($request->input('package_price', 0));
            $pkgQty = (int) ($request->input('package_qty', 1) ?: 1);
            $discount = (float) $this->parseNumber($request->input('discount', 0));

            $extraPackages = $request->input('extra_packages', []);
            if (is_string($extraPackages)) {
                $extraPackages = json_decode($extraPackages, true) ?: [];
            }

            $addons = $request->input('addons', []);
            if (is_string($addons)) {
                $addons = json_decode($addons, true) ?: [];
            }

            // Subtotal package
            $subtotalPackage = $pkgPrice * $pkgQty;
            $totalDiscount = $discount;
            foreach ($extraPackages as $extra) {
                $p = (float) $this->parseNumber($extra['price'] ?? 0);
                $d = (float) $this->parseNumber($extra['discount'] ?? 0);
                $subtotalPackage += $p;
                $totalDiscount += $d;
            }

            // Total addons
            $totalAddon = 0;
            foreach ($addons as $addon) {
                $qty = (int) ($addon['qty'] ?? 1);
                if ($qty < 1) $qty = 1;
                $price = (float) $this->parseNumber($addon['price'] ?? 0);
                $totalAddon += ($qty * $price);
            }

            // Grand Total
            $grandTotal = $subtotalPackage + $totalAddon - $totalDiscount;

            // DP and remaining balance
            $dp1 = (float) $this->parseNumber($request->input('dp1_amount', 0));
            $dp2 = (float) $this->parseNumber($request->input('dp2_amount', 0));
            $totalDp = $dp1 + $dp2;
            $remaining = max(0, $grandTotal - $totalDp);

            $existing = Invoice::find($id);
            $payments = $request->has('payments') ? $request->input('payments') : ($existing ? $existing->payments : []);
            if (is_string($payments)) {
                $payments = json_decode($payments, true) ?: [];
            }

            if (!empty($payments)) {
                $paidFromPayments = 0;
                foreach ($payments as $p) {
                    $paidFromPayments += (float) $this->parseNumber($p['amount'] ?? 0);
                }
                if ($paidFromPayments > 0) {
                    $totalDp = $paidFromPayments;
                    $remaining = max(0, $grandTotal - $totalDp);
                }
            } elseif ($totalDp > 0) {
                $payments = [
                    [
                        'id' => (string) Str::uuid(),
                        'amount' => $totalDp,
                        'date' => $this->parseDate($request->input('invoice_date')) ?: Carbon::now()->toDateString(),
                        'note' => $request->input('termin_label', 'Uang Muka / DP 1'),
                    ]
                ];
            }

            $paymentStatus = 'Belum Dibayar';
            if ($remaining <= 0 && $grandTotal > 0) {
                $paymentStatus = 'Lunas';
            } elseif ($totalDp > 0) {
                $paymentStatus = 'Terbayar Sebagian';
            }

            $data = [
                'id' => $id,
                'user_id' => $user->id,
                'booking_id' => $request->input('booking_id'),
                'invoice_number' => $request->input('invoice_number', 'INV-' . date('Ym') . '-001'),
                'invoice_date' => $this->parseDate($request->input('invoice_date')),
                'vendor_name' => $request->input('vendor_name', $user->business_name ?? $user->name),
                'vendor_address' => $request->input('vendor_address'),
                'client_name' => $request->input('client_name'),
                'client_wa' => $request->input('client_wa'),
                'client_address' => $request->input('client_address'),
                'event_date' => $this->parseDate($request->input('event_date')),
                'termin_label' => $request->input('termin_label', 'Uang Muka / DP 1'),
                'package_name' => $request->input('package_name'),
                'package_price' => $pkgPrice,
                'package_qty' => $pkgQty,
                'discount' => $discount,
                'extra_packages' => $extraPackages,
                'addons' => $addons,
                'subtotal_package' => $subtotalPackage,
                'total_addon' => $totalAddon,
                'total_discount' => $totalDiscount,
                'grand_total' => $grandTotal,
                'dp1_amount' => $dp1,
                'dp2_amount' => $dp2,
                'total_dp' => $totalDp,
                'remaining_balance' => $remaining,
                'payments' => $payments,
                'payment_status' => $paymentStatus,
                'due_date' => $this->parseDate($request->input('due_date')),
                'bank_name' => $request->input('bank_name', 'BCA'),
                'bank_account_number' => $request->input('bank_account_number'),
                'bank_account_name' => $request->input('bank_account_name'),
                'terms_and_conditions' => $request->input('terms_and_conditions'),
                'theme' => $request->input('theme', 'theme-minimalist'),
                'status' => 'saved',
            ];

            $invoice = Invoice::updateOrCreate(
                ['id' => $id],
                $data
            );

            // Sync with Booking if connected
            if (!empty($data['booking_id'])) {
                $booking = Booking::where('user_id', $user->id)->find($data['booking_id']);
                if ($booking) {
                    $booking->paid_amount = $totalDp;
                    if ($totalDp >= $grandTotal && $grandTotal > 0) {
                        $booking->payment_status = 'Lunas';
                    } elseif ($dp2 > 0) {
                        $booking->payment_status = 'Termin 2';
                    } elseif ($dp1 > 0) {
                        $booking->payment_status = 'DP 1';
                    }
                    $booking->save();
                }
            }

            // Log system activity if class exists
            if (class_exists(SystemActivity::class)) {
                SystemActivity::create([
                    'user_id' => $user->id,
                    'type' => 'invoice_created',
                    'description' => "Menyimpan invoice {$invoice->invoice_number} untuk klien " . ($invoice->client_name ?: '-'),
                ]);
            }

            $count = Invoice::where('user_id', $user->id)->count();

            return response()->json([
                'success' => true,
                'invoice' => $invoice,
                'saved_count' => $count
            ]);
        } catch (\Throwable $e) {
            Log::error('addInvoice error: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // Mark invoice as paid
    public function markPaid($id)
    {
        $this->ensureSchemaReady();
        try {
            $user = $this->getUser();
            if (!$user) return response()->json(['success' => false, 'error' => 'Unauthorized'], 401);

            $invoice = Invoice::where('user_id', $user->id)->where('id', $id)->first();
            if (!$invoice) return response()->json(['error' => 'Not found'], 404);

            $prevTotalDp = (float) $invoice->total_dp;
            $grandTotal = (float) $invoice->grand_total;
            $unpaid = max(0, $grandTotal - $prevTotalDp);

            $payments = $invoice->payments ?: [];
            if ($unpaid > 0) {
                $payments[] = [
                    'id' => (string) Str::uuid(),
                    'amount' => $unpaid,
                    'date' => Carbon::now()->toDateString(),
                    'note' => 'Pelunasan',
                ];
            }

            $invoice->payments = $payments;
            $invoice->total_dp = $grandTotal;
            $invoice->remaining_balance = 0;
            $invoice->payment_status = 'Lunas';
            $invoice->save();

            if (!empty($invoice->booking_id)) {
                $booking = Booking::where('user_id', $user->id)->find($invoice->booking_id);
                if ($booking) {
                    $booking->paid_amount = $grandTotal;
                    $booking->payment_status = 'Lunas';
                    $booking->save();
                }
            }

            if (class_exists(SystemActivity::class)) {
                SystemActivity::create([
                    'user_id' => $user->id,
                    'type' => 'invoice_paid',
                    'description' => "Invoice {$invoice->invoice_number} ditandai lunas",
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Invoice ditandai lunas',
                'invoice' => $invoice
            ]);
        } catch (\Throwable $e) {
            Log::error('markPaid error: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // Record payment
    public function recordPayment(Request $request, $id)
    {
        $this->ensureSchemaReady();
        try {
            $user = $this->getUser();
            if (!$user) return response()->json(['success' => false, 'error' => 'Unauthorized'], 401);

            $invoice = Invoice::where('user_id', $user->id)->where('id', $id)->first();
            if (!$invoice) return response()->json(['error' => 'Not found'], 404);

            $amount = (float) $this->parseNumber($request->input('amount', 0));
            if ($amount <= 0) {
                return response()->json(['success' => false, 'error' => 'Nominal harus lebih dari 0'], 422);
            }

            $date = $request->input('date') ? $this->parseDate($request->input('date')) : Carbon::now()->toDateString();
            $note = $request->input('note', '');

            $payments = $invoice->payments ?: [];
            $newPayment = [
                'id' => (string) Str::uuid(),
                'amount' => $amount,
                'date' => $date ?: Carbon::now()->toDateString(),
                'note' => $note,
            ];
            $payments[] = $newPayment;

            $totalPaid = 0;
            foreach ($payments as $p) {
                $totalPaid += (float) ($p['amount'] ?? 0);
            }

            $grandTotal = (float) $invoice->grand_total;
            $remaining = max(0, $grandTotal - $totalPaid);

            $invoice->payments = $payments;
            $invoice->total_dp = $totalPaid;
            $invoice->remaining_balance = $remaining;

            $bookingStatus = 'DP 1';
            if ($remaining <= 0 && $grandTotal > 0) {
                $invoice->payment_status = 'Lunas';
                $bookingStatus = 'Lunas';
            } else {
                $invoice->payment_status = 'Terbayar Sebagian';
                $paymentCount = count($payments);
                if ($paymentCount >= 2) {
                    $bookingStatus = 'Termin ' . $paymentCount;
                } else {
                    $bookingStatus = 'DP 1';
                }
            }
            $invoice->save();

            if (!empty($invoice->booking_id)) {
                $booking = Booking::where('user_id', $user->id)->find($invoice->booking_id);
                if ($booking) {
                    $booking->paid_amount = $totalPaid;
                    $booking->payment_status = $bookingStatus;
                    $booking->save();
                }
            }

            if (class_exists(SystemActivity::class)) {
                SystemActivity::create([
                    'user_id' => $user->id,
                    'type' => 'invoice_payment_added',
                    'description' => "Mencatat pembayaran Rp " . number_format($amount, 0, ',', '.') . " untuk invoice {$invoice->invoice_number}",
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => "Pembayaran tercatat dan status Booking diperbarui menjadi {$bookingStatus}.",
                'booking_status' => $bookingStatus,
                'invoice' => $invoice
            ]);
        } catch (\Throwable $e) {
            Log::error('recordPayment error: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // Delete payment
    public function deletePayment($id, $paymentId)
    {
        $this->ensureSchemaReady();
        try {
            $user = $this->getUser();
            if (!$user) return response()->json(['success' => false, 'error' => 'Unauthorized'], 401);

            $invoice = Invoice::where('user_id', $user->id)->where('id', $id)->first();
            if (!$invoice) return response()->json(['error' => 'Not found'], 404);

            $payments = $invoice->payments ?: [];
            $filtered = [];
            foreach ($payments as $p) {
                if (($p['id'] ?? '') !== $paymentId) {
                    $filtered[] = $p;
                }
            }

            $totalPaid = 0;
            foreach ($filtered as $p) {
                $totalPaid += (float) ($p['amount'] ?? 0);
            }

            $grandTotal = (float) $invoice->grand_total;
            $remaining = max(0, $grandTotal - $totalPaid);

            $invoice->payments = $filtered;
            $invoice->total_dp = $totalPaid;
            $invoice->remaining_balance = $remaining;

            if ($totalPaid <= 0) {
                $invoice->payment_status = 'Belum Dibayar';
                $bookingStatus = 'Belum DP';
            } elseif ($remaining <= 0 && $grandTotal > 0) {
                $invoice->payment_status = 'Lunas';
                $bookingStatus = 'Lunas';
            } else {
                $invoice->payment_status = 'Terbayar Sebagian';
                $bookingStatus = count($filtered) >= 2 ? 'Termin ' . count($filtered) : 'DP 1';
            }
            $invoice->save();

            if (!empty($invoice->booking_id)) {
                $booking = Booking::where('user_id', $user->id)->find($invoice->booking_id);
                if ($booking) {
                    $booking->paid_amount = $totalPaid;
                    $booking->payment_status = $bookingStatus;
                    $booking->save();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran dihapus',
                'invoice' => $invoice
            ]);
        } catch (\Throwable $e) {
            Log::error('deletePayment error: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // Public view for /inv/{id}
    public function showPublic($id)
    {
        $invoice = Invoice::where('id', $id)->first();
        if (!$invoice) {
            $invoice = Invoice::where('invoice_number', $id)->first();
        }
        if (!$invoice) {
            abort(404, 'Invoice tidak ditemukan');
        }

        $user = \App\Models\User::find($invoice->user_id);

        return view('invoice-public', compact('invoice', 'user'));
    }

    // Export CSV
    public function exportCsv()
    {
        $user = $this->getUser();
        if (!$user) return response()->json(['error' => 'Unauthorized'], 401);

        $invoices = Invoice::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();

        $filename = "invoices-" . date('Ymd-His') . ".csv";
        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['No Invoice', 'Klien', 'WhatsApp', 'Tanggal Acara', 'Grand Total', 'Terbayar', 'Status', 'Dibuat'];

        $callback = function() use ($invoices, $columns) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF"); // UTF-8 BOM
            fputcsv($file, $columns);

            foreach ($invoices as $inv) {
                $grandTotal = round((float) ($inv->grand_total ?? 0));
                $totalPaid = round((float) ($inv->total_dp ?? 0));
                $rem = round((float) ($inv->remaining_balance ?? 0));
                
                $status = $inv->payment_status ?: 'Belum Dibayar';
                if ($rem <= 0 && $grandTotal > 0) {
                    $status = 'Lunas';
                } elseif ($totalPaid > 0 && $status !== 'Lunas') {
                    $status = 'Terbayar Sebagian';
                }

                $eventDateStr = '';
                if ($inv->event_date) {
                    try {
                        $eventDateStr = is_string($inv->event_date) ? Carbon::parse($inv->event_date)->format('d/m/Y') : $inv->event_date->format('d/m/Y');
                    } catch (\Throwable $e) {
                        $eventDateStr = (string) $inv->event_date;
                    }
                }

                $createdStr = '';
                if ($inv->created_at) {
                    try {
                        $createdStr = is_string($inv->created_at) ? Carbon::parse($inv->created_at)->format('m/d/Y') : $inv->created_at->format('m/d/Y');
                    } catch (\Throwable $e) {
                        $createdStr = date('m/d/Y');
                    }
                } else {
                    $createdStr = date('m/d/Y');
                }

                fputcsv($file, [
                    $inv->invoice_number,
                    $inv->client_name ?: '',
                    $inv->client_wa ?: '',
                    $eventDateStr,
                    $grandTotal,
                    $totalPaid,
                    $status,
                    $createdStr
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Duplicate invoice
    public function duplicateInvoice($id)
    {
        $this->ensureSchemaReady();
        try {
            $user = $this->getUser();
            if (!$user) return response()->json(['success' => false, 'error' => 'Unauthorized'], 401);

            $source = Invoice::where('user_id', $user->id)->where('id', $id)->first();
            if (!$source) {
                return response()->json(['success' => false, 'error' => 'Invoice sumber tidak ditemukan'], 404);
            }

            // Generate next invoice number
            $prefix = 'INV-' . date('Ym') . '-';
            $invoices = Invoice::where('user_id', $user->id)
                ->where('invoice_number', 'like', $prefix . '%')
                ->pluck('invoice_number');

            $maxSeq = 0;
            foreach ($invoices as $num) {
                $parts = explode('-', $num);
                $last = end($parts);
                if (is_numeric($last)) {
                    $seq = (int) $last;
                    if ($seq > $maxSeq) {
                        $maxSeq = $seq;
                    }
                }
            }
            $nextSeq = $maxSeq + 1;
            $newNumber = $prefix . sprintf('%03d', $nextSeq);

            $newId = (string) Str::uuid();
            $grandTotal = (float) $source->grand_total;

            $newInvoice = Invoice::create([
                'id' => $newId,
                'user_id' => $user->id,
                'booking_id' => $source->booking_id,
                'invoice_number' => $newNumber,
                'invoice_date' => Carbon::now()->toDateString(),
                'vendor_name' => $source->vendor_name ?: ($user->business_name ?? $user->name),
                'vendor_address' => $source->vendor_address,
                'client_name' => $source->client_name,
                'client_wa' => $source->client_wa,
                'client_address' => $source->client_address,
                'event_date' => $source->event_date,
                'termin_label' => $source->termin_label ?: 'Uang Muka / DP 1',
                'package_name' => $source->package_name,
                'package_price' => $source->package_price,
                'package_qty' => $source->package_qty ?: 1,
                'discount' => $source->discount ?: 0,
                'extra_packages' => $source->extra_packages ?: [],
                'addons' => $source->addons ?: [],
                'subtotal_package' => $source->subtotal_package,
                'total_addon' => $source->total_addon ?: 0,
                'total_discount' => $source->total_discount ?: 0,
                'grand_total' => $grandTotal,
                'dp1_amount' => $source->dp1_amount ?: 0,
                'dp2_amount' => $source->dp2_amount ?: 0,
                'total_dp' => $source->total_dp !== null ? $source->total_dp : 0,
                'remaining_balance' => $source->remaining_balance !== null ? $source->remaining_balance : $grandTotal,
                'payments' => $source->payments ?: [],
                'payment_status' => $source->payment_status ?: 'Belum Dibayar',
                'due_date' => $source->due_date,
                'bank_name' => $source->bank_name ?: 'BCA',
                'bank_account_number' => $source->bank_account_number,
                'bank_account_name' => $source->bank_account_name,
                'terms_and_conditions' => $source->terms_and_conditions,
                'theme' => $source->theme ?: 'theme-minimalist',
                'status' => 'saved',
            ]);

            if (class_exists(SystemActivity::class)) {
                SystemActivity::create([
                    'user_id' => $user->id,
                    'type' => 'invoice_created',
                    'description' => "Menduplikasi invoice {$source->invoice_number} menjadi {$newNumber}",
                ]);
            }

            $count = Invoice::where('user_id', $user->id)->count();

            return response()->json([
                'success' => true,
                'message' => "Invoice diduplikat sebagai {$newNumber}",
                'invoice_number' => $newNumber,
                'invoice' => $newInvoice,
                'saved_count' => $count
            ]);
        } catch (\Throwable $e) {
            Log::error('duplicateInvoice error: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // Delete invoice
    public function deleteInvoice($id)
    {
        $user = $this->getUser();
        if (!$user) return response()->json(['success' => false], 401);

        Invoice::where('user_id', $user->id)->where('id', $id)->delete();
        $count = Invoice::where('user_id', $user->id)->count();

        return response()->json([
            'success' => true,
            'saved_count' => $count
        ]);
    }

    // Save vendor bank details to user profile
    public function saveBank(Request $request)
    {
        $this->ensureSchemaReady();
        try {
            $user = $this->getUser();
            if (!$user) return response()->json(['success' => false, 'error' => 'Unauthorized'], 401);

            $user->bank_name = $request->input('bank_name', 'BCA');
            $user->bank_account_number = $request->input('bank_account_number');
            $user->bank_account_name = $request->input('bank_account_name');
            $user->save();

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            Log::error('saveBank error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Save terms & conditions
    public function saveTerms(Request $request)
    {
        $this->ensureSchemaReady();
        try {
            $user = $this->getUser();
            if (!$user) return response()->json(['success' => false, 'error' => 'Unauthorized'], 401);

            $terms = $request->input('terms', []);
            $user->default_terms = is_array($terms) ? implode("\n", $terms) : (string)$terms;
            $user->save();

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            Log::error('saveTerms error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Get settings
    public function getSettings()
    {
        $this->ensureSchemaReady();
        try {
            $user = $this->getUser();
            if (!$user) return response()->json([]);

            $count = 0;
            try {
                $count = Invoice::where('user_id', $user->id)->count();
            } catch (\Throwable $e) {}

            return response()->json([
                'bank_name' => $user->bank_name ?: 'BCA',
                'bank_account_number' => $user->bank_account_number ?: '0431063867',
                'bank_account_name' => $user->bank_account_name ?: 'Umi Salamah',
                'default_terms' => is_string($user->default_terms) ? explode("\n", $user->default_terms) : ($user->default_terms ?: []),
                'business_name' => $user->business_name ?: $user->name,
                'vendor_address' => $user->vendor_address ?: 'Kombas purwokerto',
                'invoice_format' => $user->invoice_format ?: 'INV-{YYYY}{MM}-{SEQ3}',
                'saved_count' => $count,
            ]);
        } catch (\Throwable $e) {
            Log::error('getSettings error: ' . $e->getMessage());
            return response()->json([
                'bank_name' => 'BCA',
                'bank_account_number' => '0431063867',
                'bank_account_name' => 'Umi Salamah',
                'default_terms' => [],
                'business_name' => 'CloseMateAI',
                'vendor_address' => 'Kombas purwokerto',
                'invoice_format' => 'INV-{YYYY}{MM}-{SEQ3}',
                'saved_count' => 0,
            ]);
        }
    }

    public function getPackages()
    {
        $this->ensureSchemaReady();
        try {
            $user = $this->getUser();
            if (!$user) return response()->json([]);

            $packages = InvoicePackage::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();

            if ($packages->isEmpty()) {
                $defaults = [
                    ['name' => 'paket 111', 'price' => 9000, 'discount' => 0],
                    ['name' => 'Penawaran QT-202610-236', 'price' => 9000, 'discount' => 0],
                    ['name' => 'Silver Package', 'price' => 15000000, 'discount' => 0],
                ];
                foreach ($defaults as $def) {
                    InvoicePackage::create([
                        'user_id' => $user->id,
                        'name' => $def['name'],
                        'price' => $def['price'],
                        'discount' => $def['discount'],
                    ]);
                }
                $packages = InvoicePackage::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
            }

            return response()->json($packages);
        } catch (\Throwable $e) {
            Log::error('getPackages error: ' . $e->getMessage());
            return response()->json([]);
        }
    }

    public function savePackage(Request $request)
    {
        $this->ensureSchemaReady();
        try {
            $user = $this->getUser();
            if (!$user) return response()->json(['success' => false, 'error' => 'Unauthorized'], 401);

            $name = trim((string)$request->input('name', ''));
            if (empty($name)) {
                return response()->json(['success' => false, 'error' => 'Nama paket tidak boleh kosong'], 400);
            }
            $price = (float) $this->parseNumber($request->input('price', 0));
            $discount = (float) $this->parseNumber($request->input('discount', 0));

            $pkg = InvoicePackage::updateOrCreate(
                ['user_id' => $user->id, 'name' => $name],
                ['price' => $price, 'discount' => $discount]
            );

            return response()->json(['success' => true, 'package' => $pkg]);
        } catch (\Throwable $e) {
            Log::error('savePackage error: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function deletePackage($id)
    {
        $this->ensureSchemaReady();
        try {
            $user = $this->getUser();
            if (!$user) return response()->json(['success' => false, 'error' => 'Unauthorized'], 401);

            InvoicePackage::where('user_id', $user->id)->where('id', $id)->delete();

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            Log::error('deletePackage error: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function saveBusiness(Request $request)
    {
        $this->ensureSchemaReady();
        try {
            $user = $this->getUser();
            if (!$user) return response()->json(['success' => false, 'error' => 'Unauthorized'], 401);

            if ($request->has('vendor_name')) {
                $user->business_name = $request->input('vendor_name');
            }
            if ($request->has('vendor_address')) {
                $user->vendor_address = $request->input('vendor_address');
            }
            $user->save();

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            Log::error('saveBusiness error: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function saveFormat(Request $request)
    {
        $this->ensureSchemaReady();
        try {
            $user = $this->getUser();
            if (!$user) return response()->json(['success' => false, 'error' => 'Unauthorized'], 401);

            $user->invoice_format = $request->input('format', 'INV-{YYYY}{MM}-{SEQ3}');
            $user->save();

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            Log::error('saveFormat error: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    protected function parseNumber($val)
    {
        if (is_numeric($val)) return (float) $val;
        if (empty($val)) return 0;
        return (float) preg_replace('/[^0-9]/', '', (string) $val);
    }

    protected function parseDate($val)
    {
        if (empty($val)) return null;
        try {
            if (str_contains($val, '/')) {
                // mm/dd/yyyy or dd/mm/yyyy
                $parts = explode('/', $val);
                if (count($parts) === 3) {
                    if (strlen($parts[2]) === 4) {
                        return Carbon::createFromDate($parts[2], $parts[0], $parts[1])->toDateString();
                    }
                }
            }
            return Carbon::parse($val)->toDateString();
        } catch (\Exception $e) {
            return null;
        }
    }
}
