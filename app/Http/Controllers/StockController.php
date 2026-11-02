<?php

namespace App\Http\Controllers;

use App\Models\ProductionBalance;
use App\Models\ProductionBalanceLog;
use App\Models\StockItem;
use App\Models\StockOpname;
use App\Models\StockTransaction;
use App\Models\SystemActivity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class StockController extends Controller
{
    private function getEffectiveOwnerUser()
    {
        $user = Auth::user();
        if (!$user) {
            return null;
        }
        return $user->getEffectiveOwner();
    }

    private function ensureDefaults($ownerId)
    {
        // Seed initial photography items if workspace has no items yet
        if (StockItem::where('user_id', $ownerId)->count() === 0) {
            $defaults = [
                ['code' => 'ALB-01', 'name' => 'Album Foto', 'category' => 'Album', 'stock_system' => 10, 'unit' => 'unit', 'price' => 150000, 'min_stock' => 3, 'notes' => 'Album hard cover standar'],
                ['code' => 'GK-01', 'name' => 'Gantungan Kunci', 'category' => 'Aksesoris', 'stock_system' => 30, 'unit' => 'unit', 'price' => 10000, 'min_stock' => 10, 'notes' => 'Souvenir gantungan kunci foto'],
                ['code' => 'FD-08', 'name' => 'Flashdisk 8 GB', 'category' => 'Flashdisk', 'stock_system' => 15, 'unit' => 'unit', 'price' => 45000, 'min_stock' => 5, 'notes' => 'USB 2.0 / 3.0 kayu/kartu'],
                ['code' => 'FD-16', 'name' => 'Flashdisk 16 GB', 'category' => 'Flashdisk', 'stock_system' => 10, 'unit' => 'unit', 'price' => 60000, 'min_stock' => 5, 'notes' => 'USB 3.0 standar packaging'],
                ['code' => 'FD-32', 'name' => 'Flashdisk 32 GB', 'category' => 'Flashdisk', 'stock_system' => 8, 'unit' => 'unit', 'price' => 85000, 'min_stock' => 3, 'notes' => 'USB 3.0 high speed transfer'],
                ['code' => 'BX-01', 'name' => 'Box Item Kayu', 'category' => 'Packaging', 'stock_system' => 12, 'unit' => 'unit', 'price' => 75000, 'min_stock' => 4, 'notes' => 'Box kayu exclusive grafir'],
            ];
            foreach ($defaults as $d) {
                StockItem::create(array_merge($d, ['user_id' => $ownerId]));
            }
        }

        // Ensure production balance record exists
        ProductionBalance::firstOrCreate(
            ['user_id' => $ownerId],
            ['balance' => 0]
        );
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect('/login');
        }

        $owner = $this->getEffectiveOwnerUser();
        $ownerId = $user->getEffectiveOwnerId();
        $this->ensureDefaults($ownerId);

        $isProduction = $user->isProduction();
        $isOwner = $user->isOwner();

        // Balance
        $balanceRecord = ProductionBalance::firstOrCreate(['user_id' => $ownerId], ['balance' => 0]);
        $currentBalance = $balanceRecord->balance;

        // Stats
        $totalItems = StockItem::where('user_id', $ownerId)->count();
        $problematicStockCount = StockItem::where('user_id', $ownerId)
            ->where(function ($q) {
                $q->where('stock_system', '<=', 0)
                  ->orWhereRaw('stock_system <= min_stock');
            })->count();

        $pendingOpnamesCount = StockOpname::where('user_id', $ownerId)->where('status', 'pending')->count();
        $pendingPurchasesCount = StockTransaction::where('user_id', $ownerId)->where('type', 'purchase')->where('status', 'pending')->count();

        // Opname stats
        $checkedItemsCount = StockOpname::where('user_id', $ownerId)->where('status', 'approved')->distinct('stock_item_id')->count('stock_item_id');
        $needCheckCount = max(0, $totalItems - $checkedItemsCount);

        // Recent items
        $items = StockItem::where('user_id', $ownerId)->orderBy('name', 'asc')->get();
        $opnames = StockOpname::where('user_id', $ownerId)->with(['item', 'inspector', 'verifier'])->latest()->take(50)->get();
        $purchases = StockTransaction::where('user_id', $ownerId)->where('type', 'purchase')->with(['item', 'creator', 'verifier'])->latest()->take(50)->get();
        $balanceLogs = ProductionBalanceLog::where('user_id', $ownerId)->with('creator')->latest()->take(50)->get();

        $activeTab = $request->query('tab', $isProduction ? 'dashboard' : 'stock');

        return view('stock', compact(
            'user',
            'owner',
            'isProduction',
            'isOwner',
            'currentBalance',
            'totalItems',
            'problematicStockCount',
            'pendingOpnamesCount',
            'pendingPurchasesCount',
            'needCheckCount',
            'items',
            'opnames',
            'purchases',
            'balanceLogs',
            'activeTab'
        ));
    }

    public function getItems(Request $request)
    {
        $user = Auth::user();
        $ownerId = $user->getEffectiveOwnerId();
        $this->ensureDefaults($ownerId);

        $items = StockItem::where('user_id', $ownerId)
            ->orderBy('name', 'asc')
            ->get()
            ->map(function ($item) {
                $item->status_label = $item->stock_status;
                return $item;
            });

        return response()->json(['success' => true, 'data' => $items]);
    }

    public function saveItem(Request $request, $id = null)
    {
        $user = Auth::user();
        $ownerId = $user->getEffectiveOwnerId();

        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
            'unit' => 'nullable|string|max:50',
            'price' => 'nullable|numeric|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'stock_system' => 'nullable|integer',
            'notes' => 'nullable|string',
        ]);

        $item = null;
        if ($id) {
            $item = StockItem::where('user_id', $ownerId)->where('id', $id)->first();
            if (!$item) {
                return response()->json(['success' => false, 'error' => 'Barang tidak ditemukan.'], 404);
            }
        }

        $code = trim((string)$request->input('code'));
        if (empty($code) && !$item) {
            $count = StockItem::where('user_id', $ownerId)->count() + 1;
            $code = 'ITM-' . str_pad($count, 3, '0', STR_PAD_LEFT);
        }

        $data = [
            'name' => trim($request->input('name')),
            'category' => trim($request->input('category', 'Perlengkapan')) ?: 'Perlengkapan',
            'unit' => trim($request->input('unit', 'unit')) ?: 'unit',
            'price' => (float)$request->input('price', 0),
            'min_stock' => (int)$request->input('min_stock', 5),
            'notes' => $request->input('notes'),
        ];

        if (!empty($code)) {
            $data['code'] = $code;
        }

        // Only owner can directly set system stock through item edit, or if creating brand new item
        if ($user->isOwner() || !$item) {
            $data['stock_system'] = (int)$request->input('stock_system', 0);
        }

        if ($item) {
            $item->update($data);
            $actionMsg = 'Barang berhasil diperbarui.';
        } else {
            $data['user_id'] = $ownerId;
            $item = StockItem::create($data);
            $actionMsg = 'Barang baru berhasil ditambahkan.';
        }

        if (class_exists(SystemActivity::class)) {
            SystemActivity::create([
                'user_id' => $ownerId,
                'type' => 'stock_item_saved',
                'description' => "{$user->name} menyimpan barang: {$item->name} (Stok: {$item->stock_system} {$item->unit})",
            ]);
        }

        $item->status_label = $item->stock_status;

        return response()->json([
            'success' => true,
            'message' => $actionMsg,
            'data' => $item,
        ]);
    }

    public function deleteItem($id)
    {
        $user = Auth::user();
        if (!$user->isOwner()) {
            return response()->json(['success' => false, 'error' => 'Hanya Owner yang dapat menghapus barang.'], 403);
        }

        $ownerId = $user->getEffectiveOwnerId();
        $item = StockItem::where('user_id', $ownerId)->where('id', $id)->first();
        if (!$item) {
            return response()->json(['success' => false, 'error' => 'Barang tidak ditemukan.'], 404);
        }

        $name = $item->name;
        $item->delete();

        if (class_exists(SystemActivity::class)) {
            SystemActivity::create([
                'user_id' => $ownerId,
                'type' => 'stock_item_deleted',
                'description' => "Owner menghapus barang persediaan: {$name}",
            ]);
        }

        return response()->json(['success' => true, 'message' => "Barang {$name} berhasil dihapus."]);
    }

    public function getOpnames(Request $request)
    {
        $user = Auth::user();
        $ownerId = $user->getEffectiveOwnerId();

        $query = StockOpname::where('user_id', $ownerId)->with(['item', 'inspector', 'verifier'])->latest();

        if ($request->has('status') && in_array($request->status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $request->status);
        }

        $opnames = $query->take(100)->get();

        return response()->json(['success' => true, 'data' => $opnames]);
    }

    public function submitOpname(Request $request)
    {
        $user = Auth::user();
        $ownerId = $user->getEffectiveOwnerId();

        $request->validate([
            'stock_item_id' => 'required|integer',
            'physical_stock' => 'required|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $item = StockItem::where('user_id', $ownerId)->where('id', $request->stock_item_id)->first();
        if (!$item) {
            return response()->json(['success' => false, 'error' => 'Barang tidak ditemukan di inventaris.'], 404);
        }

        $systemStock = $item->stock_system;
        $physicalStock = (int)$request->input('physical_stock');
        $difference = $physicalStock - $systemStock;

        // If submitted by owner directly, auto-approve immediately
        $isOwner = $user->isOwner();
        $status = $isOwner ? 'approved' : 'pending';

        $opname = StockOpname::create([
            'user_id' => $ownerId,
            'inspector_id' => $user->id,
            'stock_item_id' => $item->id,
            'system_stock' => $systemStock,
            'physical_stock' => $physicalStock,
            'difference' => $difference,
            'notes' => $request->input('notes'),
            'status' => $status,
            'verified_by' => $isOwner ? $user->id : null,
            'verified_at' => $isOwner ? now() : null,
        ]);

        // If owner submitted, apply adjustment immediately
        if ($isOwner) {
            $item->stock_system = $physicalStock;
            $item->save();

            StockTransaction::create([
                'user_id' => $ownerId,
                'stock_item_id' => $item->id,
                'item_name' => $item->name,
                'type' => 'opname_adjustment',
                'qty' => $difference,
                'unit_price' => $item->price,
                'total_price' => abs($difference) * $item->price,
                'notes' => "Penyesuaian Stock Opname langsung oleh Owner: " . ($request->input('notes') ?: 'Pemeriksaan fisik sesuai'),
                'status' => 'approved',
                'created_by' => $user->id,
                'verified_by' => $user->id,
                'verified_at' => now(),
            ]);
        }

        if (class_exists(SystemActivity::class)) {
            $statusText = $isOwner ? 'dan stok disesuaikan' : 'menunggu verifikasi Owner';
            SystemActivity::create([
                'user_id' => $ownerId,
                'type' => 'stock_opname_submitted',
                'description' => "{$user->name} melakukan Stock Opname pada {$item->name} (Fisik: {$physicalStock}, Selisih: {$difference}) - {$statusText}",
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => $isOwner
                ? 'Stock Opname berhasil disimpan dan stok sistem telah diperbarui.'
                : 'Hasil pemeriksaan berhasil dikirim! Menunggu verifikasi dari Owner.',
            'data' => $opname->load(['item', 'inspector']),
        ]);
    }

    public function verifyOpname(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->isOwner()) {
            return response()->json(['success' => false, 'error' => 'Hanya Owner yang dapat memverifikasi Stock Opname.'], 403);
        }

        $ownerId = $user->getEffectiveOwnerId();
        $opname = StockOpname::where('user_id', $ownerId)->where('id', $id)->first();
        if (!$opname) {
            return response()->json(['success' => false, 'error' => 'Data Stock Opname tidak ditemukan.'], 404);
        }

        $action = $request->input('action'); // 'approve' or 'reject'
        if (!in_array($action, ['approve', 'reject'])) {
            return response()->json(['success' => false, 'error' => 'Aksi verifikasi tidak valid.'], 422);
        }

        if ($action === 'approve') {
            $opname->status = 'approved';
            $opname->verified_by = $user->id;
            $opname->verified_at = now();
            $opname->rejection_reason = null;
            $opname->save();

            // Adjust stock system to match physical stock
            $item = StockItem::find($opname->stock_item_id);
            if ($item) {
                $item->stock_system = $opname->physical_stock;
                $item->save();

                // Record adjustment transaction
                StockTransaction::create([
                    'user_id' => $ownerId,
                    'stock_item_id' => $item->id,
                    'item_name' => $item->name,
                    'type' => 'opname_adjustment',
                    'qty' => $opname->difference,
                    'unit_price' => $item->price,
                    'total_price' => abs($opname->difference) * $item->price,
                    'notes' => "Verifikasi Stock Opname disetujui Owner: " . ($opname->notes ?: 'Pemeriksaan fisik sesuai'),
                    'status' => 'approved',
                    'created_by' => $opname->inspector_id,
                    'verified_by' => $user->id,
                    'verified_at' => now(),
                ]);
            }

            if (class_exists(SystemActivity::class)) {
                SystemActivity::create([
                    'user_id' => $ownerId,
                    'type' => 'stock_opname_approved',
                    'description' => "Owner menyetujui Stock Opname {$opname->item->name} (Stok sistem disesuaikan menjadi {$opname->physical_stock} unit)",
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => "Stock Opname {$opname->item->name} disetujui! Stok sistem otomatis disesuaikan.",
                'data' => $opname->load(['item', 'inspector', 'verifier']),
            ]);
        } else {
            $reason = trim((string)$request->input('reason', 'Pemeriksaan fisik belum sesuai atau perlu dicek ulang'));
            $opname->status = 'rejected';
            $opname->verified_by = $user->id;
            $opname->verified_at = now();
            $opname->rejection_reason = $reason;
            $opname->save();

            if (class_exists(SystemActivity::class)) {
                SystemActivity::create([
                    'user_id' => $ownerId,
                    'type' => 'stock_opname_rejected',
                    'description' => "Owner menolak Stock Opname {$opname->item->name} (Alasan: {$reason})",
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => "Stock Opname ditolak. Alasan tersimpan untuk divisi produksi.",
                'data' => $opname->load(['item', 'inspector', 'verifier']),
            ]);
        }
    }

    public function getPurchases(Request $request)
    {
        $user = Auth::user();
        $ownerId = $user->getEffectiveOwnerId();

        $purchases = StockTransaction::where('user_id', $ownerId)
            ->where('type', 'purchase')
            ->with(['item', 'creator', 'verifier'])
            ->latest()
            ->take(100)
            ->get();

        return response()->json(['success' => true, 'data' => $purchases]);
    }

    public function submitPurchase(Request $request)
    {
        $user = Auth::user();
        $ownerId = $user->getEffectiveOwnerId();

        $request->validate([
            'stock_item_id' => 'nullable|integer',
            'item_name' => 'nullable|string|max:255',
            'qty' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:10240',
        ]);

        $itemId = $request->input('stock_item_id');
        $itemName = trim((string)$request->input('item_name'));

        $item = null;
        if ($itemId) {
            $item = StockItem::where('user_id', $ownerId)->where('id', $itemId)->first();
            if ($item && empty($itemName)) {
                $itemName = $item->name;
            }
        }

        if (empty($itemName) && !$item) {
            return response()->json(['success' => false, 'error' => 'Harap masukkan nama barang yang dibeli.'], 422);
        }

        $qty = (int)$request->input('qty');
        $unitPrice = (float)$request->input('unit_price');
        $totalPrice = $qty * $unitPrice;

        // Handle receipt image upload
        $receiptPath = null;
        if ($request->hasFile('receipt')) {
            $file = $request->file('receipt');
            $uploadDir = public_path('uploads/receipts');
            if (!File::isDirectory($uploadDir)) {
                File::makeDirectory($uploadDir, 0755, true, true);
            }
            $ext = $file->getClientOriginalExtension() ?: 'jpg';
            $filename = 'receipt_' . time() . '_' . Str::random(8) . '.' . $ext;
            $file->move($uploadDir, $filename);
            $receiptPath = '/uploads/receipts/' . $filename;
        }

        $isOwner = $user->isOwner();
        $status = $isOwner ? 'approved' : 'pending';

        $trx = StockTransaction::create([
            'user_id' => $ownerId,
            'stock_item_id' => $item ? $item->id : null,
            'item_name' => $itemName,
            'type' => 'purchase',
            'qty' => $qty,
            'unit_price' => $unitPrice,
            'total_price' => $totalPrice,
            'receipt_file' => $receiptPath,
            'notes' => $request->input('notes'),
            'status' => $status,
            'created_by' => $user->id,
            'verified_by' => $isOwner ? $user->id : null,
            'verified_at' => $isOwner ? now() : null,
        ]);

        // If owner submitted directly, automatically apply stock & balance deduction
        if ($isOwner) {
            if ($item) {
                $item->stock_system += $qty;
                $item->save();
            }

            $bal = ProductionBalance::firstOrCreate(['user_id' => $ownerId], ['balance' => 0]);
            $bal->balance -= $totalPrice;
            $bal->save();

            ProductionBalanceLog::create([
                'user_id' => $ownerId,
                'type' => 'purchase',
                'amount' => -$totalPrice,
                'balance_after' => $bal->balance,
                'notes' => "Pembelian langsung oleh Owner: {$itemName} ({$qty} unit)",
                'created_by' => $user->id,
                'reference_id' => $trx->id,
            ]);
        }

        if (class_exists(SystemActivity::class)) {
            $msg = $isOwner
                ? "Owner mencatat pembelian: {$itemName} ({$qty} unit @ Rp " . number_format($unitPrice, 0, ',', '.') . ")"
                : "{$user->name} mengajukan pembelian produksi: {$itemName} ({$qty} unit, Total Rp " . number_format($totalPrice, 0, ',', '.') . ") - Menunggu Verifikasi";

            SystemActivity::create([
                'user_id' => $ownerId,
                'type' => 'stock_purchase_submitted',
                'description' => $msg,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => $isOwner
                ? 'Transaksi pembelian berhasil disimpan dan stok otomatis bertambah.'
                : 'Pengajuan pembelian berhasil dikirim! Menunggu verifikasi dari Owner.',
            'data' => $trx->load(['item', 'creator']),
        ]);
    }

    public function verifyPurchase(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user->isOwner()) {
            return response()->json(['success' => false, 'error' => 'Hanya Owner yang dapat memverifikasi transaksi pembelian.'], 403);
        }

        $ownerId = $user->getEffectiveOwnerId();
        $trx = StockTransaction::where('user_id', $ownerId)->where('id', $id)->first();
        if (!$trx) {
            return response()->json(['success' => false, 'error' => 'Transaksi pembelian tidak ditemukan.'], 404);
        }

        $action = $request->input('action'); // 'approve' or 'reject'
        if (!in_array($action, ['approve', 'reject'])) {
            return response()->json(['success' => false, 'error' => 'Aksi verifikasi tidak valid.'], 422);
        }

        if ($action === 'approve') {
            $trx->status = 'approved';
            $trx->verified_by = $user->id;
            $trx->verified_at = now();
            $trx->rejection_reason = null;
            $trx->save();

            // Increase stock
            if ($trx->stock_item_id) {
                $item = StockItem::find($trx->stock_item_id);
                if ($item) {
                    $item->stock_system += $trx->qty;
                    $item->save();
                }
            }

            // Deduct production balance
            $bal = ProductionBalance::firstOrCreate(['user_id' => $ownerId], ['balance' => 0]);
            $bal->balance -= $trx->total_price;
            $bal->save();

            ProductionBalanceLog::create([
                'user_id' => $ownerId,
                'type' => 'purchase',
                'amount' => -$trx->total_price,
                'balance_after' => $bal->balance,
                'notes' => "Pembelian diverifikasi Owner: {$trx->item_name} ({$trx->qty} unit)",
                'created_by' => $user->id,
                'reference_id' => $trx->id,
            ]);

            if (class_exists(SystemActivity::class)) {
                SystemActivity::create([
                    'user_id' => $ownerId,
                    'type' => 'stock_purchase_approved',
                    'description' => "Owner menyetujui pembelian {$trx->item_name} (Total Rp " . number_format($trx->total_price, 0, ',', '.') . "). Saldo operasional terpotong dan stok bertambah.",
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => "Pembelian {$trx->item_name} disetujui! Stok bertambah dan saldo operasional telah diperbarui.",
                'data' => $trx->load(['item', 'creator', 'verifier']),
            ]);
        } else {
            $reason = trim((string)$request->input('reason', 'Pengeluaran belum disetujui / bukti belum lengkap'));
            $trx->status = 'rejected';
            $trx->verified_by = $user->id;
            $trx->verified_at = now();
            $trx->rejection_reason = $reason;
            $trx->save();

            if (class_exists(SystemActivity::class)) {
                SystemActivity::create([
                    'user_id' => $ownerId,
                    'type' => 'stock_purchase_rejected',
                    'description' => "Owner menolak pembelian {$trx->item_name} (Alasan: {$reason})",
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => "Pengajuan pembelian ditolak. Alasan penolakan telah disimpan.",
                'data' => $trx->load(['item', 'creator', 'verifier']),
            ]);
        }
    }

    public function getBalance(Request $request)
    {
        $user = Auth::user();
        $ownerId = $user->getEffectiveOwnerId();

        $balance = ProductionBalance::firstOrCreate(['user_id' => $ownerId], ['balance' => 0]);
        $logs = ProductionBalanceLog::where('user_id', $ownerId)->with('creator')->latest()->take(50)->get();

        return response()->json([
            'success' => true,
            'balance' => $balance->balance,
            'balance_formatted' => 'Rp ' . number_format($balance->balance, 0, ',', '.'),
            'logs' => $logs,
        ]);
    }

    public function topupBalance(Request $request)
    {
        $user = Auth::user();
        if (!$user->isOwner()) {
            return response()->json(['success' => false, 'error' => 'Hanya Owner yang dapat menambahkan saldo operasional.'], 403);
        }

        $ownerId = $user->getEffectiveOwnerId();

        $request->validate([
            'amount' => 'required|numeric|min:1000',
            'notes' => 'nullable|string',
        ]);

        $amount = (float)$request->input('amount');
        $notes = trim((string)$request->input('notes', 'Top up saldo operasional divisi produksi'));

        $bal = ProductionBalance::firstOrCreate(['user_id' => $ownerId], ['balance' => 0]);
        $bal->balance += $amount;
        $bal->save();

        $log = ProductionBalanceLog::create([
            'user_id' => $ownerId,
            'type' => 'topup',
            'amount' => $amount,
            'balance_after' => $bal->balance,
            'notes' => $notes,
            'created_by' => $user->id,
        ]);

        if (class_exists(SystemActivity::class)) {
            SystemActivity::create([
                'user_id' => $ownerId,
                'type' => 'production_balance_topup',
                'description' => "Owner menambahkan saldo operasional produksi sebesar Rp " . number_format($amount, 0, ',', '.') . " (Saldo sekarang: Rp " . number_format($bal->balance, 0, ',', '.') . ")",
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Saldo operasional divisi produksi berhasil ditambahkan!',
            'balance' => $bal->balance,
            'balance_formatted' => 'Rp ' . number_format($bal->balance, 0, ',', '.'),
            'log' => $log,
        ]);
    }

    public function exportCsv(Request $request)
    {
        $user = Auth::user();
        $ownerId = $user->getEffectiveOwnerId();
        $owner = $this->getEffectiveOwnerUser();
        $this->ensureDefaults($ownerId);

        $items = StockItem::where('user_id', $ownerId)->orderBy('category', 'asc')->orderBy('name', 'asc')->get();
        $balance = ProductionBalance::firstOrCreate(['user_id' => $ownerId], ['balance' => 0])->balance;

        $filename = 'laporan-stok-opname-' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($items, $balance, $owner) {
            $output = fopen('php://output', 'w');
            // Write UTF-8 BOM
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($output, ['LAPORAN PERSERDIAAN & STOCK OPNAME']);
            fputcsv($output, ['Bisnis / Vendor', $owner->business_name ?? 'CloseMateAI Workspace']);
            fputcsv($output, ['Tanggal Unduh', date('d F Y, H:i')]);
            fputcsv($output, ['Saldo Operasional Produksi', 'Rp ' . number_format($balance, 0, ',', '.')]);
            fputcsv($output, []);

            // Summary table
            fputcsv($output, ['No', 'Kode', 'Nama Barang', 'Kategori', 'Satuan', 'Stok Sistem', 'Batas Min', 'Status', 'Harga Satuan', 'Total Nilai Aset', 'Catatan']);

            $totalAset = 0;
            $no = 1;
            foreach ($items as $item) {
                $subtotal = $item->stock_system * $item->price;
                $totalAset += $subtotal;

                fputcsv($output, [
                    $no++,
                    $item->code ?: '-',
                    $item->name,
                    $item->category,
                    $item->unit,
                    number_format($item->stock_system, 0, ',', '.'),
                    number_format($item->min_stock, 0, ',', '.'),
                    $item->stock_status,
                    'Rp ' . number_format($item->price, 0, ',', '.'),
                    'Rp ' . number_format($subtotal, 0, ',', '.'),
                    $item->notes ?: '-',
                ]);
            }

            fputcsv($output, []);
            fputcsv($output, ['', '', '', '', '', '', '', '', 'TOTAL NILAI ASET STOK', 'Rp ' . number_format($totalAset, 0, ',', '.')]);

            fclose($output);
        };

        return response()->stream($callback, 200, $headers);
    }
}

