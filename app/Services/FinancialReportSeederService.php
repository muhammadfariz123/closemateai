<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\FinancialExpense;
use App\Models\FinancialMasterSalary;
use App\Models\FinancialMonthlyOperation;
use App\Models\StockItem;

class FinancialReportSeederService
{
    public static function seedIfEmpty($userId)
    {
        self::seedMasterSalaries($userId);
        self::seedMonthlyOperations($userId);
        self::seedOtherExpenses($userId);
        self::seedAlbums($userId);
        self::seedBookings($userId);
    }

    public static function seedMasterSalaries($userId)
    {
        if (FinancialMasterSalary::where('user_id', $userId)->count() === 0) {
            $studioSalaries = [
                ['name' => 'Danu', 'position' => 'CEO, MARKETTING, CS, ADMIN', 'qty' => 1, 'price' => 1700000, 'total' => 1700000, 'division' => 'studio'],
                ['name' => 'Umi', 'position' => 'FINANCE, MARKETTING, ADMIN', 'qty' => 1, 'price' => 1600000, 'total' => 1600000, 'division' => 'studio'],
                ['name' => 'Dika', 'position' => 'MARKETTING, HRD, MANAJER', 'qty' => 1, 'price' => 1500000, 'total' => 1500000, 'division' => 'studio'],
                ['name' => 'Nanang', 'position' => 'SPV CS', 'qty' => 1, 'price' => 1500000, 'total' => 1500000, 'division' => 'studio'],
                ['name' => 'Desti', 'position' => 'CS', 'qty' => 1, 'price' => 1200000, 'total' => 1200000, 'division' => 'studio'],
                ['name' => 'Bella', 'position' => 'CS', 'qty' => 1, 'price' => 1200000, 'total' => 1200000, 'division' => 'studio'],
            ];
            foreach ($studioSalaries as $s) {
                FinancialMasterSalary::create(array_merge($s, ['user_id' => $userId]));
            }

            $sosmedSalaries = [
                ['name' => 'IG PENAPICT', 'position' => null, 'qty' => 1, 'price' => 0, 'total' => 0, 'division' => 'sosmed'],
                ['name' => 'TIKTOK PENAPICT', 'position' => null, 'qty' => 1, 'price' => 0, 'total' => 0, 'division' => 'sosmed'],
                ['name' => 'IG PENAPICT CINEMA', 'position' => null, 'qty' => 1, 'price' => 0, 'total' => 0, 'division' => 'sosmed'],
                ['name' => 'TIKTOK PENAPICT CINEMA', 'position' => null, 'qty' => 1, 'price' => 0, 'total' => 0, 'division' => 'sosmed'],
                ['name' => 'IG PENAPICT DAILY', 'position' => null, 'qty' => 1, 'price' => 0, 'total' => 0, 'division' => 'sosmed'],
                ['name' => 'TIKTOK PENAPICT DAILY', 'position' => null, 'qty' => 1, 'price' => 0, 'total' => 0, 'division' => 'sosmed'],
                ['name' => 'IG STORYTADI', 'position' => null, 'qty' => 1, 'price' => 0, 'total' => 0, 'division' => 'sosmed'],
                ['name' => 'TIKTOK STORYTADI', 'position' => null, 'qty' => 1, 'price' => 105000, 'total' => 105000, 'division' => 'sosmed'],
                ['name' => 'YOUTUBE PENAPICT', 'position' => null, 'qty' => 1, 'price' => 0, 'total' => 0, 'division' => 'sosmed'],
            ];
            foreach ($sosmedSalaries as $s) {
                FinancialMasterSalary::create(array_merge($s, ['user_id' => $userId]));
            }
        }
    }

    public static function seedMonthlyOperations($userId)
    {
        if (FinancialMonthlyOperation::where('user_id', $userId)->count() === 0) {
            $ops = [
                ['name' => 'Peralatan Studio & Property', 'qty' => 1, 'price' => 0, 'total' => 0],
                ['name' => 'Biaya Renovasi', 'qty' => 1, 'price' => 0, 'total' => 0],
                ['name' => 'Biaya Promosi & Pengembangan Bisnis', 'qty' => 1, 'price' => 1000000, 'total' => 1000000],
                ['name' => 'Utilitas & Pemeliharaan', 'qty' => 1, 'price' => 1000000, 'total' => 1000000],
                ['name' => 'Hosting & Domain', 'qty' => 1, 'price' => 117000, 'total' => 117000],
                ['name' => 'Indihome Penapict', 'qty' => 1, 'price' => 313800, 'total' => 313800],
                ['name' => 'Listirk Penapict', 'qty' => 1, 'price' => 462770, 'total' => 462770],
                ['name' => 'Anggaran Belanja Alat', 'qty' => 1, 'price' => 3000000, 'total' => 3000000],
                ['name' => 'Kas Maintance Alat', 'qty' => 1, 'price' => 1000000, 'total' => 1000000],
                ['name' => 'Capcut', 'qty' => 1, 'price' => 300000, 'total' => 300000],
                ['name' => 'Adobe PS + LR', 'qty' => 1, 'price' => 300000, 'total' => 300000],
                ['name' => 'Microsite', 'qty' => 1, 'price' => 50000, 'total' => 50000],
                ['name' => 'Google Drive', 'qty' => 1, 'price' => 400000, 'total' => 400000],
            ];
            foreach ($ops as $o) {
                FinancialMonthlyOperation::create(array_merge($o, ['user_id' => $userId]));
            }
        }
    }

    public static function seedOtherExpenses($userId)
    {
        if (FinancialExpense::where('user_id', $userId)->count() === 0) {
            $expenses = [
                ['name' => 'Kunjungan griya afifah', 'date' => '2026-08-04', 'description' => '1', 'amount' => 50000],
                ['name' => 'Slip gaji bulan juli 2026', 'date' => '2026-08-05', 'description' => '1', 'amount' => 5225223],
                ['name' => 'Service Kamera', 'date' => '2026-08-12', 'description' => '1', 'amount' => 200000],
                ['name' => 'Munyi tegar', 'date' => '2026-08-19', 'description' => '1', 'amount' => 50000],
                ['name' => 'Belanja album', 'date' => '2026-08-20', 'description' => '3', 'amount' => 297000],
                ['name' => 'nyewa hp ipone naufal', 'date' => '2026-08-25', 'description' => '1', 'amount' => 25000],
                ['name' => 'Papan sponsor', 'date' => '2026-08-28', 'description' => '1', 'amount' => 500000],
            ];
            foreach ($expenses as $e) {
                FinancialExpense::create(array_merge($e, ['user_id' => $userId]));
            }
        }
    }

    public static function seedAlbums($userId)
    {
        $albums = [
            ['code' => 'ALB-MINI', 'name' => 'Mini Album', 'category' => 'Album', 'stock_system' => 25, 'unit' => 'unit', 'price' => 75000, 'min_stock' => 5, 'notes' => 'Mini Album Standar Foto'],
            ['code' => 'ALB-STD', 'name' => 'Standar Album', 'category' => 'Album', 'stock_system' => 30, 'unit' => 'unit', 'price' => 98000, 'min_stock' => 5, 'notes' => 'Album Standar Foto Lipat'],
            ['code' => 'ALB-EXC', 'name' => 'Exclusive Album', 'category' => 'Album', 'stock_system' => 20, 'unit' => 'unit', 'price' => 120000, 'min_stock' => 5, 'notes' => 'Exclusive Hardcover Album'],
            ['code' => 'ALB-WB', 'name' => 'Wedding Book', 'category' => 'Album', 'stock_system' => 15, 'unit' => 'unit', 'price' => 395000, 'min_stock' => 3, 'notes' => 'Premium Magazine Wedding Book'],
        ];
        foreach ($albums as $alb) {
            $exists = StockItem::where('user_id', $userId)->where('name', $alb['name'])->first();
            if (!$exists) {
                StockItem::create(array_merge($alb, ['user_id' => $userId]));
            }
        }
    }

    public static function seedBookings($userId)
    {
        if (Booking::where('user_id', $userId)->count() > 0) {
            return;
        }

        $rawBookings = [
            [
                'no' => 1, 'date' => '2026-08-01', 'client' => 'Aullia', 'addr' => 'Kuwarasan', 'hp' => '085870195327', 'mkt' => 'Danu',
                'pkg' => 'Wedding Prenium Foto Dan WCC', 'price' => 2630000, 'crew_foto' => 600000, 'c_4r' => 144000, 'pkg_cost' => 200000, 'alb' => 120000, 'frame' => 80500,
                'crew_cinema' => 0, 'laba_cinema' => 0, 'crew_liputan' => 0, 'laba_liputan' => 0, 'crew_konten' => 150000, 'laba_konten' => 0,
                'sales_fee' => 263000, 'dsc' => 0, 'cost' => 1557500, 'inc' => 1072500, 'valid' => 2630000,
                'features' => ['photo' => [], 'cinema' => [], 'liputan' => [], 'konten' => [], 'bundling' => []],
                'crew_fees' => ['ITAH' => 150000, 'ADIT' => 350000, 'HOHO' => 250000],
                'extras' => null,
            ],
            [
                'no' => 2, 'date' => '2026-08-01', 'client' => 'MAHKOTA', 'addr' => 'Tulus', 'hp' => '087821885853', 'mkt' => 'Mahkota',
                'pkg' => 'Wedding AIO Foto dan WCC', 'price' => 3380000, 'crew_foto' => 750000, 'c_4r' => 168000, 'pkg_cost' => 200000, 'alb' => 515000, 'frame' => 123500,
                'crew_cinema' => 0, 'laba_cinema' => 0, 'crew_liputan' => 0, 'laba_liputan' => 0, 'crew_konten' => 175000, 'laba_konten' => 0,
                'sales_fee' => 338000, 'dsc' => 0, 'cost' => 2269500, 'inc' => 1110500, 'valid' => 3380000,
                'features' => ['photo' => [], 'cinema' => [], 'liputan' => [], 'konten' => ['H'], 'bundling' => []],
                'crew_fees' => ['CANDRA' => 400000, 'ADIT' => 350000, 'NAUFAL' => 175000],
                'extras' => null,
            ],
            [
                'no' => 3, 'date' => '2026-08-02', 'client' => 'Karin', 'addr' => 'Tambak', 'hp' => '085700417917', 'mkt' => 'Danu',
                'pkg' => 'Wedding Prenium Foto Dan WCC', 'price' => 2630000, 'crew_foto' => 600000, 'c_4r' => 144000, 'pkg_cost' => 200000, 'alb' => 120000, 'frame' => 80500,
                'crew_cinema' => 0, 'laba_cinema' => 0, 'crew_liputan' => 0, 'laba_liputan' => 0, 'crew_konten' => 150000, 'laba_konten' => 0,
                'sales_fee' => 263000, 'dsc' => 0, 'cost' => 1557500, 'inc' => 1072500, 'valid' => 2630000,
                'features' => ['photo' => [], 'cinema' => [], 'liputan' => [], 'konten' => [], 'bundling' => []],
                'crew_fees' => ['ADIT' => 350000, 'HOHO' => 250000, 'FARHAN' => 150000],
                'extras' => ['transportasi' => 125000, 'jumlah' => 125000, 'keterangan' => 'Transportasi'],
            ],
            [
                'no' => 4, 'date' => '2026-08-02', 'client' => 'DINA SRIKANDI', 'addr' => 'Kedungwinangun', 'hp' => '08112615811', 'mkt' => 'Dina',
                'pkg' => 'Wedding Foto Basic', 'price' => 1800000, 'crew_foto' => 550000, 'c_4r' => 120000, 'pkg_cost' => 100000, 'alb' => 120000, 'frame' => 8500,
                'crew_cinema' => 0, 'laba_cinema' => 0, 'crew_liputan' => 0, 'laba_liputan' => 0, 'crew_konten' => 0, 'laba_konten' => 0,
                'sales_fee' => 180000, 'dsc' => 0, 'cost' => 1078500, 'inc' => 721500, 'valid' => 1800000,
                'features' => ['photo' => ['B'], 'cinema' => [], 'liputan' => [], 'konten' => [], 'bundling' => []],
                'crew_fees' => ['ADIT' => 350000, 'SOETONI' => 200000],
                'extras' => ['cetak' => 40700, 'jumlah' => 40700, 'keterangan' => '10 r (1)n& 4r (9)'],
            ],
            [
                'no' => 5, 'date' => '2026-08-03', 'client' => 'Sriwahyuni', 'addr' => 'Sruweng', 'hp' => '08991270588', 'mkt' => 'umi',
                'pkg' => 'Wedding AIO Foto,Cinema dan WCC', 'price' => 4400000, 'crew_foto' => 600000, 'c_4r' => 168000, 'pkg_cost' => 200000, 'alb' => 515000, 'frame' => 123500,
                'crew_cinema' => 700000, 'laba_cinema' => 0, 'crew_liputan' => 0, 'laba_liputan' => 0, 'crew_konten' => 150000, 'laba_konten' => 0,
                'sales_fee' => 440000, 'dsc' => 0, 'cost' => 2896500, 'inc' => 1503500, 'valid' => 4400000,
                'features' => ['photo' => [], 'cinema' => [], 'liputan' => [], 'konten' => [], 'bundling' => []],
                'crew_fees' => ['ITAH' => 150000, 'JEN' => 350000, 'HOHO' => 250000, 'PIXIO' => 700000],
                'extras' => null,
            ],
            [
                'no' => 6, 'date' => '2026-08-03', 'client' => 'Anisa', 'addr' => 'mirit', 'hp' => '082136546515', 'mkt' => 'Danu',
                'pkg' => 'Wedding Prenium Foto Dan Cinema', 'price' => 3650000, 'crew_foto' => 550000, 'c_4r' => 144000, 'pkg_cost' => 200000, 'alb' => 120000, 'frame' => 80500,
                'crew_cinema' => 700000, 'laba_cinema' => 0, 'crew_liputan' => 0, 'laba_liputan' => 0, 'crew_konten' => 0, 'laba_konten' => 0,
                'sales_fee' => 365000, 'dsc' => 0, 'cost' => 2159500, 'inc' => 1490500, 'valid' => 3650000,
                'features' => ['photo' => [], 'cinema' => [], 'liputan' => [], 'konten' => [], 'bundling' => []],
                'crew_fees' => ['JEN' => 350000, 'SOETONI' => 200000, 'PIXIO' => 700000],
                'extras' => ['transportasi' => 50000, 'jumlah' => 50000, 'keterangan' => 'Trabsportasi'],
            ],
            [
                'no' => 7, 'date' => '2026-08-06', 'client' => 'Sherly', 'addr' => 'Giyanti', 'hp' => '087726317773', 'mkt' => 'Danu',
                'pkg' => 'Wedding AIO Foto,Cinema dan WCC', 'price' => 4400000, 'crew_foto' => 750000, 'c_4r' => 168000, 'pkg_cost' => 200000, 'alb' => 515000, 'frame' => 123500,
                'crew_cinema' => 700000, 'laba_cinema' => 0, 'crew_liputan' => 0, 'laba_liputan' => 0, 'crew_konten' => 150000, 'laba_konten' => 0,
                'sales_fee' => 440000, 'dsc' => 0, 'cost' => 3046500, 'inc' => 1353500, 'valid' => 4400000,
                'features' => ['photo' => [], 'cinema' => [], 'liputan' => [], 'konten' => [], 'bundling' => []],
                'crew_fees' => ['DIKA' => 400000, 'ITAH' => 150000, 'ADIT' => 350000, 'PIXIO' => 700000],
                'extras' => null,
            ],
            [
                'no' => 8, 'date' => '2026-08-07', 'client' => 'Mira', 'addr' => 'Adikarso', 'hp' => '085880908503', 'mkt' => 'Danu',
                'pkg' => 'Wedding AIO Foto dan WCC ganti hglight pro', 'price' => 3380000, 'crew_foto' => 550000, 'c_4r' => 168000, 'pkg_cost' => 200000, 'alb' => 515000, 'frame' => 123500,
                'crew_cinema' => 0, 'laba_cinema' => 0, 'crew_liputan' => 0, 'laba_liputan' => 0, 'crew_konten' => 175000, 'laba_konten' => 0,
                'sales_fee' => 338000, 'dsc' => 0, 'cost' => 2069500, 'inc' => 1310500, 'valid' => 3380000,
                'features' => ['photo' => [], 'cinema' => [], 'liputan' => [], 'konten' => ['H'], 'bundling' => []],
                'crew_fees' => ['JEN' => 350000, 'SOETONI' => 200000, 'NAUFAL' => 175000],
                'extras' => ['drive' => 100000, 'jumlah' => 100000, 'keterangan' => 'Penambahan drive'],
            ],
            [
                'no' => 9, 'date' => '2026-08-07', 'client' => 'JARIMANIS', 'addr' => 'Gombong', 'hp' => '085155339077', 'mkt' => 'Jarimanis',
                'pkg' => 'Wedding AIO Foto,Cinema dan WCC', 'price' => 4400000, 'crew_foto' => 750000, 'c_4r' => 168000, 'pkg_cost' => 200000, 'alb' => 515000, 'frame' => 123500,
                'crew_cinema' => 700000, 'laba_cinema' => 0, 'crew_liputan' => 0, 'laba_liputan' => 0, 'crew_konten' => 150000, 'laba_konten' => 0,
                'sales_fee' => 660000, 'dsc' => 0, 'cost' => 3266500, 'inc' => 1133500, 'valid' => 4400000,
                'features' => ['photo' => [], 'cinema' => [], 'liputan' => [], 'konten' => [], 'bundling' => []],
                'crew_fees' => ['DIKA' => 400000, 'ITAH' => 150000, 'ARSAL' => 350000, 'PIXIO' => 700000],
                'extras' => ['drive' => 400000, 'jumlah' => 400000, 'keterangan' => 'raw video'],
            ],
            [
                'no' => 10, 'date' => '2026-08-07', 'client' => 'NOVI CUCUK', 'addr' => 'Petanahan', 'hp' => '083109645407', 'mkt' => 'Novi',
                'pkg' => 'Wedding Foto Basic', 'price' => 1750000, 'crew_foto' => 650000, 'c_4r' => 120000, 'pkg_cost' => 100000, 'alb' => 120000, 'frame' => 8500,
                'crew_cinema' => 0, 'laba_cinema' => 0, 'crew_liputan' => 0, 'laba_liputan' => 0, 'crew_konten' => 0, 'laba_konten' => 0,
                'sales_fee' => 175000, 'dsc' => 0, 'cost' => 1173500, 'inc' => 576500, 'valid' => 1750000,
                'features' => ['photo' => ['B'], 'cinema' => [], 'liputan' => [], 'konten' => [], 'bundling' => []],
                'crew_fees' => ['JEN' => 350000, 'TEGAR' => 300000],
                'extras' => null,
            ],
            [
                'no' => 11, 'date' => '2026-08-08', 'client' => 'Mei', 'addr' => 'Alian', 'hp' => '087889191827', 'mkt' => 'Danu',
                'pkg' => 'Wedding Prenium Foto Dan WCC ganti full creator', 'price' => 3030000, 'crew_foto' => 500000, 'c_4r' => 144000, 'pkg_cost' => 200000, 'alb' => 120000, 'frame' => 80500,
                'crew_cinema' => 0, 'laba_cinema' => 0, 'crew_liputan' => 0, 'laba_liputan' => 0, 'crew_konten' => 250000, 'laba_konten' => 0,
                'sales_fee' => 303000, 'dsc' => 0, 'cost' => 1597500, 'inc' => 1432500, 'valid' => 3030000,
                'features' => ['photo' => [], 'cinema' => [], 'liputan' => [], 'konten' => ['F'], 'bundling' => []],
                'crew_fees' => ['SOETONI' => 200000, 'ROIS' => 300000, 'FARHAN' => 250000],
                'extras' => ['cetak' => 612000, 'jumlah' => 612000, 'keterangan' => 'cetak 120 foto (2x)'],
            ],
            [
                'no' => 12, 'date' => '2026-08-08', 'client' => 'Triyuliani', 'addr' => 'Adimulyo', 'hp' => '083153952618', 'mkt' => 'Danu',
                'pkg' => 'Wedding Prenium Foto Dan WCC', 'price' => 2630000, 'crew_foto' => 600000, 'c_4r' => 144000, 'pkg_cost' => 200000, 'alb' => 120000, 'frame' => 80500,
                'crew_cinema' => 0, 'laba_cinema' => 0, 'crew_liputan' => 0, 'laba_liputan' => 0, 'crew_konten' => 150000, 'laba_konten' => 0,
                'sales_fee' => 263000, 'dsc' => 0, 'cost' => 1557500, 'inc' => 1072500, 'valid' => 2630000,
                'features' => ['photo' => [], 'cinema' => [], 'liputan' => [], 'konten' => [], 'bundling' => []],
                'crew_fees' => ['HOHO' => 250000, 'DITA' => 350000, 'DINA F' => 150000],
                'extras' => null,
            ],
            [
                'no' => 13, 'date' => '2026-08-08', 'client' => 'Shifa', 'addr' => 'panjer', 'hp' => '08992928401', 'mkt' => 'Danu',
                'pkg' => 'Wedding Foto Elegant dan Liputan Elegant', 'price' => 3500000, 'crew_foto' => 700000, 'c_4r' => 144000, 'pkg_cost' => 200000, 'alb' => 120000, 'frame' => 80500,
                'crew_cinema' => 0, 'laba_cinema' => 0, 'crew_liputan' => 850000, 'laba_liputan' => 350000, 'crew_konten' => 0, 'laba_konten' => 0,
                'sales_fee' => 350000, 'dsc' => 0, 'cost' => 2444500, 'inc' => 1055500, 'valid' => 3500000,
                'features' => ['photo' => ['E'], 'cinema' => [], 'liputan' => ['E'], 'konten' => [], 'bundling' => []],
                'crew_fees' => ['JEN' => 350000, 'DIDI' => 350000, 'ANAS' => 850000],
                'extras' => null,
            ],
            [
                'no' => 14, 'date' => '2026-08-08', 'client' => 'Siti', 'addr' => 'Alian', 'hp' => '083862438251', 'mkt' => 'Danu',
                'pkg' => 'Wedding Prenium Foto Dan WCC', 'price' => 2630000, 'crew_foto' => 600000, 'c_4r' => 144000, 'pkg_cost' => 200000, 'alb' => 120000, 'frame' => 80500,
                'crew_cinema' => 0, 'laba_cinema' => 0, 'crew_liputan' => 0, 'laba_liputan' => 0, 'crew_konten' => 150000, 'laba_konten' => 0,
                'sales_fee' => 263000, 'dsc' => 0, 'cost' => 1557500, 'inc' => 1072500, 'valid' => 2630000,
                'features' => ['photo' => [], 'cinema' => [], 'liputan' => [], 'konten' => [], 'bundling' => []],
                'crew_fees' => ['ADIT' => 350000, 'DAWIR' => 250000, 'FARHAN' => 150000],
                'extras' => null,
            ],
            [
                'no' => 15, 'date' => '2026-08-08', 'client' => 'Ikhbati', 'addr' => 'Gombong', 'hp' => '08988020262', 'mkt' => 'Danu',
                'pkg' => 'Wedding AIO Foto,Cinema dan WCC', 'price' => 4400000, 'crew_foto' => 750000, 'c_4r' => 168000, 'pkg_cost' => 200000, 'alb' => 515000, 'frame' => 123500,
                'crew_cinema' => 700000, 'laba_cinema' => 0, 'crew_liputan' => 0, 'laba_liputan' => 0, 'crew_konten' => 150000, 'laba_konten' => 0,
                'sales_fee' => 440000, 'dsc' => 0, 'cost' => 3046500, 'inc' => 1353500, 'valid' => 4400000,
                'features' => ['photo' => [], 'cinema' => [], 'liputan' => [], 'konten' => [], 'bundling' => []],
                'crew_fees' => ['DIKA' => 400000, 'ITAH' => 150000, 'TEGAR' => 350000, 'PIXIO' => 700000],
                'extras' => null,
            ],
            [
                'no' => 16, 'date' => '2026-08-09', 'client' => 'Dewi', 'addr' => 'Kutowinangun', 'hp' => '085640279781', 'mkt' => 'Danu',
                'pkg' => 'Wedding AIO Foto dan WCC', 'price' => 3180000, 'crew_foto' => 700000, 'c_4r' => 168000, 'pkg_cost' => 200000, 'alb' => 515000, 'frame' => 123500,
                'crew_cinema' => 0, 'laba_cinema' => 0, 'crew_liputan' => 0, 'laba_liputan' => 0, 'crew_konten' => 150000, 'laba_konten' => 0,
                'sales_fee' => 318000, 'dsc' => 0, 'cost' => 2174500, 'inc' => 1005500, 'valid' => 3180000,
                'features' => ['photo' => [], 'cinema' => [], 'liputan' => [], 'konten' => [], 'bundling' => []],
                'crew_fees' => ['JEN' => 350000, 'DIDI' => 350000, 'FARHAN' => 150000],
                'extras' => null,
            ],
            [
                'no' => 17, 'date' => '2026-08-09', 'client' => 'NOVI CUCUK', 'addr' => 'Ambal', 'hp' => '082146340401', 'mkt' => 'Novi',
                'pkg' => 'Wedding Foto Basic', 'price' => 1750000, 'crew_foto' => 550000, 'c_4r' => 120000, 'pkg_cost' => 100000, 'alb' => 120000, 'frame' => 8500,
                'crew_cinema' => 0, 'laba_cinema' => 0, 'crew_liputan' => 0, 'laba_liputan' => 0, 'crew_konten' => 0, 'laba_konten' => 0,
                'sales_fee' => 175000, 'dsc' => 0, 'cost' => 1073500, 'inc' => 676500, 'valid' => 1750000,
                'features' => ['photo' => ['B'], 'cinema' => [], 'liputan' => [], 'konten' => [], 'bundling' => []],
                'crew_fees' => ['HOHO' => 250000, 'ROIS' => 300000],
                'extras' => null,
            ],
            [
                'no' => 18, 'date' => '2026-08-09', 'client' => 'NAYAKA MAKEUP', 'addr' => 'Puring', 'hp' => '085703954616', 'mkt' => 'Nayaka',
                'pkg' => 'Wedding Foto Elegant', 'price' => 2300000, 'crew_foto' => 550000, 'c_4r' => 144000, 'pkg_cost' => 200000, 'alb' => 120000, 'frame' => 80500,
                'crew_cinema' => 0, 'laba_cinema' => 0, 'crew_liputan' => 0, 'laba_liputan' => 0, 'crew_konten' => 0, 'laba_konten' => 0,
                'sales_fee' => 230000, 'dsc' => 0, 'cost' => 1324500, 'inc' => 975500, 'valid' => 2300000,
                'features' => ['photo' => ['E'], 'cinema' => [], 'liputan' => [], 'konten' => [], 'bundling' => []],
                'crew_fees' => ['SOETONI' => 200000, 'TEGAR' => 350000],
                'extras' => null,
            ],
            [
                'no' => 19, 'date' => '2026-08-09', 'client' => 'Aminatuz', 'addr' => 'Tanuraksan', 'hp' => '083863619273', 'mkt' => 'Danu',
                'pkg' => 'Wedding Prenium Foto Dan Cinema', 'price' => 3400000, 'crew_foto' => 700000, 'c_4r' => 144000, 'pkg_cost' => 200000, 'alb' => 120000, 'frame' => 80500,
                'crew_cinema' => 700000, 'laba_cinema' => 0, 'crew_liputan' => 0, 'laba_liputan' => 0, 'crew_konten' => 0, 'laba_konten' => 0,
                'sales_fee' => 340000, 'dsc' => 0, 'cost' => 2284500, 'inc' => 1115500, 'valid' => 3400000,
                'features' => ['photo' => [], 'cinema' => [], 'liputan' => [], 'konten' => [], 'bundling' => []],
                'crew_fees' => ['JEN' => 350000, 'ARSAL' => 350000, 'PIXIO' => 700000],
                'extras' => null,
            ],
            [
                'no' => 20, 'date' => '2026-08-10', 'client' => 'Nova', 'addr' => 'Karangsari', 'hp' => '082128612541', 'mkt' => 'Ucup',
                'pkg' => 'Wedding AIO Foto, Cinema dan WCC ganti hglight pro', 'price' => 4600000, 'crew_foto' => 600000, 'c_4r' => 168000, 'pkg_cost' => 200000, 'alb' => 515000, 'frame' => 123500,
                'crew_cinema' => 700000, 'laba_cinema' => 0, 'crew_liputan' => 0, 'laba_liputan' => 0, 'crew_konten' => 175000, 'laba_konten' => 0,
                'sales_fee' => 460000, 'dsc' => 0, 'cost' => 2941500, 'inc' => 1658500, 'valid' => 4600000,
                'features' => ['photo' => [], 'cinema' => [], 'liputan' => [], 'konten' => ['H'], 'bundling' => []],
                'crew_fees' => ['DIKA' => 400000, 'SOETONI' => 200000, 'PIXIO' => 700000, 'NAUFAL' => 175000],
                'extras' => null,
            ],
            [
                'no' => 21, 'date' => '2026-08-11', 'client' => 'Siti', 'addr' => 'Kutowinangun', 'hp' => '081227939292', 'mkt' => 'Danu',
                'pkg' => 'Wedding Prenium Foto Dan Cinema', 'price' => 3650000, 'crew_foto' => 600000, 'c_4r' => 144000, 'pkg_cost' => 200000, 'alb' => 120000, 'frame' => 80500,
                'crew_cinema' => 700000, 'laba_cinema' => 0, 'crew_liputan' => 0, 'laba_liputan' => 0, 'crew_konten' => 0, 'laba_konten' => 0,
                'sales_fee' => 365000, 'dsc' => 0, 'cost' => 2209500, 'inc' => 1440500, 'valid' => 3650000,
                'features' => ['photo' => [], 'cinema' => [], 'liputan' => [], 'konten' => [], 'bundling' => []],
                'crew_fees' => ['DIKA' => 400000, 'SOETONI' => 200000, 'PIXIO' => 700000],
                'extras' => null,
            ],
            [
                'no' => 22, 'date' => '2026-08-12', 'client' => 'Deby', 'addr' => 'Jalan pemuda', 'hp' => '0895329940529', 'mkt' => 'Danu',
                'pkg' => 'Wedding Prenium Foto Dan WCC ganti full creator', 'price' => 3030000, 'crew_foto' => 750000, 'c_4r' => 144000, 'pkg_cost' => 200000, 'alb' => 120000, 'frame' => 80500,
                'crew_cinema' => 0, 'laba_cinema' => 0, 'crew_liputan' => 0, 'laba_liputan' => 0, 'crew_konten' => 350000, 'laba_konten' => 0,
                'sales_fee' => 303000, 'dsc' => 0, 'cost' => 1947500, 'inc' => 1082500, 'valid' => 3030000,
                'features' => ['photo' => [], 'cinema' => [], 'liputan' => [], 'konten' => ['F'], 'bundling' => []],
                'crew_fees' => ['DIKA' => 400000, 'ITAH' => 350000, 'ADIT' => 350000],
                'extras' => null,
            ],
            [
                'no' => 23, 'date' => '2026-08-12', 'client' => 'DWI YULI', 'addr' => 'Sidomoro', 'hp' => '082229200038', 'mkt' => 'Dwi',
                'pkg' => 'Wedding Prenium Foto Dan WCC', 'price' => 2630000, 'crew_foto' => 600000, 'c_4r' => 144000, 'pkg_cost' => 200000, 'alb' => 120000, 'frame' => 80500,
                'crew_cinema' => 0, 'laba_cinema' => 0, 'crew_liputan' => 0, 'laba_liputan' => 0, 'crew_konten' => 150000, 'laba_konten' => 0,
                'sales_fee' => 263000, 'dsc' => 0, 'cost' => 1557500, 'inc' => 1072500, 'valid' => 2630000,
                'features' => ['photo' => [], 'cinema' => [], 'liputan' => [], 'konten' => [], 'bundling' => []],
                'crew_fees' => ['JEN' => 350000, 'HOHO' => 250000, 'FARHAN' => 150000],
                'extras' => null,
            ],
            [
                'no' => 24, 'date' => '2026-08-13', 'client' => 'MAHKOTA', 'addr' => 'Padureso', 'hp' => '081329343013', 'mkt' => 'Mahkota',
                'pkg' => 'Wedding Prenium Foto Dan WCC', 'price' => 2630000, 'crew_foto' => 550000, 'c_4r' => 144000, 'pkg_cost' => 200000, 'alb' => 120000, 'frame' => 80500,
                'crew_cinema' => 0, 'laba_cinema' => 0, 'crew_liputan' => 0, 'laba_liputan' => 0, 'crew_konten' => 150000, 'laba_konten' => 0,
                'sales_fee' => 263000, 'dsc' => 0, 'cost' => 1507500, 'inc' => 1122500, 'valid' => 2630000,
                'features' => ['photo' => [], 'cinema' => [], 'liputan' => [], 'konten' => [], 'bundling' => []],
                'crew_fees' => ['ITAH' => 150000, 'ADIT' => 350000, 'SOETONI' => 200000],
                'extras' => ['transportasi' => 150000, 'jumlah' => 150000, 'keterangan' => 'Transportasi'],
            ],
            [
                'no' => 25, 'date' => '2026-08-13', 'client' => 'Devid', 'addr' => 'Jl yani', 'hp' => '08986179694', 'mkt' => 'Danu',
                'pkg' => 'Penambahan Hari', 'price' => 900000, 'crew_foto' => 400000, 'c_4r' => 0, 'pkg_cost' => 0, 'alb' => 0, 'frame' => 0,
                'crew_cinema' => 0, 'laba_cinema' => 0, 'crew_liputan' => 0, 'laba_liputan' => 0, 'crew_konten' => 0, 'laba_konten' => 0,
                'sales_fee' => 90000, 'dsc' => 0, 'cost' => 490000, 'inc' => 410000, 'valid' => 900000,
                'features' => ['photo' => [], 'cinema' => [], 'liputan' => [], 'konten' => [], 'bundling' => []],
                'crew_fees' => ['ARSAL' => 400000],
                'extras' => null,
            ],
            [
                'no' => 26, 'date' => '2026-08-13', 'client' => 'Widi', 'addr' => 'Poncowarno', 'hp' => '0818026269938', 'mkt' => 'Bintang',
                'pkg' => 'Wedding AIO Foto, Cinema dan WCC', 'price' => 4400000, 'crew_foto' => 750000, 'c_4r' => 168000, 'pkg_cost' => 200000, 'alb' => 515000, 'frame' => 123500,
                'crew_cinema' => 700000, 'laba_cinema' => 0, 'crew_liputan' => 0, 'laba_liputan' => 0, 'crew_konten' => 150000, 'laba_konten' => 0,
                'sales_fee' => 440000, 'dsc' => 0, 'cost' => 3046500, 'inc' => 1353500, 'valid' => 4400000,
                'features' => ['photo' => [], 'cinema' => [], 'liputan' => [], 'konten' => [], 'bundling' => []],
                'crew_fees' => ['DIKA' => 400000, 'JEN' => 350000, 'PIXIO' => 700000, 'SALUH' => 150000],
                'extras' => ['transportasi' => 100000, 'jumlah' => 100000, 'keterangan' => 'Transportasi'],
            ],
            [
                'no' => 27, 'date' => '2026-08-16', 'client' => 'Evi', 'addr' => 'Alian', 'hp' => '083874536860', 'mkt' => 'Danu',
                'pkg' => 'Wedding Prenium Foto Dan WCC', 'price' => 2630000, 'crew_foto' => 600000, 'c_4r' => 144000, 'pkg_cost' => 200000, 'alb' => 120000, 'frame' => 80500,
                'crew_cinema' => 0, 'laba_cinema' => 0, 'crew_liputan' => 0, 'laba_liputan' => 0, 'crew_konten' => 150000, 'laba_konten' => 0,
                'sales_fee' => 263000, 'dsc' => 0, 'cost' => 1557500, 'inc' => 1072500, 'valid' => 2630000,
                'features' => ['photo' => [], 'cinema' => [], 'liputan' => [], 'konten' => [], 'bundling' => []],
                'crew_fees' => ['DIKA' => 400000, 'SOETONI' => 200000, 'FARHAN' => 150000],
                'extras' => null,
            ],
            [
                'no' => 28, 'date' => '2026-08-17', 'client' => 'Dina', 'addr' => 'Jtaisari', 'hp' => '087724796372', 'mkt' => 'Danu',
                'pkg' => 'Wedding AIO Foto dan WCC', 'price' => 3180000, 'crew_foto' => 550000, 'c_4r' => 168000, 'pkg_cost' => 200000, 'alb' => 515000, 'frame' => 123500,
                'crew_cinema' => 0, 'laba_cinema' => 0, 'crew_liputan' => 0, 'laba_liputan' => 0, 'crew_konten' => 150000, 'laba_konten' => 0,
                'sales_fee' => 318000, 'dsc' => 0, 'cost' => 2024500, 'inc' => 1155500, 'valid' => 3180000,
                'features' => ['photo' => [], 'cinema' => [], 'liputan' => [], 'konten' => [], 'bundling' => []],
                'crew_fees' => ['JEN' => 350000, 'SOETONI' => 200000, 'FARHAN' => 150000],
                'extras' => null,
            ],
            [
                'no' => 29, 'date' => '2026-08-17', 'client' => 'AMS DEKOR', 'addr' => 'Bocor', 'hp' => '082229200038', 'mkt' => 'ams',
                'pkg' => 'Kemerdekaan', 'price' => 1250000, 'crew_foto' => 250000, 'c_4r' => 0, 'pkg_cost' => 0, 'alb' => 0, 'frame' => 0,
                'crew_cinema' => 0, 'laba_cinema' => 0, 'crew_liputan' => 0, 'laba_liputan' => 0, 'crew_konten' => 200000, 'laba_konten' => 0,
                'sales_fee' => 125000, 'dsc' => 0, 'cost' => 575000, 'inc' => 675000, 'valid' => 1250000,
                'features' => ['photo' => [], 'cinema' => [], 'liputan' => [], 'konten' => [], 'bundling' => []],
                'crew_fees' => ['ITAH' => 150000, 'HOHO' => 250000, 'ALULA' => 50000],
                'extras' => null,
            ],
            [
                'no' => 30, 'date' => '2026-08-19', 'client' => 'DWI YULI', 'addr' => 'Dwi', 'hp' => '082229200038', 'mkt' => 'Dwi',
                'pkg' => 'Liputan Basic', 'price' => 1000000, 'crew_foto' => 0, 'c_4r' => 0, 'pkg_cost' => 0, 'alb' => 0, 'frame' => 0,
                'crew_cinema' => 0, 'laba_cinema' => 0, 'crew_liputan' => 650000, 'laba_liputan' => 350000, 'crew_konten' => 0, 'laba_konten' => 0,
                'sales_fee' => 100000, 'dsc' => 0, 'cost' => 750000, 'inc' => 250000, 'valid' => 1000000,
                'features' => ['photo' => [], 'cinema' => [], 'liputan' => ['B'], 'konten' => [], 'bundling' => []],
                'crew_fees' => ['ANAS' => 650000],
                'extras' => null,
            ],
            [
                'no' => 31, 'date' => '2026-08-20', 'client' => 'Laeli', 'addr' => 'Krakal', 'hp' => '081226343893', 'mkt' => 'Danu',
                'pkg' => 'Wedding Prenium Foto Dan Cinema', 'price' => 3650000, 'crew_foto' => 550000, 'c_4r' => 144000, 'pkg_cost' => 200000, 'alb' => 120000, 'frame' => 80500,
                'crew_cinema' => 700000, 'laba_cinema' => 0, 'crew_liputan' => 0, 'laba_liputan' => 0, 'crew_konten' => 0, 'laba_konten' => 0,
                'sales_fee' => 365000, 'dsc' => 0, 'cost' => 2159500, 'inc' => 1490500, 'valid' => 3650000,
                'features' => ['photo' => [], 'cinema' => [], 'liputan' => [], 'konten' => [], 'bundling' => []],
                'crew_fees' => ['JEN' => 350000, 'SOETONI' => 200000, 'PIXIO' => 700000],
                'extras' => ['transportasi' => 100000, 'jumlah' => 100000, 'keterangan' => 'Transportasi'],
            ],
            [
                'no' => 32, 'date' => '2026-08-20', 'client' => 'Nala', 'addr' => 'Tamanwinangun', 'hp' => '085742937536', 'mkt' => 'Itah',
                'pkg' => 'Wedding Foto Elegant', 'price' => 2300000, 'crew_foto' => 750000, 'c_4r' => 144000, 'pkg_cost' => 200000, 'alb' => 120000, 'frame' => 80500,
                'crew_cinema' => 0, 'laba_cinema' => 0, 'crew_liputan' => 0, 'laba_liputan' => 0, 'crew_konten' => 0, 'laba_konten' => 0,
                'sales_fee' => 230000, 'dsc' => 0, 'cost' => 1524500, 'inc' => 775500, 'valid' => 2300000,
                'features' => ['photo' => ['E'], 'cinema' => [], 'liputan' => [], 'konten' => [], 'bundling' => []],
                'crew_fees' => ['DIKA' => 400000, 'ADIT' => 350000],
                'extras' => null,
            ],
            [
                'no' => 33, 'date' => '2026-08-20', 'client' => 'BERLYN', 'addr' => 'Adikarso', 'hp' => '089669569606', 'mkt' => 'berlyn',
                'pkg' => 'Wedding Foto Basic', 'price' => 2100000, 'crew_foto' => 400000, 'c_4r' => 120000, 'pkg_cost' => 100000, 'alb' => 120000, 'frame' => 8500,
                'crew_cinema' => 0, 'laba_cinema' => 0, 'crew_liputan' => 0, 'laba_liputan' => 0, 'crew_konten' => 0, 'laba_konten' => 0,
                'sales_fee' => 210000, 'dsc' => 0, 'cost' => 958500, 'inc' => 1141500, 'valid' => 2100000,
                'features' => ['photo' => ['B'], 'cinema' => [], 'liputan' => [], 'konten' => [], 'bundling' => []],
                'crew_fees' => ['ARSAL' => 400000],
                'extras' => null,
            ],
            [
                'no' => 34, 'date' => '2026-08-21', 'client' => 'Devi', 'addr' => 'Tamanwinangun', 'hp' => '0895385232066', 'mkt' => 'Nanang',
                'pkg' => 'Wedding AIO Foto dan WCC ganti hglight pro', 'price' => 3380000, 'crew_foto' => 600000, 'c_4r' => 168000, 'pkg_cost' => 200000, 'alb' => 515000, 'frame' => 123500,
                'crew_cinema' => 0, 'laba_cinema' => 0, 'crew_liputan' => 0, 'laba_liputan' => 0, 'crew_konten' => 175000, 'laba_konten' => 0,
                'sales_fee' => 338000, 'dsc' => 0, 'cost' => 2119500, 'inc' => 1260500, 'valid' => 3380000,
                'features' => ['photo' => [], 'cinema' => [], 'liputan' => [], 'konten' => ['H'], 'bundling' => []],
                'crew_fees' => ['JEN' => 350000, 'HOHO' => 250000, 'NAUFAL' => 175000],
                'extras' => null,
            ],
            [
                'no' => 35, 'date' => '2026-08-21', 'client' => 'Rizki', 'addr' => 'Kranganayr', 'hp' => '087854729250', 'mkt' => 'Danu',
                'pkg' => 'Wedding Foto Elegant & cinema elegant', 'price' => 4300000, 'crew_foto' => 600000, 'c_4r' => 144000, 'pkg_cost' => 200000, 'alb' => 120000, 'frame' => 80500,
                'crew_cinema' => 1200000, 'laba_cinema' => 800000, 'crew_liputan' => 0, 'laba_liputan' => 0, 'crew_konten' => 0, 'laba_konten' => 0,
                'sales_fee' => 430000, 'dsc' => 0, 'cost' => 2774500, 'inc' => 1525500, 'valid' => 4300000,
                'features' => ['photo' => ['E'], 'cinema' => ['E'], 'liputan' => [], 'konten' => [], 'bundling' => []],
                'crew_fees' => ['DIKA' => 400000, 'SOETONI' => 200000, 'PIXIO' => 1200000],
                'extras' => null,
            ],
            [
                'no' => 36, 'date' => '2026-08-22', 'client' => 'Rizki', 'addr' => 'Wonorejo', 'hp' => '087854729250', 'mkt' => 'Danu',
                'pkg' => 'Wedding Foto Elegant & cinema elegant', 'price' => 4300000, 'crew_foto' => 700000, 'c_4r' => 144000, 'pkg_cost' => 200000, 'alb' => 120000, 'frame' => 80500,
                'crew_cinema' => 1200000, 'laba_cinema' => 800000, 'crew_liputan' => 0, 'laba_liputan' => 0, 'crew_konten' => 0, 'laba_konten' => 0,
                'sales_fee' => 430000, 'dsc' => 0, 'cost' => 2874500, 'inc' => 1425500, 'valid' => 4300000,
                'features' => ['photo' => ['E'], 'cinema' => ['E'], 'liputan' => [], 'konten' => [], 'bundling' => []],
                'crew_fees' => ['JEN' => 350000, 'ADIT' => 350000, 'PIXIO' => 1200000],
                'extras' => null,
            ],
            [
                'no' => 37, 'date' => '2026-08-23', 'client' => 'Estri', 'addr' => 'Candisari', 'hp' => '0895412796787', 'mkt' => 'Umi',
                'pkg' => 'Wedding AIO Foto, Cinema dan WCC', 'price' => 4400000, 'crew_foto' => 750000, 'c_4r' => 168000, 'pkg_cost' => 200000, 'alb' => 515000, 'frame' => 123500,
                'crew_cinema' => 700000, 'laba_cinema' => 0, 'crew_liputan' => 0, 'laba_liputan' => 0, 'crew_konten' => 150000, 'laba_konten' => 0,
                'sales_fee' => 440000, 'dsc' => 0, 'cost' => 3046500, 'inc' => 1353500, 'valid' => 4400000,
                'features' => ['photo' => [], 'cinema' => [], 'liputan' => [], 'konten' => [], 'bundling' => []],
                'crew_fees' => ['DIKA' => 400000, 'ITAH' => 150000, 'ADIT' => 350000, 'PIXIO' => 700000],
                'extras' => null,
            ],
            [
                'no' => 38, 'date' => '2026-08-23', 'client' => 'ARCELIA M', 'addr' => 'Kampung luxulo', 'hp' => '082229200038', 'mkt' => 'Arcelia',
                'pkg' => 'WCC Sosial Spark', 'price' => 350000, 'crew_foto' => 0, 'c_4r' => 0, 'pkg_cost' => 0, 'alb' => 0, 'frame' => 0,
                'crew_cinema' => 0, 'laba_cinema' => 0, 'crew_liputan' => 0, 'laba_liputan' => 0, 'crew_konten' => 150000, 'laba_konten' => 0,
                'sales_fee' => 35000, 'dsc' => 0, 'cost' => 185000, 'inc' => 165000, 'valid' => 350000,
                'features' => ['photo' => [], 'cinema' => [], 'liputan' => [], 'konten' => [], 'bundling' => []],
                'crew_fees' => ['BURHAN' => 150000],
                'extras' => null,
            ],
            [
                'no' => 39, 'date' => '2026-08-23', 'client' => 'DINA SRIKANDI', 'addr' => 'Alian', 'hp' => '089643394886', 'mkt' => 'Dina',
                'pkg' => 'Wedding Foto Basic & Liputan Basic', 'price' => 2800000, 'crew_foto' => 700000, 'c_4r' => 120000, 'pkg_cost' => 100000, 'alb' => 120000, 'frame' => 8500,
                'crew_cinema' => 0, 'laba_cinema' => 0, 'crew_liputan' => 650000, 'laba_liputan' => 350000, 'crew_konten' => 0, 'laba_konten' => 0,
                'sales_fee' => 280000, 'dsc' => 0, 'cost' => 1978500, 'inc' => 821500, 'valid' => 2800000,
                'features' => ['photo' => ['B'], 'cinema' => [], 'liputan' => ['B'], 'konten' => [], 'bundling' => []],
                'crew_fees' => ['JEN' => 350000, 'ROIS' => 350000, 'ANAS' => 650000],
                'extras' => null,
            ],
            [
                'no' => 40, 'date' => '2026-08-23', 'client' => 'Tyas', 'addr' => 'Panjer', 'hp' => '082210895808', 'mkt' => 'Danu',
                'pkg' => 'Prewedding Foto Luqury, Conten Dayli & Cinema Basic', 'price' => 3100000, 'crew_foto' => 200000, 'c_4r' => 26000, 'pkg_cost' => 200000, 'alb' => 50000, 'frame' => 110000,
                'crew_cinema' => 700000, 'laba_cinema' => 0, 'crew_liputan' => 0, 'laba_liputan' => 0, 'crew_konten' => 100000, 'laba_konten' => 0,
                'sales_fee' => 310000, 'dsc' => 155000, 'cost' => 1851000, 'inc' => 1249000, 'valid' => 3100000,
                'features' => ['photo' => [], 'cinema' => ['B'], 'liputan' => [], 'konten' => ['H'], 'bundling' => []],
                'crew_fees' => ['SOETONI' => 200000, 'PIXIO' => 700000, 'FARHAN' => 100000],
                'extras' => ['transportasi' => 50000, 'jumlah' => 50000, 'keterangan' => 'Transportaso'],
            ],
            [
                'no' => 41, 'date' => '2026-08-24', 'client' => 'Ika', 'addr' => 'Alian', 'hp' => '083861171121', 'mkt' => 'Astri',
                'pkg' => 'Wedding Foto Elegant & Hglight pro', 'price' => 2850000, 'crew_foto' => 600000, 'c_4r' => 144000, 'pkg_cost' => 200000, 'alb' => 120000, 'frame' => 80500,
                'crew_cinema' => 0, 'laba_cinema' => 0, 'crew_liputan' => 0, 'laba_liputan' => 0, 'crew_konten' => 250000, 'laba_konten' => 0,
                'sales_fee' => 285000, 'dsc' => 0, 'cost' => 1679500, 'inc' => 1170500, 'valid' => 2850000,
                'features' => ['photo' => ['E'], 'cinema' => [], 'liputan' => [], 'konten' => ['H'], 'bundling' => []],
                'crew_fees' => ['DIKA' => 400000, 'ITAH' => 250000, 'SOETONI' => 200000],
                'extras' => null,
            ],
            [
                'no' => 42, 'date' => '2026-08-24', 'client' => 'Nur Kholifah', 'addr' => 'Kebakalan', 'hp' => '085647010163', 'mkt' => 'Naufal',
                'pkg' => 'Wedding Foto Elegant', 'price' => 2300000, 'crew_foto' => 600000, 'c_4r' => 144000, 'pkg_cost' => 200000, 'alb' => 120000, 'frame' => 80500,
                'crew_cinema' => 0, 'laba_cinema' => 0, 'crew_liputan' => 0, 'laba_liputan' => 0, 'crew_konten' => 0, 'laba_konten' => 0,
                'sales_fee' => 230000, 'dsc' => 0, 'cost' => 1374500, 'inc' => 925500, 'valid' => 2300000,
                'features' => ['photo' => ['E'], 'cinema' => [], 'liputan' => [], 'konten' => [], 'bundling' => []],
                'crew_fees' => ['HOHO' => 250000, 'TEGAR' => 350000],
                'extras' => null,
            ],
            [
                'no' => 43, 'date' => '2026-08-24', 'client' => 'Romadhona', 'addr' => 'Kutowinangun', 'hp' => '087724949042', 'mkt' => 'Danu',
                'pkg' => 'Wedding Prenium Foto Dan WCC', 'price' => 2630000, 'crew_foto' => 700000, 'c_4r' => 144000, 'pkg_cost' => 200000, 'alb' => 120000, 'frame' => 80500,
                'crew_cinema' => 0, 'laba_cinema' => 0, 'crew_liputan' => 0, 'laba_liputan' => 0, 'crew_konten' => 150000, 'laba_konten' => 0,
                'sales_fee' => 263000, 'dsc' => 0, 'cost' => 1657500, 'inc' => 972500, 'valid' => 2630000,
                'features' => ['photo' => [], 'cinema' => [], 'liputan' => [], 'konten' => [], 'bundling' => []],
                'crew_fees' => ['JEN' => 350000, 'ADIT' => 350000, 'SALUH' => 150000],
                'extras' => null,
            ],
            [
                'no' => 44, 'date' => '2026-08-24', 'client' => 'Helmi', 'addr' => 'Purworejo', 'hp' => '085868426332', 'mkt' => 'Danu',
                'pkg' => 'Wedding Foto Elegant dan Liputan Elegant', 'price' => 3500000, 'crew_foto' => 700000, 'c_4r' => 144000, 'pkg_cost' => 200000, 'alb' => 120000, 'frame' => 80500,
                'crew_cinema' => 0, 'laba_cinema' => 0, 'crew_liputan' => 850000, 'laba_liputan' => 350000, 'crew_konten' => 0, 'laba_konten' => 0,
                'sales_fee' => 350000, 'dsc' => 0, 'cost' => 2444500, 'inc' => 1055500, 'valid' => 3500000,
                'features' => ['photo' => ['E'], 'cinema' => [], 'liputan' => ['E'], 'konten' => [], 'bundling' => []],
                'crew_fees' => ['JEN' => 350000, 'DITA' => 350000, 'ANAS' => 850000],
                'extras' => ['transportasi' => 135000, 'jumlah' => 135000, 'keterangan' => 'transportasi'],
            ],
            [
                'no' => 45, 'date' => '2026-08-25', 'client' => 'Nur Istiqomah', 'addr' => 'Sempor', 'hp' => '0889831756696', 'mkt' => 'Danu',
                'pkg' => 'Wedding Prenium Foto Dan WCC', 'price' => 2630000, 'crew_foto' => 550000, 'c_4r' => 144000, 'pkg_cost' => 200000, 'alb' => 120000, 'frame' => 80500,
                'crew_cinema' => 0, 'laba_cinema' => 0, 'crew_liputan' => 0, 'laba_liputan' => 0, 'crew_konten' => 150000, 'laba_konten' => 0,
                'sales_fee' => 263000, 'dsc' => 0, 'cost' => 1507500, 'inc' => 1122500, 'valid' => 2630000,
                'features' => ['photo' => [], 'cinema' => [], 'liputan' => [], 'konten' => [], 'bundling' => []],
                'crew_fees' => ['ITAH' => 150000, 'ADIT' => 350000, 'SOETONI' => 200000],
                'extras' => ['transportasi' => 150000, 'jumlah' => 150000, 'keterangan' => 'transportasi'],
            ],
            [
                'no' => 46, 'date' => '2026-08-25', 'client' => 'Anisa', 'addr' => 'Kutowinangun', 'hp' => '0895421792232', 'mkt' => 'umi',
                'pkg' => 'Wedding Prenium Foto Dan WCC ganti full creator', 'price' => 3030000, 'crew_foto' => 600000, 'c_4r' => 144000, 'pkg_cost' => 200000, 'alb' => 120000, 'frame' => 80500,
                'crew_cinema' => 0, 'laba_cinema' => 0, 'crew_liputan' => 0, 'laba_liputan' => 0, 'crew_konten' => 250000, 'laba_konten' => 0,
                'sales_fee' => 303000, 'dsc' => 0, 'cost' => 1697500, 'inc' => 1332500, 'valid' => 3030000,
                'features' => ['photo' => [], 'cinema' => [], 'liputan' => [], 'konten' => ['F'], 'bundling' => []],
                'crew_fees' => ['JEN' => 350000, 'HOHO' => 250000, 'FARHAN' => 250000],
                'extras' => null,
            ],
            [
                'no' => 47, 'date' => '2026-08-25', 'client' => 'Ummu', 'addr' => 'jembangan', 'hp' => '085700366391', 'mkt' => 'Umi',
                'pkg' => 'Prewedding Foto Basic', 'price' => 800000, 'crew_foto' => 250000, 'c_4r' => 0, 'pkg_cost' => 0, 'alb' => 0, 'frame' => 8500,
                'crew_cinema' => 0, 'laba_cinema' => 0, 'crew_liputan' => 0, 'laba_liputan' => 0, 'crew_konten' => 0, 'laba_konten' => 0,
                'sales_fee' => 80000, 'dsc' => 0, 'cost' => 338500, 'inc' => 461500, 'valid' => 800000,
                'features' => ['photo' => ['B'], 'cinema' => [], 'liputan' => [], 'konten' => [], 'bundling' => []],
                'crew_fees' => ['ITAH' => 250000],
                'extras' => null,
            ],
            [
                'no' => 48, 'date' => '2026-08-26', 'client' => 'Fitria', 'addr' => 'Pejagoan', 'hp' => '081216571004', 'mkt' => 'Ria',
                'pkg' => 'Wedding Prenium Foto Dan Cinema', 'price' => 3650000, 'crew_foto' => 600000, 'c_4r' => 144000, 'pkg_cost' => 200000, 'alb' => 120000, 'frame' => 80500,
                'crew_cinema' => 700000, 'laba_cinema' => 0, 'crew_liputan' => 0, 'laba_liputan' => 0, 'crew_konten' => 0, 'laba_konten' => 0,
                'sales_fee' => 365000, 'dsc' => 0, 'cost' => 2209500, 'inc' => 1440500, 'valid' => 3650000,
                'features' => ['photo' => [], 'cinema' => [], 'liputan' => [], 'konten' => [], 'bundling' => []],
                'crew_fees' => ['DIKA' => 400000, 'SOETONI' => 200000, 'PIXIO' => 700000],
                'extras' => null,
            ],
            [
                'no' => 49, 'date' => '2026-08-27', 'client' => 'Khusnul', 'addr' => 'Selang', 'hp' => '083112176568', 'mkt' => 'Danu',
                'pkg' => 'Wedding Prenium Foto Dan Cinema', 'price' => 3650000, 'crew_foto' => 750000, 'c_4r' => 144000, 'pkg_cost' => 200000, 'alb' => 120000, 'frame' => 80500,
                'crew_cinema' => 700000, 'laba_cinema' => 0, 'crew_liputan' => 0, 'laba_liputan' => 0, 'crew_konten' => 0, 'laba_konten' => 0,
                'sales_fee' => 365000, 'dsc' => 0, 'cost' => 2359500, 'inc' => 1290500, 'valid' => 3650000,
                'features' => ['photo' => [], 'cinema' => [], 'liputan' => [], 'konten' => [], 'bundling' => []],
                'crew_fees' => ['DIKA' => 400000, 'ARSAL' => 350000, 'PIXIO' => 700000],
                'extras' => null,
            ],
            [
                'no' => 50, 'date' => '2026-08-27', 'client' => 'Ica', 'addr' => 'Buayan', 'hp' => '089636911771', 'mkt' => 'Naufal',
                'pkg' => 'Wedding Prenium Foto Dan Cinema', 'price' => 3650000, 'crew_foto' => 700000, 'c_4r' => 144000, 'pkg_cost' => 200000, 'alb' => 120000, 'frame' => 80500,
                'crew_cinema' => 700000, 'laba_cinema' => 0, 'crew_liputan' => 0, 'laba_liputan' => 0, 'crew_konten' => 0, 'laba_konten' => 0,
                'sales_fee' => 365000, 'dsc' => 0, 'cost' => 2309500, 'inc' => 1340500, 'valid' => 3650000,
                'features' => ['photo' => [], 'cinema' => [], 'liputan' => [], 'konten' => [], 'bundling' => []],
                'crew_fees' => ['JEN' => 350000, 'ADIT' => 350000, 'PIXIO' => 700000],
                'extras' => null,
            ],
            // 51 to 58 (Laporan 2)
            [
                'no' => 51, 'date' => '2026-08-27', 'client' => 'RISWANTO', 'addr' => 'Rowokele', 'hp' => '081385947322', 'mkt' => 'RISWANTO',
                'pkg' => 'Wedding Prenium Foto Dan WCC', 'price' => 2630000, 'crew_foto' => 600000, 'c_4r' => 144000, 'pkg_cost' => 200000, 'alb' => 120000, 'frame' => 80500,
                'crew_cinema' => 0, 'laba_cinema' => 0, 'crew_liputan' => 0, 'laba_liputan' => 0, 'crew_konten' => 150000, 'laba_konten' => 0,
                'sales_fee' => 263000, 'dsc' => 0, 'cost' => 1557500, 'inc' => 1072500, 'valid' => 2630000,
                'features' => ['photo' => [], 'cinema' => [], 'liputan' => [], 'konten' => [], 'bundling' => []],
                'crew_fees' => ['HOHO' => 250000, 'TEGAR' => 350000, 'FARHAN' => 150000],
                'extras' => null,
            ],
            [
                'no' => 52, 'date' => '2026-08-27', 'client' => 'Fiki', 'addr' => 'Petanahan', 'hp' => '0882003330053', 'mkt' => 'Danu',
                'pkg' => 'Wedding Prenium Foto Dan WCC', 'price' => 2630000, 'crew_foto' => 700000, 'c_4r' => 144000, 'pkg_cost' => 200000, 'alb' => 120000, 'frame' => 80500,
                'crew_cinema' => 0, 'laba_cinema' => 0, 'crew_liputan' => 0, 'laba_liputan' => 0, 'crew_konten' => 150000, 'laba_konten' => 0,
                'sales_fee' => 263000, 'dsc' => 0, 'cost' => 1657500, 'inc' => 972500, 'valid' => 2630000,
                'features' => ['photo' => [], 'cinema' => [], 'liputan' => [], 'konten' => [], 'bundling' => []],
                'crew_fees' => ['ITAH' => 150000, 'JEN' => 350000, 'ARSAL' => 350000],
                'extras' => null,
            ],
            [
                'no' => 53, 'date' => '2026-08-28', 'client' => 'EVENT HANIFA', 'addr' => 'Candimulyo', 'hp' => '082229200038', 'mkt' => 'HANI',
                'pkg' => 'conten Sosial Spark', 'price' => 650000, 'crew_foto' => 0, 'c_4r' => 0, 'pkg_cost' => 0, 'alb' => 0, 'frame' => 0,
                'crew_cinema' => 0, 'laba_cinema' => 0, 'crew_liputan' => 0, 'laba_liputan' => 0, 'crew_konten' => 450000, 'laba_konten' => 0,
                'sales_fee' => 0, 'dsc' => 0, 'cost' => 450000, 'inc' => 200000, 'valid' => 650000,
                'features' => ['photo' => [], 'cinema' => [], 'liputan' => [], 'konten' => [], 'bundling' => []],
                'crew_fees' => ['FARHAN' => 150000, 'SALUH' => 300000],
                'extras' => null,
            ],
            [
                'no' => 54, 'date' => '2026-08-29', 'client' => 'Amalia', 'addr' => 'Sempor', 'hp' => '088975730594', 'mkt' => 'Danu',
                'pkg' => 'Wedding AIO Foto, Cinema dan WCC', 'price' => 4400000, 'crew_foto' => 600000, 'c_4r' => 168000, 'pkg_cost' => 200000, 'alb' => 515000, 'frame' => 123500,
                'crew_cinema' => 700000, 'laba_cinema' => 0, 'crew_liputan' => 0, 'laba_liputan' => 0, 'crew_konten' => 150000, 'laba_konten' => 0,
                'sales_fee' => 440000, 'dsc' => 0, 'cost' => 2896500, 'inc' => 1503500, 'valid' => 4400000,
                'features' => ['photo' => [], 'cinema' => [], 'liputan' => [], 'konten' => [], 'bundling' => []],
                'crew_fees' => ['DIKA' => 400000, 'ITAH' => 150000, 'SOETONI' => 200000, 'PIXIO' => 700000],
                'extras' => ['transportasi' => 100000, 'jumlah' => 100000, 'keterangan' => 'Transportasi'],
            ],
            [
                'no' => 55, 'date' => '2026-08-29', 'client' => 'Rifngatun', 'addr' => 'Buluspesantren', 'hp' => '082137972869', 'mkt' => 'Candra',
                'pkg' => 'Wedding AIO Foto dan WCC', 'price' => 3180000, 'crew_foto' => 750000, 'c_4r' => 144000, 'pkg_cost' => 200000, 'alb' => 120000, 'frame' => 80500,
                'crew_cinema' => 0, 'laba_cinema' => 0, 'crew_liputan' => 0, 'laba_liputan' => 0, 'crew_konten' => 150000, 'laba_konten' => 0,
                'sales_fee' => 318000, 'dsc' => 0, 'cost' => 1762500, 'inc' => 1417500, 'valid' => 3180000,
                'features' => ['photo' => [], 'cinema' => [], 'liputan' => [], 'konten' => [], 'bundling' => []],
                'crew_fees' => ['ADIT' => 350000, 'ARSAL' => 400000, 'FARHAN' => 150000],
                'extras' => null,
            ],
            [
                'no' => 56, 'date' => '2026-08-29', 'client' => 'Imelda', 'addr' => 'Kedungpuji', 'hp' => '083116437865', 'mkt' => 'Danu',
                'pkg' => 'Wedding AIO Foto dan WCC', 'price' => 3180000, 'crew_foto' => 600000, 'c_4r' => 168000, 'pkg_cost' => 200000, 'alb' => 515000, 'frame' => 123500,
                'crew_cinema' => 0, 'laba_cinema' => 0, 'crew_liputan' => 0, 'laba_liputan' => 0, 'crew_konten' => 150000, 'laba_konten' => 0,
                'sales_fee' => 318000, 'dsc' => 0, 'cost' => 2074500, 'inc' => 1105500, 'valid' => 3180000,
                'features' => ['photo' => [], 'cinema' => [], 'liputan' => [], 'konten' => [], 'bundling' => []],
                'crew_fees' => ['JEN' => 350000, 'HOHO' => 250000, 'FARHAN' => 150000],
                'extras' => ['transportasi' => 100000, 'jumlah' => 100000, 'keterangan' => 'Transportasi'],
            ],
            [
                'no' => 57, 'date' => '2026-08-30', 'client' => 'Vika', 'addr' => 'Pejagoan', 'hp' => '081460813162', 'mkt' => 'Itah',
                'pkg' => 'Prewedding Foto Elegant', 'price' => 2300000, 'crew_foto' => 600000, 'c_4r' => 144000, 'pkg_cost' => 200000, 'alb' => 120000, 'frame' => 80500,
                'crew_cinema' => 0, 'laba_cinema' => 0, 'crew_liputan' => 0, 'laba_liputan' => 0, 'crew_konten' => 0, 'laba_konten' => 0,
                'sales_fee' => 230000, 'dsc' => 0, 'cost' => 1374500, 'inc' => 925500, 'valid' => 2300000,
                'features' => ['photo' => ['E'], 'cinema' => [], 'liputan' => [], 'konten' => [], 'bundling' => []],
                'crew_fees' => ['DIKA' => 400000, 'SOETONI' => 200000],
                'extras' => null,
            ],
            [
                'no' => 58, 'date' => '2026-08-31', 'client' => 'Milla', 'addr' => 'Gedung Setda', 'hp' => '08895507851', 'mkt' => 'Umi',
                'pkg' => 'Wedding AIO Foto, Cinema dan WCC', 'price' => 4400000, 'crew_foto' => 750000, 'c_4r' => 168000, 'pkg_cost' => 200000, 'alb' => 515000, 'frame' => 123500,
                'crew_cinema' => 700000, 'laba_cinema' => 0, 'crew_liputan' => 0, 'laba_liputan' => 0, 'crew_konten' => 150000, 'laba_konten' => 0,
                'sales_fee' => 440000, 'dsc' => 0, 'cost' => 3046500, 'inc' => 1353500, 'valid' => 4400000,
                'features' => ['photo' => [], 'cinema' => [], 'liputan' => [], 'konten' => [], 'bundling' => []],
                'crew_fees' => ['DIKA' => 400000, 'ITAH' => 150000, 'CANDRA' => 350000, 'PIXIO' => 700000],
                'extras' => null,
            ],
        ];

        foreach ($rawBookings as $b) {
            Booking::create([
                'user_id' => $userId,
                'client_name' => $b['client'],
                'client_address' => $b['addr'],
                'client_wa_number' => $b['hp'],
                'event_date' => $b['date'],
                'package_name' => $b['pkg'],
                'package_price' => $b['price'],
                'package_qty' => 1,
                'marketing_name' => $b['mkt'],
                'cost_crew_foto' => $b['crew_foto'],
                'cost_cetak_4r' => $b['c_4r'],
                'cost_packaging' => $b['pkg_cost'],
                'cost_album' => $b['alb'],
                'cost_cetak_jumbo' => $b['frame'],
                'cost_crew_cinema' => $b['crew_cinema'],
                'laba_cinema' => $b['laba_cinema'],
                'cost_crew_liputan' => $b['crew_liputan'],
                'laba_liputan' => $b['laba_liputan'],
                'cost_crew_konten' => $b['crew_konten'],
                'laba_konten' => $b['laba_konten'],
                'sales_fee' => $b['sales_fee'],
                'discount' => $b['dsc'],
                'total_operational_cost' => $b['cost'],
                'total_income' => $b['price'],
                'paid_amount' => $b['price'],
                'net_profit' => $b['inc'],
                'crew_fee_details' => $b['crew_fees'],
                'package_features' => $b['features'],
                'extras_detail' => $b['extras'],
                'payment_status' => 'Lunas',
                'production_status' => 'Ready to Pick Up',
            ]);
        }
    }
}

