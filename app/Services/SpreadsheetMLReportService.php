<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\FinancialExpense;
use App\Models\FinancialMasterSalary;
use App\Models\FinancialMonthlyOperation;
use App\Models\StockItem;

class SpreadsheetMLReportService
{
    protected $userId;
    protected $filterStart;
    protected $filterEnd;

    public function __construct($userId, $filterStart = null, $filterEnd = null)
    {
        $this->userId = $userId;
        $this->filterStart = $filterStart;
        $this->filterEnd = $filterEnd;

        // Ensure database defaults exist for this user
        FinancialReportSeederService::seedIfEmpty($userId);
    }

    public function generateXml(): string
    {
        // 1. Fetch data
        $bookingQuery = Booking::where('user_id', $this->userId)->orderBy('id', 'asc');
        if ($this->filterStart && $this->filterEnd) {
            $bookingQuery->whereBetween('event_date', [$this->filterStart, $this->filterEnd]);
        }
        $bookings = $bookingQuery->get();

        $studioSalaries = FinancialMasterSalary::where('user_id', $this->userId)->where('division', 'studio')->get();
        $sosmedSalaries = FinancialMasterSalary::where('user_id', $this->userId)->where('division', 'sosmed')->get();
        $monthlyOps = FinancialMonthlyOperation::where('user_id', $this->userId)->get();
        $expenses = FinancialExpense::where('user_id', $this->userId)->orderBy('date', 'asc')->get();
        $albums = StockItem::where('user_id', $this->userId)->where('category', 'Album')->get();

        // If no albums found under category Album, fallback to all stock items
        if ($albums->isEmpty()) {
            $albums = StockItem::where('user_id', $this->userId)->get();
        }

        // Crew list for FG & VG Matrix
        $crewList = [
            'NANANG', 'DANU', 'DIKA', 'ARYA', 'FAUZAN', 'ADIT', 'HOHO', 'ANGGA',
            'AFIF', 'RENDI', 'TIO', 'ZAKY', 'KELVIN', 'FARIS', 'BAGUS', 'ZEN',
            'ITAH', 'RIZKY', 'FAIZAL', 'IRVAN', 'RYAN', 'ARIF', 'CANDRA', 'NAUFAL'
        ];

        // 2. Build XML
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\r\n";
        $xml .= '<?mso-application progid="Excel.Sheet"?>' . "\r\n";
        $xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"' . "\r\n";
        $xml .= ' xmlns:o="urn:schemas-microsoft-com:office:office"' . "\r\n";
        $xml .= ' xmlns:x="urn:schemas-microsoft-com:office:excel"' . "\r\n";
        $xml .= ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"' . "\r\n";
        $xml .= ' xmlns:html="http://www.w3.org/TR/REC-html40">' . "\r\n";

        // Document Properties
        $xml .= '  <DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">' . "\r\n";
        $xml .= '    <Author>Penapict / CloseMate AI</Author>' . "\r\n";
        $xml .= '    <LastAuthor>Penapict</LastAuthor>' . "\r\n";
        $xml .= '    <Created>' . date('Y-m-d\TH:i:s\Z') . '</Created>' . "\r\n";
        $xml .= '    <Company>Penapict</Company>' . "\r\n";
        $xml .= '  </DocumentProperties>' . "\r\n";

        // Styles
        $xml .= $this->buildStyles();

        // Split bookings: Tab 1 (1-50), Tab 2 (51+)
        $bookingsPart1 = $bookings->slice(0, 50);
        $bookingsPart2 = $bookings->slice(50);

        // 3. Worksheets
        $xml .= $this->buildLaporanSheet('LAPORAN', $bookingsPart1, 1, 50, $monthlyOps, $expenses);
        $xml .= $this->buildLaporanSheet('LAPORAN (2)', $bookingsPart2, 51, 100, $monthlyOps, $expenses);
        $xml .= $this->buildStudioSheet($studioSalaries);
        $xml .= $this->buildSosmedSheet($sosmedSalaries);
        $xml .= $this->buildFgVgSheet($bookings, $crewList);
        $xml .= $this->buildMonthlyOpsSheet($monthlyOps);
        $xml .= $this->buildOtherExpensesSheet($expenses);
        $xml .= $this->buildDanuMarketingSheet($bookings);
        $xml .= $this->buildAlbumSheet($albums);

        $xml .= '</Workbook>';

        return $xml;
    }

    protected function buildStyles(): string
    {
        return '  <Styles>
    <Style ss:ID="Default" ss:Name="Normal">
      <Alignment ss:Vertical="Center"/>
      <Borders/>
      <Font ss:FontName="Calibri" ss:Size="10" ss:Color="#000000"/>
      <Interior/>
      <NumberFormat/>
      <Protection/>
    </Style>
    <Style ss:ID="TitleBanner">
      <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
      <Font ss:FontName="Calibri" ss:Size="14" ss:Bold="1" ss:Color="#FFFFFF"/>
      <Interior ss:Color="#1D4ED8" ss:Pattern="Solid"/>
      <Borders>
        <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#1E40AF"/>
      </Borders>
    </Style>
    <Style ss:ID="SubTitleBanner">
      <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
      <Font ss:FontName="Calibri" ss:Size="11" ss:Bold="1" ss:Color="#FFFFFF"/>
      <Interior ss:Color="#2563EB" ss:Pattern="Solid"/>
    </Style>
    <Style ss:ID="SectionHeader">
      <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
      <Font ss:FontName="Calibri" ss:Size="11" ss:Bold="1" ss:Color="#FFFFFF"/>
      <Interior ss:Color="#1E40AF" ss:Pattern="Solid"/>
      <Borders>
        <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#94A3B8"/>
      </Borders>
    </Style>
    <Style ss:ID="SummaryLabel">
      <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
      <Font ss:FontName="Calibri" ss:Size="10" ss:Bold="1" ss:Color="#1E293B"/>
      <Interior ss:Color="#F1F5F9" ss:Pattern="Solid"/>
      <Borders>
        <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>
        <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>
        <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>
        <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>
      </Borders>
    </Style>
    <Style ss:ID="SummaryValue">
      <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
      <Font ss:FontName="Calibri" ss:Size="10" ss:Bold="1" ss:Color="#0F172A"/>
      <Interior ss:Color="#FFFFFF" ss:Pattern="Solid"/>
      <NumberFormat ss:Format="&quot;Rp &quot;#,##0.00"/>
      <Borders>
        <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>
        <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>
        <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>
        <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>
      </Borders>
    </Style>
    <Style ss:ID="SummaryNote">
      <Alignment ss:Horizontal="Left" ss:Vertical="Center" ss:WrapText="1"/>
      <Font ss:FontName="Calibri" ss:Size="9" ss:Italic="1" ss:Color="#475569"/>
      <Interior ss:Color="#F8FAFC" ss:Pattern="Solid"/>
    </Style>
    <Style ss:ID="ColHeaderMain">
      <Alignment ss:Horizontal="Center" ss:Vertical="Center" ss:WrapText="1"/>
      <Font ss:FontName="Calibri" ss:Size="9" ss:Bold="1" ss:Color="#FFFFFF"/>
      <Interior ss:Color="#1D4ED8" ss:Pattern="Solid"/>
      <Borders>
        <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#94A3B8"/>
        <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#94A3B8"/>
        <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#94A3B8"/>
        <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#94A3B8"/>
      </Borders>
    </Style>
    <Style ss:ID="ColSubHeader">
      <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
      <Font ss:FontName="Calibri" ss:Size="8" ss:Bold="1" ss:Color="#1E3A8A"/>
      <Interior ss:Color="#DBEAFE" ss:Pattern="Solid"/>
      <Borders>
        <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#94A3B8"/>
        <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#94A3B8"/>
        <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#94A3B8"/>
        <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#94A3B8"/>
      </Borders>
    </Style>
    <Style ss:ID="CellText">
      <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
      <Font ss:FontName="Calibri" ss:Size="9" ss:Color="#0F172A"/>
      <Borders>
        <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
        <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
        <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
        <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
      </Borders>
    </Style>
    <Style ss:ID="CellCenter">
      <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
      <Font ss:FontName="Calibri" ss:Size="9" ss:Color="#0F172A"/>
      <Borders>
        <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
        <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
        <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
        <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
      </Borders>
    </Style>
    <Style ss:ID="CellCheckbox">
      <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
      <Font ss:FontName="Calibri" ss:Size="11" ss:Color="#1E3A8A"/>
      <Borders>
        <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>
        <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>
        <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>
        <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>
      </Borders>
    </Style>
    <Style ss:ID="CellCurrency">
      <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
      <Font ss:FontName="Calibri" ss:Size="9" ss:Color="#0F172A"/>
      <NumberFormat ss:Format="&quot;Rp &quot;#,##0.00"/>
      <Borders>
        <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
        <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
        <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
        <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
      </Borders>
    </Style>
    <Style ss:ID="CellCurrencyZero">
      <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
      <Font ss:FontName="Calibri" ss:Size="9" ss:Color="#94A3B8"/>
      <NumberFormat ss:Format="&quot;Rp &quot;#,##0.00"/>
      <Borders>
        <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
        <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
        <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
        <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#E2E8F0"/>
      </Borders>
    </Style>
    <Style ss:ID="CellTotalLabel">
      <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>
      <Font ss:FontName="Calibri" ss:Size="10" ss:Bold="1" ss:Color="#1E3A8A"/>
      <Interior ss:Color="#BFDBFE" ss:Pattern="Solid"/>
      <Borders>
        <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#1D4ED8"/>
        <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#94A3B8"/>
        <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#94A3B8"/>
        <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#1D4ED8"/>
      </Borders>
    </Style>
    <Style ss:ID="CellTotalCurrency">
      <Alignment ss:Horizontal="Right" ss:Vertical="Center"/>
      <Font ss:FontName="Calibri" ss:Size="10" ss:Bold="1" ss:Color="#1E3A8A"/>
      <Interior ss:Color="#BFDBFE" ss:Pattern="Solid"/>
      <NumberFormat ss:Format="&quot;Rp &quot;#,##0.00"/>
      <Borders>
        <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#1D4ED8"/>
        <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#94A3B8"/>
        <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#94A3B8"/>
        <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="2" ss:Color="#1D4ED8"/>
      </Borders>
    </Style>
    <Style ss:ID="MetaLabel">
      <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
      <Font ss:FontName="Calibri" ss:Size="10" ss:Bold="1" ss:Color="#1E293B"/>
    </Style>
    <Style ss:ID="MetaValue">
      <Alignment ss:Horizontal="Left" ss:Vertical="Center"/>
      <Font ss:FontName="Calibri" ss:Size="10" ss:Color="#0F172A"/>
    </Style>
  </Styles>' . "\r\n";
    }

    public function getEffectiveFeatures($b): array
    {
        $features = is_array($b->package_features) ? $b->package_features : [];
        $hasAny = false;
        foreach (['photo', 'cinema', 'liputan', 'konten', 'bundling'] as $k) {
            if (!empty($features[$k]) && is_array($features[$k]) && count($features[$k]) > 0) {
                $hasAny = true;
                break;
            }
        }
        if ($hasAny) {
            return $features;
        }

        // Auto-detect from package name if not explicitly set
        $name = strtolower($b->package_name ?? '');
        $det = ['photo' => [], 'cinema' => [], 'liputan' => [], 'konten' => [], 'bundling' => []];

        if (str_contains($name, 'bundling') || str_contains($name, 'aio')) {
            if (str_contains($name, 'basic') || str_contains($name, 'bk')) {
                $det['bundling'][] = 'BK';
            } elseif (str_contains($name, 'custom') || str_contains($name, 'bc')) {
                $det['bundling'][] = 'BC';
            } else {
                $det['bundling'][] = 'BK';
            }
        }
        if (str_contains($name, 'photo') || str_contains($name, 'foto')) {
            if (str_contains($name, 'exclusive') || str_contains($name, 'excl')) {
                $det['photo'][] = 'E';
            } elseif (str_contains($name, 'wedding book') || str_contains($name, 'wb')) {
                $det['photo'][] = 'WB';
            } elseif (str_contains($name, 'roll')) {
                $det['photo'][] = 'ROLL';
            } else {
                $det['photo'][] = 'B';
            }
        }
        if (str_contains($name, 'cinema') || str_contains($name, 'wcc') || str_contains($name, 'video')) {
            if (str_contains($name, 'exclusive')) {
                $det['cinema'][] = 'E';
            } elseif (str_contains($name, 'liputan')) {
                $det['cinema'][] = 'L';
            } else {
                $det['cinema'][] = 'B';
            }
        }
        if (str_contains($name, 'liputan')) {
            $det['liputan'][] = 'B';
        }
        if (str_contains($name, 'konten') || str_contains($name, 'content') || str_contains($name, 'sde')) {
            if (str_contains($name, 'sde')) {
                $det['konten'][] = 'SDE';
            } elseif (str_contains($name, 'full')) {
                $det['konten'][] = 'F';
            } else {
                $det['konten'][] = 'H';
            }
        }

        return $det;
    }

    public function getEffectiveCosts($b): array
    {
        $costs = [
            'crew_foto' => $this->cleanNumber($b->cost_crew_foto),
            'cetak_4r' => $this->cleanNumber($b->cost_cetak_4r),
            'packaging' => $this->cleanNumber($b->cost_packaging),
            'album' => $this->cleanNumber($b->cost_album),
            'frame' => $this->cleanNumber($b->cost_cetak_jumbo),
            'crew_cinema' => $this->cleanNumber($b->cost_crew_cinema),
            'laba_cinema' => $this->cleanNumber($b->laba_cinema),
            'crew_liputan' => $this->cleanNumber($b->cost_crew_liputan),
            'laba_liputan' => $this->cleanNumber($b->laba_liputan),
            'crew_konten' => $this->cleanNumber($b->cost_crew_konten),
            'laba_konten' => $this->cleanNumber($b->laba_konten),
            'sales_fee' => $this->cleanNumber($b->sales_fee),
        ];

        // If specific breakdown columns are empty, auto-detect from operational_costs
        $totalSpecific = array_sum($costs);
        if ($totalSpecific == 0 && is_array($b->operational_costs) && !empty($b->operational_costs)) {
            foreach ($b->operational_costs as $op) {
                $itemTitle = strtolower($op['name'] ?? '');
                $itemAmt = $this->cleanNumber($op['price'] ?? 0) * (int)($op['qty'] ?? 1);
                if (str_contains($itemTitle, '4r')) $costs['cetak_4r'] += $itemAmt;
                elseif (str_contains($itemTitle, 'pack') || str_contains($itemTitle, 'box') || str_contains($itemTitle, 'fd')) $costs['packaging'] += $itemAmt;
                elseif (str_contains($itemTitle, 'album')) $costs['album'] += $itemAmt;
                elseif (str_contains($itemTitle, 'frame') || str_contains($itemTitle, 'jumbo') || str_contains($itemTitle, 'bingkai')) $costs['frame'] += $itemAmt;
                elseif (str_contains($itemTitle, 'cinema')) $costs['crew_cinema'] += $itemAmt;
                elseif (str_contains($itemTitle, 'liputan')) $costs['crew_liputan'] += $itemAmt;
                elseif (str_contains($itemTitle, 'konten')) $costs['crew_konten'] += $itemAmt;
                elseif (str_contains($itemTitle, 'foto') || str_contains($itemTitle, 'fg')) $costs['crew_foto'] += $itemAmt;
            }
        }

        // If sales fee is 0, auto default to 10% of package_price if marketing exists
        if ($costs['sales_fee'] == 0 && $this->cleanNumber($b->package_price) > 0) {
            $costs['sales_fee'] = round($this->cleanNumber($b->package_price) * 0.1);
        }

        return $costs;
    }

    public function getEffectiveCrewFees($b, array $crewList): array
    {
        $details = is_array($b->crew_fee_details) ? $b->crew_fee_details : [];
        if (!empty($details)) {
            return $details;
        }

        // Try extracting from operational_costs or team_members
        $fees = [];
        if (is_array($b->operational_costs)) {
            foreach ($b->operational_costs as $op) {
                $opName = strtoupper($op['name'] ?? '');
                $opAmt = $this->cleanNumber($op['price'] ?? 0) * (int)($op['qty'] ?? 1);
                foreach ($crewList as $c) {
                    if (str_contains($opName, $c)) {
                        $fees[$c] = ($fees[$c] ?? 0) + $opAmt;
                    }
                }
            }
        }
        if (is_array($b->team_members)) {
            foreach ($b->team_members as $tm) {
                $tmName = strtoupper($tm['name'] ?? '');
                $tmFee = $this->cleanNumber($tm['fee'] ?? 0);
                foreach ($crewList as $c) {
                    if (str_contains($tmName, $c) && $tmFee > 0) {
                        $fees[$c] = ($fees[$c] ?? 0) + $tmFee;
                    }
                }
            }
        }
        return $fees;
    }

    protected function buildLaporanSheet(string $sheetName, $bookings, int $startNo, int $endNo, $monthlyOps, $expenses): string
    {
        $xml = '  <Worksheet ss:Name="' . $this->xmlEscape($sheetName) . '">' . "\r\n";
        $xml .= '    <Table ss:DefaultRowHeight="20">' . "\r\n";

        // Set column widths
        $xml .= '      <Column ss:Width="30"/>' . "\r\n"; // No
        $xml .= '      <Column ss:Width="75"/>' . "\r\n"; // TGL
        $xml .= '      <Column ss:Width="130"/>' . "\r\n"; // CLIENT
        // PHOTO 4 cols
        $xml .= '      <Column ss:Width="25"/>' . "\r\n";
        $xml .= '      <Column ss:Width="25"/>' . "\r\n";
        $xml .= '      <Column ss:Width="28"/>' . "\r\n";
        $xml .= '      <Column ss:Width="35"/>' . "\r\n";
        // CINEMA 3 cols
        $xml .= '      <Column ss:Width="25"/>' . "\r\n";
        $xml .= '      <Column ss:Width="25"/>' . "\r\n";
        $xml .= '      <Column ss:Width="25"/>' . "\r\n";
        // LIPUTAN 3 cols
        $xml .= '      <Column ss:Width="25"/>' . "\r\n";
        $xml .= '      <Column ss:Width="25"/>' . "\r\n";
        $xml .= '      <Column ss:Width="25"/>' . "\r\n";
        // KONTEN 3 cols
        $xml .= '      <Column ss:Width="25"/>' . "\r\n";
        $xml .= '      <Column ss:Width="25"/>' . "\r\n";
        $xml .= '      <Column ss:Width="32"/>' . "\r\n";
        // BUNDLING 3 cols
        $xml .= '      <Column ss:Width="28"/>' . "\r\n";
        $xml .= '      <Column ss:Width="28"/>' . "\r\n";
        $xml .= '      <Column ss:Width="25"/>' . "\r\n";
        // Alamat, HP, Marketting
        $xml .= '      <Column ss:Width="110"/>' . "\r\n";
        $xml .= '      <Column ss:Width="95"/>' . "\r\n";
        $xml .= '      <Column ss:Width="85"/>' . "\r\n";
        // Financial columns
        $xml .= '      <Column ss:Width="100"/>' . "\r\n"; // HARGA PAKET
        $xml .= '      <Column ss:Width="90"/>' . "\r\n"; // CREW FOTO
        $xml .= '      <Column ss:Width="85"/>' . "\r\n"; // CETAK 4R
        $xml .= '      <Column ss:Width="85"/>' . "\r\n"; // PACKAGING
        $xml .= '      <Column ss:Width="85"/>' . "\r\n"; // ALBUM
        $xml .= '      <Column ss:Width="110"/>' . "\r\n"; // CETAK JUMBO & FRAME
        $xml .= '      <Column ss:Width="85"/>' . "\r\n"; // CREW CINEMA
        $xml .= '      <Column ss:Width="85"/>' . "\r\n"; // LABA CINEMA
        $xml .= '      <Column ss:Width="85"/>' . "\r\n"; // CREW LIPUTAN
        $xml .= '      <Column ss:Width="85"/>' . "\r\n"; // LABA LIPUTAN
        $xml .= '      <Column ss:Width="85"/>' . "\r\n"; // CREW KONTEN
        $xml .= '      <Column ss:Width="85"/>' . "\r\n"; // LABA KONTEN
        $xml .= '      <Column ss:Width="85"/>' . "\r\n"; // SALES FEE
        $xml .= '      <Column ss:Width="60"/>' . "\r\n"; // DSC
        $xml .= '      <Column ss:Width="95"/>' . "\r\n"; // PENGELUARAN
        $xml .= '      <Column ss:Width="95"/>' . "\r\n"; // PENDAPATAN
        $xml .= '      <Column ss:Width="95"/>' . "\r\n"; // VALIDASI JUMLAH
        // EXTRAS
        $xml .= '      <Column ss:Width="70"/>' . "\r\n"; // Cetak
        $xml .= '      <Column ss:Width="100"/>' . "\r\n"; // Penambahan drive
        $xml .= '      <Column ss:Width="85"/>' . "\r\n"; // Transportasi
        $xml .= '      <Column ss:Width="85"/>' . "\r\n"; // JUMLAH
        $xml .= '      <Column ss:Width="110"/>' . "\r\n"; // KETERANGAN

        // Calculate Totals for this batch
        $totHargaPaket = 0; $totCrewFoto = 0; $totCetak4r = 0; $totPackaging = 0; $totAlbum = 0;
        $totFrame = 0; $totCrewCinema = 0; $totLabaCinema = 0; $totCrewLiputan = 0; $totLabaLiputan = 0;
        $totCrewKonten = 0; $totLabaKonten = 0; $totSalesFee = 0; $totDsc = 0;
        $totPengeluaran = 0; $totPendapatan = 0; $totValidasi = 0;
        $totExtraCetak = 0; $totExtraDrive = 0; $totExtraTransport = 0; $totExtraJumlah = 0;

        foreach ($bookings as $b) {
            $effectiveCosts = $this->getEffectiveCosts($b);
            $totHargaPaket += $this->cleanNumber($b->package_price);
            $totCrewFoto += $effectiveCosts['crew_foto'];
            $totCetak4r += $effectiveCosts['cetak_4r'];
            $totPackaging += $effectiveCosts['packaging'];
            $totAlbum += $effectiveCosts['album'];
            $totFrame += $effectiveCosts['frame'];
            $totCrewCinema += $effectiveCosts['crew_cinema'];
            $totLabaCinema += $effectiveCosts['laba_cinema'];
            $totCrewLiputan += $effectiveCosts['crew_liputan'];
            $totLabaLiputan += $effectiveCosts['laba_liputan'];
            $totCrewKonten += $effectiveCosts['crew_konten'];
            $totLabaKonten += $effectiveCosts['laba_konten'];
            $totSalesFee += $effectiveCosts['sales_fee'];
            $totDsc += $this->cleanNumber($b->discount ?? $b->discount_amount);

            $bOpCost = $this->cleanNumber($b->total_operational_cost);
            if ($bOpCost == 0) {
                $bOpCost = $effectiveCosts['crew_foto'] + $effectiveCosts['cetak_4r'] + $effectiveCosts['packaging'] + $effectiveCosts['album'] + $effectiveCosts['frame'] + $effectiveCosts['crew_cinema'] + $effectiveCosts['crew_liputan'] + $effectiveCosts['crew_konten'] + $effectiveCosts['sales_fee'];
            }
            $totPengeluaran += $bOpCost;

            $bIncome = $this->cleanNumber($b->total_income) ?: $this->cleanNumber($b->package_price);
            $totPendapatan += $bIncome;
            $totValidasi += $this->cleanNumber($b->package_price);

            if (!empty($b->extras_detail) && is_array($b->extras_detail)) {
                $totExtraCetak += $this->cleanNumber($b->extras_detail['cetak'] ?? 0);
                $totExtraDrive += $this->cleanNumber($b->extras_detail['drive'] ?? 0);
                $totExtraTransport += $this->cleanNumber($b->extras_detail['transport'] ?? 0);
                $totExtraJumlah += $this->cleanNumber($b->extras_detail['jumlah'] ?? 0);
            }
        }

        $totMonthlyOps = $monthlyOps->sum('total');
        $totExpenses = $expenses->sum('amount');
        $labaBersih = $totPendapatan - $totMonthlyOps;
        $jumlahSetoran = 0 + $labaBersih + $totPackaging + $totExtraJumlah + $totExpenses + $totPackaging;

        // Banner Title Row (Row 1)
        $xml .= '      <Row ss:Height="36">' . "\r\n";
        $xml .= '        <Cell ss:MergeAcross="43" ss:StyleID="TitleBanner">' . "\r\n";
        $xml .= '          <Data ss:Type="String">LAPORAN LABA RUGI PENAPICT TAHUN ANGGARAN 2026/2027</Data>' . "\r\n";
        $xml .= '        </Cell>' . "\r\n";
        $xml .= '      </Row>' . "\r\n";

        // Row 2: Empty
        $xml .= '      <Row ss:Height="12"></Row>' . "\r\n";

        // Executive Summary Rows (Rows 3 to 8)
        $xml .= '      <Row ss:Height="22">' . "\r\n";
        $xml .= '        <Cell ss:Index="3" ss:StyleID="SummaryLabel"><Data ss:Type="String">Saldo Awal</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="SummaryValue"><Data ss:Type="Number">0</Data></Cell>' . "\r\n";
        $xml .= '      </Row>' . "\r\n";

        $xml .= '      <Row ss:Height="22">' . "\r\n";
        $xml .= '        <Cell ss:Index="3" ss:StyleID="SummaryLabel"><Data ss:Type="String">Saldo Akhir</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="SummaryValue"><Data ss:Type="Number">' . $totHargaPaket . '</Data></Cell>' . "\r\n";
        $xml .= '      </Row>' . "\r\n";

        $xml .= '      <Row ss:Height="22">' . "\r\n";
        $xml .= '        <Cell ss:Index="3" ss:StyleID="SummaryLabel"><Data ss:Type="String">Total Pengeluaran</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="SummaryValue"><Data ss:Type="Number">' . $totPengeluaran . '</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:Index="6" ss:MergeAcross="20" ss:StyleID="SummaryNote"><Data ss:Type="String">Danu TF ke Umi = RINCIAN BIAYA PROMO</Data></Cell>' . "\r\n";
        $xml .= '      </Row>' . "\r\n";

        $xml .= '      <Row ss:Height="22">' . "\r\n";
        $xml .= '        <Cell ss:Index="3" ss:StyleID="SummaryLabel"><Data ss:Type="String">Total Pendapatan</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="SummaryValue"><Data ss:Type="Number">' . $totPendapatan . '</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:Index="6" ss:MergeAcross="20" ss:StyleID="SummaryNote"><Data ss:Type="String">Umi TF ke Danu = JUMLAH SETORAN+FEE MARKETTING DANU+GAJI DIVISI STUDIO DANU</Data></Cell>' . "\r\n";
        $xml .= '      </Row>' . "\r\n";

        $xml .= '      <Row ss:Height="24">' . "\r\n";
        $xml .= '        <Cell ss:Index="3" ss:StyleID="SummaryLabel"><Data ss:Type="String">Laba Bersih</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="SummaryValue"><Data ss:Type="Number">' . $labaBersih . '</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:Index="6" ss:MergeAcross="30" ss:StyleID="SummaryNote"><Data ss:Type="String">Sudah Valid, Sudah dikurangi juga OPERASIONALBULANAN, jika ingin Hasilnya tidak selisih, maka Jumlahkan Laba Bersih+Operasional Bulanan+Total Pengeluaran, maka hasilnya akan sesuai dengan jumlah Saldo Akhir</Data></Cell>' . "\r\n";
        $xml .= '      </Row>' . "\r\n";

        $xml .= '      <Row ss:Height="26">' . "\r\n";
        $xml .= '        <Cell ss:Index="3" ss:StyleID="SummaryLabel"><Data ss:Type="String">Jumlah Setoran</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="SummaryValue"><Data ss:Type="Number">' . $jumlahSetoran . '</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:Index="6" ss:MergeAcross="30" ss:StyleID="SummaryNote"><Data ss:Type="String">jadi, untuk setoran dari rumus adalah SALDO AWAL+ LABA BERSIH +PACKAGING+EXTRAS+ (OPERASIONAL LAINNYA-digunakan untuk Validasi Jumlah Setoran, karena di Laba Bersih Sudah Dikurangi Operasional Bulanan, jadi di JUMLAH SETORAN harus ditambah OPERASIONAL LAINNYA supaya Netral)+Packaging (karena Danu yang belanja FD, Kotak FD, dll)</Data></Cell>' . "\r\n";
        $xml .= '      </Row>' . "\r\n";

        // Row 9: Empty
        $xml .= '      <Row ss:Height="14"></Row>' . "\r\n";

        // Table Header Row 1
        $xml .= '      <Row ss:Height="26">' . "\r\n";
        $xml .= '        <Cell ss:MergeDown="1" ss:StyleID="ColHeaderMain"><Data ss:Type="String">No</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:MergeDown="1" ss:StyleID="ColHeaderMain"><Data ss:Type="String">TGL</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:MergeDown="1" ss:StyleID="ColHeaderMain"><Data ss:Type="String">NAMA CLIENT</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:MergeAcross="3" ss:StyleID="ColHeaderMain"><Data ss:Type="String">PHOTO</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:MergeAcross="2" ss:StyleID="ColHeaderMain"><Data ss:Type="String">CINEMA</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:MergeAcross="2" ss:StyleID="ColHeaderMain"><Data ss:Type="String">LIPUTAN</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:MergeAcross="2" ss:StyleID="ColHeaderMain"><Data ss:Type="String">KONTEN</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:MergeAcross="2" ss:StyleID="ColHeaderMain"><Data ss:Type="String">BUNDLING</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:MergeDown="1" ss:StyleID="ColHeaderMain"><Data ss:Type="String">Alamat</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:MergeDown="1" ss:StyleID="ColHeaderMain"><Data ss:Type="String">NO.HP</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:MergeDown="1" ss:StyleID="ColHeaderMain"><Data ss:Type="String">MARKETTING</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:MergeDown="1" ss:StyleID="ColHeaderMain"><Data ss:Type="String">HARGA PAKET</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:MergeDown="1" ss:StyleID="ColHeaderMain"><Data ss:Type="String">CREW FOTO</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:MergeDown="1" ss:StyleID="ColHeaderMain"><Data ss:Type="String">CETAK 4R</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:MergeDown="1" ss:StyleID="ColHeaderMain"><Data ss:Type="String">PACKAGING</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:MergeDown="1" ss:StyleID="ColHeaderMain"><Data ss:Type="String">ALBUM</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:MergeDown="1" ss:StyleID="ColHeaderMain"><Data ss:Type="String">CETAK JUMBO &amp; FRAME</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:MergeDown="1" ss:StyleID="ColHeaderMain"><Data ss:Type="String">CREW CINEMA</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:MergeDown="1" ss:StyleID="ColHeaderMain"><Data ss:Type="String">LABA CINEMA</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:MergeDown="1" ss:StyleID="ColHeaderMain"><Data ss:Type="String">CREW LIPUTAN</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:MergeDown="1" ss:StyleID="ColHeaderMain"><Data ss:Type="String">LABA LIPUTAN</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:MergeDown="1" ss:StyleID="ColHeaderMain"><Data ss:Type="String">CREW KONTEN</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:MergeDown="1" ss:StyleID="ColHeaderMain"><Data ss:Type="String">LABA KONTEN</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:MergeDown="1" ss:StyleID="ColHeaderMain"><Data ss:Type="String">SALES FEE</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:MergeDown="1" ss:StyleID="ColHeaderMain"><Data ss:Type="String">DSC</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:MergeDown="1" ss:StyleID="ColHeaderMain"><Data ss:Type="String">PENGELUARAN</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:MergeDown="1" ss:StyleID="ColHeaderMain"><Data ss:Type="String">PENDAPATAN</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:MergeDown="1" ss:StyleID="ColHeaderMain"><Data ss:Type="String">VALIDASI JUMLAH</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:MergeAcross="4" ss:StyleID="ColHeaderMain"><Data ss:Type="String">EXTRAS</Data></Cell>' . "\r\n";
        $xml .= '      </Row>' . "\r\n";

        // Table Header Row 2 (Subheaders)
        $xml .= '      <Row ss:Height="22">' . "\r\n";
        // PHOTO (4 sub-cols)
        $xml .= '        <Cell ss:Index="4" ss:StyleID="ColSubHeader"><Data ss:Type="String">B</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="ColSubHeader"><Data ss:Type="String">E</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="ColSubHeader"><Data ss:Type="String">WB</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="ColSubHeader"><Data ss:Type="String">ROLL</Data></Cell>' . "\r\n";
        // CINEMA (3 sub-cols)
        $xml .= '        <Cell ss:StyleID="ColSubHeader"><Data ss:Type="String">B</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="ColSubHeader"><Data ss:Type="String">E</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="ColSubHeader"><Data ss:Type="String">L</Data></Cell>' . "\r\n";
        // LIPUTAN (3 sub-cols)
        $xml .= '        <Cell ss:StyleID="ColSubHeader"><Data ss:Type="String">B</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="ColSubHeader"><Data ss:Type="String">E</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="ColSubHeader"><Data ss:Type="String">L</Data></Cell>' . "\r\n";
        // KONTEN (3 sub-cols)
        $xml .= '        <Cell ss:StyleID="ColSubHeader"><Data ss:Type="String">H</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="ColSubHeader"><Data ss:Type="String">F</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="ColSubHeader"><Data ss:Type="String">SDE</Data></Cell>' . "\r\n";
        // BUNDLING (3 sub-cols)
        $xml .= '        <Cell ss:StyleID="ColSubHeader"><Data ss:Type="String">BK</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="ColSubHeader"><Data ss:Type="String">BC</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="ColSubHeader"><Data ss:Type="String">L</Data></Cell>' . "\r\n";
        // EXTRAS (5 sub-cols)
        $xml .= '        <Cell ss:Index="40" ss:StyleID="ColSubHeader"><Data ss:Type="String">Cetak</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="ColSubHeader"><Data ss:Type="String">Penambahan drive</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="ColSubHeader"><Data ss:Type="String">Transportasi</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="ColSubHeader"><Data ss:Type="String">JUMLAH</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="ColSubHeader"><Data ss:Type="String">KETERANGAN</Data></Cell>' . "\r\n";
        $xml .= '      </Row>' . "\r\n";

        // Data Rows
        $rowIdx = $startNo;
        foreach ($bookings as $b) {
            $formattedDate = $b->event_date ? date('d/m/Y', strtotime($b->event_date)) : '-';
            $features = $this->getEffectiveFeatures($b);
            $effectiveCosts = $this->getEffectiveCosts($b);

            $xml .= '      <Row ss:Height="19">' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellCenter"><Data ss:Type="Number">' . $rowIdx . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellCenter"><Data ss:Type="String">' . $this->xmlEscape($formattedDate) . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellText"><Data ss:Type="String">' . $this->xmlEscape($b->client_name) . '</Data></Cell>' . "\r\n";

            // PHOTO checkboxes: B, E, WB, ROLL -> rendered as ☐ (unchecked) or ☑ (checked)
            $xml .= '        <Cell ss:StyleID="CellCheckbox"><Data ss:Type="String">' . (in_array('B', $features['photo'] ?? []) ? '☑' : '☐') . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellCheckbox"><Data ss:Type="String">' . (in_array('E', $features['photo'] ?? []) ? '☑' : '☐') . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellCheckbox"><Data ss:Type="String">' . (in_array('WB', $features['photo'] ?? []) ? '☑' : '☐') . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellCheckbox"><Data ss:Type="String">' . (in_array('ROLL', $features['photo'] ?? []) ? '☑' : '☐') . '</Data></Cell>' . "\r\n";

            // CINEMA: B, E, L
            $xml .= '        <Cell ss:StyleID="CellCheckbox"><Data ss:Type="String">' . (in_array('B', $features['cinema'] ?? []) ? '☑' : '☐') . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellCheckbox"><Data ss:Type="String">' . (in_array('E', $features['cinema'] ?? []) ? '☑' : '☐') . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellCheckbox"><Data ss:Type="String">' . (in_array('L', $features['cinema'] ?? []) ? '☑' : '☐') . '</Data></Cell>' . "\r\n";

            // LIPUTAN: B, E, L
            $xml .= '        <Cell ss:StyleID="CellCheckbox"><Data ss:Type="String">' . (in_array('B', $features['liputan'] ?? []) ? '☑' : '☐') . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellCheckbox"><Data ss:Type="String">' . (in_array('E', $features['liputan'] ?? []) ? '☑' : '☐') . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellCheckbox"><Data ss:Type="String">' . (in_array('L', $features['liputan'] ?? []) ? '☑' : '☐') . '</Data></Cell>' . "\r\n";

            // KONTEN: H, F, SDE
            $xml .= '        <Cell ss:StyleID="CellCheckbox"><Data ss:Type="String">' . (in_array('H', $features['konten'] ?? []) ? '☑' : '☐') . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellCheckbox"><Data ss:Type="String">' . (in_array('F', $features['konten'] ?? []) ? '☑' : '☐') . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellCheckbox"><Data ss:Type="String">' . (in_array('SDE', $features['konten'] ?? []) ? '☑' : '☐') . '</Data></Cell>' . "\r\n";

            // BUNDLING: BK, BC, L
            $xml .= '        <Cell ss:StyleID="CellCheckbox"><Data ss:Type="String">' . (in_array('BK', $features['bundling'] ?? []) ? '☑' : '☐') . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellCheckbox"><Data ss:Type="String">' . (in_array('BC', $features['bundling'] ?? []) ? '☑' : '☐') . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellCheckbox"><Data ss:Type="String">' . (in_array('L', $features['bundling'] ?? []) ? '☑' : '☐') . '</Data></Cell>' . "\r\n";

            // Alamat, HP, Marketting
            $mktName = $b->marketing_name ?: 'Danu';
            $xml .= '        <Cell ss:StyleID="CellText"><Data ss:Type="String">' . $this->xmlEscape($b->client_address ?? '-') . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellCenter"><Data ss:Type="String">' . $this->xmlEscape($b->client_phone ?? $b->client_wa_number ?? '-') . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellText"><Data ss:Type="String">' . $this->xmlEscape($mktName) . '</Data></Cell>' . "\r\n";

            // Financial amounts
            $bPkgPrice = $this->cleanNumber($b->package_price);
            $bOpCost = $this->cleanNumber($b->total_operational_cost);
            if ($bOpCost == 0) {
                $bOpCost = $effectiveCosts['crew_foto'] + $effectiveCosts['cetak_4r'] + $effectiveCosts['packaging'] + $effectiveCosts['album'] + $effectiveCosts['frame'] + $effectiveCosts['crew_cinema'] + $effectiveCosts['crew_liputan'] + $effectiveCosts['crew_konten'] + $effectiveCosts['sales_fee'];
            }
            $bInc = $this->cleanNumber($b->total_income) ?: $bPkgPrice;
            $bProfit = $bInc - $bOpCost;

            $xml .= '        <Cell ss:StyleID="CellCurrency"><Data ss:Type="Number">' . $bPkgPrice . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellCurrency"><Data ss:Type="Number">' . $effectiveCosts['crew_foto'] . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellCurrency"><Data ss:Type="Number">' . $effectiveCosts['cetak_4r'] . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellCurrency"><Data ss:Type="Number">' . $effectiveCosts['packaging'] . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellCurrency"><Data ss:Type="Number">' . $effectiveCosts['album'] . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellCurrency"><Data ss:Type="Number">' . $effectiveCosts['frame'] . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellCurrency"><Data ss:Type="Number">' . $effectiveCosts['crew_cinema'] . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellCurrency"><Data ss:Type="Number">' . $effectiveCosts['laba_cinema'] . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellCurrency"><Data ss:Type="Number">' . $effectiveCosts['crew_liputan'] . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellCurrency"><Data ss:Type="Number">' . $effectiveCosts['laba_liputan'] . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellCurrency"><Data ss:Type="Number">' . $effectiveCosts['crew_konten'] . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellCurrency"><Data ss:Type="Number">' . $effectiveCosts['laba_konten'] . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellCurrency"><Data ss:Type="Number">' . $effectiveCosts['sales_fee'] . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellCurrency"><Data ss:Type="Number">' . $this->cleanNumber($b->discount ?? $b->discount_amount) . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellCurrency"><Data ss:Type="Number">' . $bOpCost . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellCurrency"><Data ss:Type="Number">' . $bProfit . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellCurrency"><Data ss:Type="Number">' . $bPkgPrice . '</Data></Cell>' . "\r\n";

            // EXTRAS details
            $ext = is_array($b->extras_detail) ? $b->extras_detail : [];
            $cetak = $this->cleanNumber($ext['cetak'] ?? 0);
            $drive = $this->cleanNumber($ext['drive'] ?? 0);
            $trans = $this->cleanNumber($ext['transport'] ?? 0);
            $extTotal = $this->cleanNumber($ext['jumlah'] ?? 0);
            if ($extTotal == 0 && ($cetak > 0 || $drive > 0 || $trans > 0)) {
                $extTotal = $cetak + $drive + $trans;
            }
            $extKet = $ext['keterangan'] ?? '';

            $xml .= '        <Cell ss:StyleID="' . ($cetak > 0 ? 'CellCurrency' : 'CellCurrencyZero') . '"><Data ss:Type="Number">' . $cetak . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="' . ($drive > 0 ? 'CellCurrency' : 'CellCurrencyZero') . '"><Data ss:Type="Number">' . $drive . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="' . ($trans > 0 ? 'CellCurrency' : 'CellCurrencyZero') . '"><Data ss:Type="Number">' . $trans . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="' . ($extTotal > 0 ? 'CellCurrency' : 'CellCurrencyZero') . '"><Data ss:Type="Number">' . $extTotal . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellText"><Data ss:Type="String">' . $this->xmlEscape($extKet) . '</Data></Cell>' . "\r\n";

            $xml .= '      </Row>' . "\r\n";
            $rowIdx++;
        }

        // TOTAL SUMMARY ROW
        $xml .= '      <Row ss:Height="24">' . "\r\n";
        $xml .= '        <Cell ss:MergeAcross="20" ss:StyleID="CellTotalLabel"><Data ss:Type="String">TOTAL (' . count($bookings) . ' ACARA)</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="CellTotalCurrency"><Data ss:Type="Number">' . $totHargaPaket . '</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="CellTotalCurrency"><Data ss:Type="Number">' . $totCrewFoto . '</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="CellTotalCurrency"><Data ss:Type="Number">' . $totCetak4r . '</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="CellTotalCurrency"><Data ss:Type="Number">' . $totPackaging . '</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="CellTotalCurrency"><Data ss:Type="Number">' . $totAlbum . '</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="CellTotalCurrency"><Data ss:Type="Number">' . $totFrame . '</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="CellTotalCurrency"><Data ss:Type="Number">' . $totCrewCinema . '</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="CellTotalCurrency"><Data ss:Type="Number">' . $totLabaCinema . '</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="CellTotalCurrency"><Data ss:Type="Number">' . $totCrewLiputan . '</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="CellTotalCurrency"><Data ss:Type="Number">' . $totLabaLiputan . '</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="CellTotalCurrency"><Data ss:Type="Number">' . $totCrewKonten . '</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="CellTotalCurrency"><Data ss:Type="Number">' . $totLabaKonten . '</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="CellTotalCurrency"><Data ss:Type="Number">' . $totSalesFee . '</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="CellTotalCurrency"><Data ss:Type="Number">' . $totDsc . '</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="CellTotalCurrency"><Data ss:Type="Number">' . $totPengeluaran . '</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="CellTotalCurrency"><Data ss:Type="Number">' . ($totPendapatan - $totPengeluaran) . '</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="CellTotalCurrency"><Data ss:Type="Number">' . $totValidasi . '</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="CellTotalCurrency"><Data ss:Type="Number">' . $totExtraCetak . '</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="CellTotalCurrency"><Data ss:Type="Number">' . $totExtraDrive . '</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="CellTotalCurrency"><Data ss:Type="Number">' . $totExtraTransport . '</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="CellTotalCurrency"><Data ss:Type="Number">' . $totExtraJumlah . '</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="CellTotalLabel"><Data ss:Type="String">-</Data></Cell>' . "\r\n";
        $xml .= '      </Row>' . "\r\n";

        $xml .= '    </Table>' . "\r\n";
        $xml .= '  </Worksheet>' . "\r\n";

        return $xml;
    }

    protected function buildStudioSheet($salaries): string
    {
        $xml = '  <Worksheet ss:Name="BEBAN GAJI DIVISI STUDIO">' . "\r\n";
        $xml .= '    <Table ss:DefaultRowHeight="20">' . "\r\n";
        $xml .= '      <Column ss:Width="160"/>' . "\r\n"; // Nama
        $xml .= '      <Column ss:Width="240"/>' . "\r\n"; // Jabatan
        $xml .= '      <Column ss:Width="50"/>' . "\r\n";  // QTY
        $xml .= '      <Column ss:Width="110"/>' . "\r\n"; // Harga
        $xml .= '      <Column ss:Width="120"/>' . "\r\n"; // Total

        // Header Title
        $xml .= '      <Row ss:Height="30">' . "\r\n";
        $xml .= '        <Cell ss:MergeAcross="4" ss:StyleID="TitleBanner"><Data ss:Type="String">MASTER HARGA</Data></Cell>' . "\r\n";
        $xml .= '      </Row>' . "\r\n";
        $xml .= '      <Row ss:Height="24">' . "\r\n";
        $xml .= '        <Cell ss:MergeAcross="4" ss:StyleID="SubTitleBanner"><Data ss:Type="String">PERIODE JANUARI - DESEMBER 2026</Data></Cell>' . "\r\n";
        $xml .= '      </Row>' . "\r\n";

        // Metadata
        $xml .= '      <Row ss:Height="18"><Cell ss:StyleID="MetaLabel"><Data ss:Type="String">Nama Bisnis</Data></Cell><Cell ss:MergeAcross="3" ss:StyleID="MetaValue"><Data ss:Type="String">Penapict</Data></Cell></Row>' . "\r\n";
        $xml .= '      <Row ss:Height="18"><Cell ss:StyleID="MetaLabel"><Data ss:Type="String">Nama Pemilik</Data></Cell><Cell ss:MergeAcross="3" ss:StyleID="MetaValue"><Data ss:Type="String">Dian Rahmandanu</Data></Cell></Row>' . "\r\n";
        $xml .= '      <Row ss:Height="18"><Cell ss:StyleID="MetaLabel"><Data ss:Type="String">Tahun Laporan</Data></Cell><Cell ss:MergeAcross="3" ss:StyleID="MetaValue"><Data ss:Type="String">2026</Data></Cell></Row>' . "\r\n";
        $xml .= '      <Row ss:Height="12"></Row>' . "\r\n";

        // Section Bar
        $xml .= '      <Row ss:Height="24">' . "\r\n";
        $xml .= '        <Cell ss:MergeAcross="4" ss:StyleID="SectionHeader"><Data ss:Type="String">Beban Gaji Divisi Studio</Data></Cell>' . "\r\n";
        $xml .= '      </Row>' . "\r\n";

        // Table Columns
        $xml .= '      <Row ss:Height="22">' . "\r\n";
        $xml .= '        <Cell ss:StyleID="ColHeaderMain"><Data ss:Type="String">Nama</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="ColHeaderMain"><Data ss:Type="String">Jabatan</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="ColHeaderMain"><Data ss:Type="String">QTY</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="ColHeaderMain"><Data ss:Type="String">Harga</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="ColHeaderMain"><Data ss:Type="String">Total</Data></Cell>' . "\r\n";
        $xml .= '      </Row>' . "\r\n";

        $grandTotal = 0;
        foreach ($salaries as $s) {
            $grandTotal += $this->cleanNumber($s->total);
            $xml .= '      <Row ss:Height="20">' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellText"><Data ss:Type="String">' . $this->xmlEscape($s->name) . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellText"><Data ss:Type="String">' . $this->xmlEscape($s->position ?? '-') . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellCenter"><Data ss:Type="Number">' . $this->cleanNumber($s->qty) . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellCurrency"><Data ss:Type="Number">' . $this->cleanNumber($s->price) . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellCurrency"><Data ss:Type="Number">' . $this->cleanNumber($s->total) . '</Data></Cell>' . "\r\n";
            $xml .= '      </Row>' . "\r\n";
        }

        // Total Row
        $xml .= '      <Row ss:Height="24">' . "\r\n";
        $xml .= '        <Cell ss:MergeAcross="3" ss:StyleID="CellTotalLabel"><Data ss:Type="String">Total</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="CellTotalCurrency"><Data ss:Type="Number">' . $grandTotal . '</Data></Cell>' . "\r\n";
        $xml .= '      </Row>' . "\r\n";

        $xml .= '    </Table>' . "\r\n";
        $xml .= '  </Worksheet>' . "\r\n";

        return $xml;
    }

    protected function buildSosmedSheet($salaries): string
    {
        $xml = '  <Worksheet ss:Name="BEBAN GAJI DIVISI SOSMED">' . "\r\n";
        $xml .= '    <Table ss:DefaultRowHeight="20">' . "\r\n";
        $xml .= '      <Column ss:Width="200"/>' . "\r\n"; // Nama
        $xml .= '      <Column ss:Width="50"/>' . "\r\n";  // QTY
        $xml .= '      <Column ss:Width="110"/>' . "\r\n"; // Harga
        $xml .= '      <Column ss:Width="120"/>' . "\r\n"; // Total

        // Header Title
        $xml .= '      <Row ss:Height="30">' . "\r\n";
        $xml .= '        <Cell ss:MergeAcross="3" ss:StyleID="TitleBanner"><Data ss:Type="String">MASTER HARGA</Data></Cell>' . "\r\n";
        $xml .= '      </Row>' . "\r\n";
        $xml .= '      <Row ss:Height="24">' . "\r\n";
        $xml .= '        <Cell ss:MergeAcross="3" ss:StyleID="SubTitleBanner"><Data ss:Type="String">PERIODE JANUARI - DESEMBER 2026</Data></Cell>' . "\r\n";
        $xml .= '      </Row>' . "\r\n";

        // Metadata
        $xml .= '      <Row ss:Height="18"><Cell ss:StyleID="MetaLabel"><Data ss:Type="String">Nama Bisnis</Data></Cell><Cell ss:MergeAcross="2" ss:StyleID="MetaValue"><Data ss:Type="String">Penapict</Data></Cell></Row>' . "\r\n";
        $xml .= '      <Row ss:Height="18"><Cell ss:StyleID="MetaLabel"><Data ss:Type="String">Nama Pemilik</Data></Cell><Cell ss:MergeAcross="2" ss:StyleID="MetaValue"><Data ss:Type="String">Dian Rahmandanu</Data></Cell></Row>' . "\r\n";
        $xml .= '      <Row ss:Height="18"><Cell ss:StyleID="MetaLabel"><Data ss:Type="String">Tahun Laporan</Data></Cell><Cell ss:MergeAcross="2" ss:StyleID="MetaValue"><Data ss:Type="String">2026</Data></Cell></Row>' . "\r\n";
        $xml .= '      <Row ss:Height="12"></Row>' . "\r\n";

        // Section Bar
        $xml .= '      <Row ss:Height="24">' . "\r\n";
        $xml .= '        <Cell ss:MergeAcross="3" ss:StyleID="SectionHeader"><Data ss:Type="String">Beban Gaji Divisi Sosmed</Data></Cell>' . "\r\n";
        $xml .= '      </Row>' . "\r\n";

        // Table Columns
        $xml .= '      <Row ss:Height="22">' . "\r\n";
        $xml .= '        <Cell ss:StyleID="ColHeaderMain"><Data ss:Type="String">Nama</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="ColHeaderMain"><Data ss:Type="String">QTY</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="ColHeaderMain"><Data ss:Type="String">Harga</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="ColHeaderMain"><Data ss:Type="String">Total</Data></Cell>' . "\r\n";
        $xml .= '      </Row>' . "\r\n";

        $grandTotal = 0;
        foreach ($salaries as $s) {
            $grandTotal += $this->cleanNumber($s->total);
            $xml .= '      <Row ss:Height="20">' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellText"><Data ss:Type="String">' . $this->xmlEscape($s->name) . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellCenter"><Data ss:Type="Number">' . $this->cleanNumber($s->qty) . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellCurrency"><Data ss:Type="Number">' . $this->cleanNumber($s->price) . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellCurrency"><Data ss:Type="Number">' . $this->cleanNumber($s->total) . '</Data></Cell>' . "\r\n";
            $xml .= '      </Row>' . "\r\n";
        }

        // Total Row
        $xml .= '      <Row ss:Height="24">' . "\r\n";
        $xml .= '        <Cell ss:MergeAcross="2" ss:StyleID="CellTotalLabel"><Data ss:Type="String">Total</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="CellTotalCurrency"><Data ss:Type="Number">' . $grandTotal . '</Data></Cell>' . "\r\n";
        $xml .= '      </Row>' . "\r\n";

        $xml .= '    </Table>' . "\r\n";
        $xml .= '  </Worksheet>' . "\r\n";

        return $xml;
    }

    protected function buildFgVgSheet($bookings, array $crewList): string
    {
        $xml = '  <Worksheet ss:Name="BEBAN GAJI KARYAWAN DIVISI FG &amp; VG">' . "\r\n";
        $xml .= '    <Table ss:DefaultRowHeight="20">' . "\r\n";
        $xml .= '      <Column ss:Width="30"/>' . "\r\n"; // No
        $xml .= '      <Column ss:Width="75"/>' . "\r\n"; // TGL
        $xml .= '      <Column ss:Width="130"/>' . "\r\n"; // CLIENT

        foreach ($crewList as $c) {
            $xml .= '      <Column ss:Width="75"/>' . "\r\n";
        }
        $xml .= '      <Column ss:Width="100"/>' . "\r\n"; // TOTAL PER EVENT

        // Title Row
        $xml .= '      <Row ss:Height="30">' . "\r\n";
        $xml .= '        <Cell ss:MergeAcross="' . (count($crewList) + 3) . '" ss:StyleID="TitleBanner">' . "\r\n";
        $xml .= '          <Data ss:Type="String">BEBAN GAJI KARYAWAN DIVISI FG &amp; VG</Data>' . "\r\n";
        $xml .= '        </Cell>' . "\r\n";
        $xml .= '      </Row>' . "\r\n";

        // Header Row
        $xml .= '      <Row ss:Height="24">' . "\r\n";
        $xml .= '        <Cell ss:StyleID="ColHeaderMain"><Data ss:Type="String">No</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="ColHeaderMain"><Data ss:Type="String">Tanggal</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="ColHeaderMain"><Data ss:Type="String">Nama Client</Data></Cell>' . "\r\n";
        foreach ($crewList as $c) {
            $xml .= '        <Cell ss:StyleID="ColHeaderMain"><Data ss:Type="String">' . $this->xmlEscape($c) . '</Data></Cell>' . "\r\n";
        }
        $xml .= '        <Cell ss:StyleID="ColHeaderMain"><Data ss:Type="String">JUMLAH</Data></Cell>' . "\r\n";
        $xml .= '      </Row>' . "\r\n";

        $crewTotals = array_fill_keys($crewList, 0);
        $grandTotal = 0;
        $rowIdx = 1;

        foreach ($bookings as $b) {
            $formattedDate = $b->event_date ? date('d/m/Y', strtotime($b->event_date)) : '-';
            $details = $this->getEffectiveCrewFees($b, $crewList);
            $eventTotal = 0;

            $xml .= '      <Row ss:Height="19">' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellCenter"><Data ss:Type="Number">' . $rowIdx . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellCenter"><Data ss:Type="String">' . $this->xmlEscape($formattedDate) . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellText"><Data ss:Type="String">' . $this->xmlEscape($b->client_name) . '</Data></Cell>' . "\r\n";

            foreach ($crewList as $c) {
                $fee = $this->cleanNumber($details[$c] ?? 0);
                $eventTotal += $fee;
                $crewTotals[$c] += $fee;

                $xml .= '        <Cell ss:StyleID="' . ($fee > 0 ? 'CellCurrency' : 'CellCurrencyZero') . '"><Data ss:Type="Number">' . $fee . '</Data></Cell>' . "\r\n";
            }

            $grandTotal += $eventTotal;
            $xml .= '        <Cell ss:StyleID="CellCurrency"><Data ss:Type="Number">' . $eventTotal . '</Data></Cell>' . "\r\n";
            $xml .= '      </Row>' . "\r\n";
            $rowIdx++;
        }

        // Summary Total Row
        $xml .= '      <Row ss:Height="24">' . "\r\n";
        $xml .= '        <Cell ss:MergeAcross="2" ss:StyleID="CellTotalLabel"><Data ss:Type="String">TOTAL GAJI PER KARYAWAN</Data></Cell>' . "\r\n";
        foreach ($crewList as $c) {
            $xml .= '        <Cell ss:StyleID="CellTotalCurrency"><Data ss:Type="Number">' . $crewTotals[$c] . '</Data></Cell>' . "\r\n";
        }
        $xml .= '        <Cell ss:StyleID="CellTotalCurrency"><Data ss:Type="Number">' . $grandTotal . '</Data></Cell>' . "\r\n";
        $xml .= '      </Row>' . "\r\n";

        $xml .= '    </Table>' . "\r\n";
        $xml .= '  </Worksheet>' . "\r\n";

        return $xml;
    }

    protected function buildMonthlyOpsSheet($monthlyOps): string
    {
        $xml = '  <Worksheet ss:Name="OPERASIONAL BULANAN">' . "\r\n";
        $xml .= '    <Table ss:DefaultRowHeight="20">' . "\r\n";
        $xml .= '      <Column ss:Width="40"/>' . "\r\n";  // No
        $xml .= '      <Column ss:Width="250"/>' . "\r\n"; // Nama Pengeluaran
        $xml .= '      <Column ss:Width="50"/>' . "\r\n";  // QTY
        $xml .= '      <Column ss:Width="120"/>' . "\r\n"; // Biaya
        $xml .= '      <Column ss:Width="130"/>' . "\r\n"; // Total

        // Header Title
        $xml .= '      <Row ss:Height="30">' . "\r\n";
        $xml .= '        <Cell ss:MergeAcross="4" ss:StyleID="TitleBanner"><Data ss:Type="String">OPERASIONAL BULANAN PENAPICT</Data></Cell>' . "\r\n";
        $xml .= '      </Row>' . "\r\n";
        $xml .= '      <Row ss:Height="24">' . "\r\n";
        $xml .= '        <Cell ss:MergeAcross="4" ss:StyleID="SubTitleBanner"><Data ss:Type="String">BIAYA OPERASIONAL RUTIN TETAP</Data></Cell>' . "\r\n";
        $xml .= '      </Row>' . "\r\n";

        // Columns
        $xml .= '      <Row ss:Height="22">' . "\r\n";
        $xml .= '        <Cell ss:StyleID="ColHeaderMain"><Data ss:Type="String">No</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="ColHeaderMain"><Data ss:Type="String">Nama Pengeluaran</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="ColHeaderMain"><Data ss:Type="String">QTY</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="ColHeaderMain"><Data ss:Type="String">Biaya (Rp)</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="ColHeaderMain"><Data ss:Type="String">Total (Rp)</Data></Cell>' . "\r\n";
        $xml .= '      </Row>' . "\r\n";

        $idx = 1;
        $grandTotal = 0;
        foreach ($monthlyOps as $op) {
            $grandTotal += $this->cleanNumber($op->total);
            $xml .= '      <Row ss:Height="20">' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellCenter"><Data ss:Type="Number">' . $idx . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellText"><Data ss:Type="String">' . $this->xmlEscape($op->name) . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellCenter"><Data ss:Type="Number">' . $this->cleanNumber($op->qty) . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellCurrency"><Data ss:Type="Number">' . $this->cleanNumber($op->price) . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellCurrency"><Data ss:Type="Number">' . $this->cleanNumber($op->total) . '</Data></Cell>' . "\r\n";
            $xml .= '      </Row>' . "\r\n";
            $idx++;
        }

        // Total
        $xml .= '      <Row ss:Height="24">' . "\r\n";
        $xml .= '        <Cell ss:MergeAcross="3" ss:StyleID="CellTotalLabel"><Data ss:Type="String">TOTAL OPERASIONAL BULANAN</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="CellTotalCurrency"><Data ss:Type="Number">' . $grandTotal . '</Data></Cell>' . "\r\n";
        $xml .= '      </Row>' . "\r\n";

        $xml .= '    </Table>' . "\r\n";
        $xml .= '  </Worksheet>' . "\r\n";

        return $xml;
    }

    protected function buildOtherExpensesSheet($expenses): string
    {
        $xml = '  <Worksheet ss:Name="OPERASIONAL LAINNYA">' . "\r\n";
        $xml .= '    <Table ss:DefaultRowHeight="20">' . "\r\n";
        $xml .= '      <Column ss:Width="40"/>' . "\r\n";  // No
        $xml .= '      <Column ss:Width="85"/>' . "\r\n";  // Tanggal
        $xml .= '      <Column ss:Width="240"/>' . "\r\n"; // Keterangan
        $xml .= '      <Column ss:Width="80"/>' . "\r\n";  // Deskripsi / Qty
        $xml .= '      <Column ss:Width="130"/>' . "\r\n"; // Jumlah Biaya

        // Header Title
        $xml .= '      <Row ss:Height="30">' . "\r\n";
        $xml .= '        <Cell ss:MergeAcross="4" ss:StyleID="TitleBanner"><Data ss:Type="String">OPERASIONAL LAINNYA</Data></Cell>' . "\r\n";
        $xml .= '      </Row>' . "\r\n";

        // Columns
        $xml .= '      <Row ss:Height="22">' . "\r\n";
        $xml .= '        <Cell ss:StyleID="ColHeaderMain"><Data ss:Type="String">No</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="ColHeaderMain"><Data ss:Type="String">Tanggal</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="ColHeaderMain"><Data ss:Type="String">Keterangan / Nama Pengeluaran</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="ColHeaderMain"><Data ss:Type="String">Deskripsi</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="ColHeaderMain"><Data ss:Type="String">Jumlah Biaya (Rp)</Data></Cell>' . "\r\n";
        $xml .= '      </Row>' . "\r\n";

        $idx = 1;
        $grandTotal = 0;
        foreach ($expenses as $e) {
            $grandTotal += $this->cleanNumber($e->amount);
            $formattedDate = $e->date ? date('d/m/Y', strtotime($e->date)) : '-';

            $xml .= '      <Row ss:Height="20">' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellCenter"><Data ss:Type="Number">' . $idx . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellCenter"><Data ss:Type="String">' . $this->xmlEscape($formattedDate) . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellText"><Data ss:Type="String">' . $this->xmlEscape($e->name) . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellCenter"><Data ss:Type="String">' . $this->xmlEscape($e->description ?? '-') . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellCurrency"><Data ss:Type="Number">' . $this->cleanNumber($e->amount) . '</Data></Cell>' . "\r\n";
            $xml .= '      </Row>' . "\r\n";
            $idx++;
        }

        // Total
        $xml .= '      <Row ss:Height="24">' . "\r\n";
        $xml .= '        <Cell ss:MergeAcross="3" ss:StyleID="CellTotalLabel"><Data ss:Type="String">TOTAL OPERASIONAL LAINNYA</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="CellTotalCurrency"><Data ss:Type="Number">' . $grandTotal . '</Data></Cell>' . "\r\n";
        $xml .= '      </Row>' . "\r\n";

        $xml .= '    </Table>' . "\r\n";
        $xml .= '  </Worksheet>' . "\r\n";

        return $xml;
    }

    protected function buildDanuMarketingSheet($bookings): string
    {
        $xml = '  <Worksheet ss:Name="FEE MARKETING DANU">' . "\r\n";
        $xml .= '    <Table ss:DefaultRowHeight="20">' . "\r\n";
        $xml .= '      <Column ss:Width="40"/>' . "\r\n";  // No
        $xml .= '      <Column ss:Width="85"/>' . "\r\n";  // Tanggal
        $xml .= '      <Column ss:Width="160"/>' . "\r\n"; // Nama Klien
        $xml .= '      <Column ss:Width="200"/>' . "\r\n"; // Paket Layanan
        $xml .= '      <Column ss:Width="120"/>' . "\r\n"; // Harga Paket
        $xml .= '      <Column ss:Width="130"/>' . "\r\n"; // Sales Fee Danu
        $xml .= '      <Column ss:Width="120"/>' . "\r\n"; // Keterangan

        // Header Title
        $xml .= '      <Row ss:Height="30">' . "\r\n";
        $xml .= '        <Cell ss:MergeAcross="6" ss:StyleID="TitleBanner"><Data ss:Type="String">FEE MARKETING DANU</Data></Cell>' . "\r\n";
        $xml .= '      </Row>' . "\r\n";

        // Columns
        $xml .= '      <Row ss:Height="22">' . "\r\n";
        $xml .= '        <Cell ss:StyleID="ColHeaderMain"><Data ss:Type="String">No</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="ColHeaderMain"><Data ss:Type="String">Tanggal</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="ColHeaderMain"><Data ss:Type="String">Nama Klien</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="ColHeaderMain"><Data ss:Type="String">Paket Layanan</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="ColHeaderMain"><Data ss:Type="String">Harga Paket</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="ColHeaderMain"><Data ss:Type="String">Fee Marketing (Rp)</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="ColHeaderMain"><Data ss:Type="String">Keterangan</Data></Cell>' . "\r\n";
        $xml .= '      </Row>' . "\r\n";

        $idx = 1;
        $grandTotal = 0;
        foreach ($bookings as $b) {
            $mkt = strtolower(trim($b->marketing_name ?? ''));
            if ($mkt !== '' && $mkt !== 'danu') {
                continue;
            }

            $effectiveCosts = $this->getEffectiveCosts($b);
            $fee = $effectiveCosts['sales_fee'];
            $grandTotal += $fee;
            $formattedDate = $b->event_date ? date('d/m/Y', strtotime($b->event_date)) : '-';

            $xml .= '      <Row ss:Height="20">' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellCenter"><Data ss:Type="Number">' . $idx . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellCenter"><Data ss:Type="String">' . $this->xmlEscape($formattedDate) . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellText"><Data ss:Type="String">' . $this->xmlEscape($b->client_name) . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellText"><Data ss:Type="String">' . $this->xmlEscape($b->package_name ?? '-') . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellCurrency"><Data ss:Type="Number">' . $this->cleanNumber($b->package_price) . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellCurrency"><Data ss:Type="Number">' . $fee . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellText"><Data ss:Type="String">Marketing Danu</Data></Cell>' . "\r\n";
            $xml .= '      </Row>' . "\r\n";
            $idx++;
        }

        // Total
        $xml .= '      <Row ss:Height="24">' . "\r\n";
        $xml .= '        <Cell ss:MergeAcross="4" ss:StyleID="CellTotalLabel"><Data ss:Type="String">TOTAL FEE MARKETING DANU</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="CellTotalCurrency"><Data ss:Type="Number">' . $grandTotal . '</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="CellTotalLabel"><Data ss:Type="String">-</Data></Cell>' . "\r\n";
        $xml .= '      </Row>' . "\r\n";

        $xml .= '    </Table>' . "\r\n";
        $xml .= '  </Worksheet>' . "\r\n";

        return $xml;
    }

    protected function buildAlbumSheet($albums): string
    {
        $xml = '  <Worksheet ss:Name="ALBUM">' . "\r\n";
        $xml .= '    <Table ss:DefaultRowHeight="20">' . "\r\n";
        $xml .= '      <Column ss:Width="40"/>' . "\r\n";  // No
        $xml .= '      <Column ss:Width="100"/>' . "\r\n"; // Kode
        $xml .= '      <Column ss:Width="160"/>' . "\r\n"; // Nama Album
        $xml .= '      <Column ss:Width="90"/>' . "\r\n";  // Kategori
        $xml .= '      <Column ss:Width="70"/>' . "\r\n";  // Stok Sistem
        $xml .= '      <Column ss:Width="60"/>' . "\r\n";  // Satuan
        $xml .= '      <Column ss:Width="110"/>' . "\r\n"; // Harga Satuan
        $xml .= '      <Column ss:Width="130"/>' . "\r\n"; // Total Nilai Stok
        $xml .= '      <Column ss:Width="80"/>' . "\r\n";  // Min Stok
        $xml .= '      <Column ss:Width="200"/>' . "\r\n"; // Keterangan

        // Header Title
        $xml .= '      <Row ss:Height="30">' . "\r\n";
        $xml .= '        <Cell ss:MergeAcross="9" ss:StyleID="TitleBanner"><Data ss:Type="String">MASTER DATA INVENTARIS ALBUM</Data></Cell>' . "\r\n";
        $xml .= '      </Row>' . "\r\n";
        $xml .= '      <Row ss:Height="24">' . "\r\n";
        $xml .= '        <Cell ss:MergeAcross="9" ss:StyleID="SubTitleBanner"><Data ss:Type="String">TERKONEKSI DENGAN STOCK OPNAME &amp; PERSEDIAAN</Data></Cell>' . "\r\n";
        $xml .= '      </Row>' . "\r\n";

        // Columns
        $xml .= '      <Row ss:Height="22">' . "\r\n";
        $xml .= '        <Cell ss:StyleID="ColHeaderMain"><Data ss:Type="String">No</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="ColHeaderMain"><Data ss:Type="String">Kode Barang</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="ColHeaderMain"><Data ss:Type="String">Nama Album</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="ColHeaderMain"><Data ss:Type="String">Kategori</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="ColHeaderMain"><Data ss:Type="String">Stok Sistem</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="ColHeaderMain"><Data ss:Type="String">Satuan</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="ColHeaderMain"><Data ss:Type="String">Harga Satuan (Rp)</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="ColHeaderMain"><Data ss:Type="String">Total Nilai Stok (Rp)</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="ColHeaderMain"><Data ss:Type="String">Batas Min</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="ColHeaderMain"><Data ss:Type="String">Keterangan</Data></Cell>' . "\r\n";
        $xml .= '      </Row>' . "\r\n";

        $idx = 1;
        $grandTotalValue = 0;
        foreach ($albums as $alb) {
            $stok = $this->cleanNumber($alb->stock_system);
            $harga = $this->cleanNumber($alb->price);
            $totalVal = $stok * $harga;
            $grandTotalValue += $totalVal;

            $xml .= '      <Row ss:Height="20">' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellCenter"><Data ss:Type="Number">' . $idx . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellCenter"><Data ss:Type="String">' . $this->xmlEscape($alb->code ?? '-') . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellText"><Data ss:Type="String">' . $this->xmlEscape($alb->name) . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellCenter"><Data ss:Type="String">' . $this->xmlEscape($alb->category ?? 'Album') . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellCenter"><Data ss:Type="Number">' . $stok . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellCenter"><Data ss:Type="String">' . $this->xmlEscape($alb->unit ?? 'unit') . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellCurrency"><Data ss:Type="Number">' . $harga . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellCurrency"><Data ss:Type="Number">' . $totalVal . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellCenter"><Data ss:Type="Number">' . $this->cleanNumber($alb->min_stock) . '</Data></Cell>' . "\r\n";
            $xml .= '        <Cell ss:StyleID="CellText"><Data ss:Type="String">' . $this->xmlEscape($alb->notes ?? '-') . '</Data></Cell>' . "\r\n";
            $xml .= '      </Row>' . "\r\n";
            $idx++;
        }

        // Total
        $xml .= '      <Row ss:Height="24">' . "\r\n";
        $xml .= '        <Cell ss:MergeAcross="6" ss:StyleID="CellTotalLabel"><Data ss:Type="String">TOTAL NILAI PERSEDIAAN ALBUM</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:StyleID="CellTotalCurrency"><Data ss:Type="Number">' . $grandTotalValue . '</Data></Cell>' . "\r\n";
        $xml .= '        <Cell ss:MergeAcross="1" ss:StyleID="CellTotalLabel"><Data ss:Type="String">-</Data></Cell>' . "\r\n";
        $xml .= '      </Row>' . "\r\n";

        $xml .= '    </Table>' . "\r\n";
        $xml .= '  </Worksheet>' . "\r\n";

        return $xml;
    }

    protected function cleanNumber($val): float
    {
        if ($val === null || $val === '') {
            return 0.0;
        }
        if (is_numeric($val)) {
            return (float) $val;
        }
        $str = (string) $val;
        $cleaned = preg_replace('/[^\d.,-]/', '', $str);
        if (preg_match('/^\d{1,3}(,\d{3})+(\.\d+)?$/', $cleaned)) {
            $cleaned = str_replace(',', '', $cleaned);
        } elseif (preg_match('/^\d{1,3}(\.\d{3})+(,\d+)?$/', $cleaned)) {
            $cleaned = str_replace('.', '', $cleaned);
            $cleaned = str_replace(',', '.', $cleaned);
        } else {
            $cleaned = str_replace(',', '', $cleaned);
        }
        return is_numeric($cleaned) ? (float) $cleaned : 0.0;
    }

    protected function xmlEscape($str): string
    {
        if ($str === null || $str === '') {
            return '';
        }
        return htmlspecialchars((string)$str, ENT_XML1, 'UTF-8');
    }
}
