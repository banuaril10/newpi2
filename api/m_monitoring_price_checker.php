<?php
// Load PHPSpreadsheet (sesuaikan path autoload dengan project Anda)
require_once 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

// Cek apakah tombol export ditekan
if(isset($_POST['export_excel']) && $_POST['export_excel'] == '1') {
    $date_start = $_POST['start_date'] ?? date('Y-m-d', strtotime('-7 days'));
    $date_end = $_POST['end_date'] ?? date('Y-m-d');
    $sku_filter = $_POST['sku'] ?? '';
    $location_filter = $_POST['location'] ?? '';
    
    $spreadsheet = new Spreadsheet();
    
    // ==================== SHEET 1: UNIQUE SKU PER KATEGORI ====================
    $sheet1 = $spreadsheet->getActiveSheet();
    $sheet1->setTitle('Unique SKU per Kategori');
    
    // Header
    $sheet1->setCellValue('A1', 'UNIQUE SKU PER KATEGORI');
    $sheet1->setCellValue('A2', 'Periode: ' . date('d-m-Y', strtotime($date_start)) . ' s/d ' . date('d-m-Y', strtotime($date_end)));
    $sheet1->setCellValue('A4', 'No');
    $sheet1->setCellValue('B4', 'Kategori');
    $sheet1->setCellValue('C4', 'Unique SKU');
    
    // Style header
    $sheet1->getStyle('A4:C4')->getFont()->setBold(true);
    $sheet1->getStyle('A4:C4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF2c7da0');
    $sheet1->getStyle('A4:C4')->getFont()->getColor()->setARGB('FFFFFFFF');
    
    // Query data
    $catQuery = "SELECT ims.cat_name as category, COUNT(DISTINCT pa.sku) as unique_sku
    FROM price_audit pa
    INNER JOIN in_item ims ON pa.sku = ims.sku
    WHERE pa.scanfrom = 'price_checker' AND DATE(pa.insertdate) BETWEEN ? AND ?";
    $catParams = [$date_start, $date_end];
    if (!empty($sku_filter)) { $catQuery .= " AND pa.sku LIKE ?"; $catParams[] = "%$sku_filter%"; }
    if (!empty($location_filter)) { $catQuery .= " AND pa.id_location = ?"; $catParams[] = $location_filter; }
    $catQuery .= " GROUP BY ims.cat_name ORDER BY unique_sku DESC";
    $stmt = $connec->prepare($catQuery);
    $stmt->execute($catParams);
    $catData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $row = 5;
    $no = 1;
    foreach($catData as $c) {
        $sheet1->setCellValue('A' . $row, $no);
        $sheet1->setCellValue('B' . $row, $c['category']);
        $sheet1->setCellValue('C' . $row, $c['unique_sku']);
        $row++;
        $no++;
    }
    
    // Auto size columns
    foreach(range('A','C') as $col) {
        $sheet1->getColumnDimension($col)->setAutoSize(true);
    }
    
    // ==================== SHEET 2: TOTAL SCAN PER KATEGORI ====================
    $sheet2 = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Total Scan per Kategori');
    $spreadsheet->addSheet($sheet2, 1);
    
    $sheet2->setCellValue('A1', 'TOTAL SCAN PER KATEGORI');
    $sheet2->setCellValue('A2', 'Periode: ' . date('d-m-Y', strtotime($date_start)) . ' s/d ' . date('d-m-Y', strtotime($date_end)));
    $sheet2->setCellValue('A4', 'No');
    $sheet2->setCellValue('B4', 'Kategori');
    $sheet2->setCellValue('C4', 'Total Scan');
    
    $sheet2->getStyle('A4:C4')->getFont()->setBold(true);
    $sheet2->getStyle('A4:C4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF2c7da0');
    $sheet2->getStyle('A4:C4')->getFont()->getColor()->setARGB('FFFFFFFF');
    
    $monQuery = "SELECT ims.cat_name as category, COUNT(*) as total_scan
    FROM price_audit pa
    INNER JOIN in_item ims ON pa.sku = ims.sku
    WHERE pa.scanfrom = 'price_checker' AND DATE(pa.insertdate) BETWEEN ? AND ?";
    $monParams = [$date_start, $date_end];
    if (!empty($sku_filter)) { $monQuery .= " AND pa.sku LIKE ?"; $monParams[] = "%$sku_filter%"; }
    if (!empty($location_filter)) { $monQuery .= " AND pa.id_location = ?"; $monParams[] = $location_filter; }
    $monQuery .= " GROUP BY ims.cat_name ORDER BY total_scan DESC";
    $stmt = $connec->prepare($monQuery);
    $stmt->execute($monParams);
    $monData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $row = 5;
    $no = 1;
    foreach($monData as $m) {
        $sheet2->setCellValue('A' . $row, $no);
        $sheet2->setCellValue('B' . $row, $m['category']);
        $sheet2->setCellValue('C' . $row, $m['total_scan']);
        $row++;
        $no++;
    }
    
    foreach(range('A','C') as $col) {
        $sheet2->getColumnDimension($col)->setAutoSize(true);
    }
    
    // ==================== SHEET 3: AKTIVITAS PER TANGGAL ====================
    $sheet3 = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Aktivitas per Tanggal');
    $spreadsheet->addSheet($sheet3, 2);
    
    $sheet3->setCellValue('A1', 'AKTIVITAS PRICE CHECKER PER TANGGAL');
    $sheet3->setCellValue('A2', 'Periode: ' . date('d-m-Y', strtotime($date_start)) . ' s/d ' . date('d-m-Y', strtotime($date_end)));
    $sheet3->setCellValue('A4', 'No');
    $sheet3->setCellValue('B4', 'Tanggal');
    $sheet3->setCellValue('C4', 'SKU Unik');
    $sheet3->setCellValue('D4', 'Total Scan');
    
    $sheet3->getStyle('A4:D4')->getFont()->setBold(true);
    $sheet3->getStyle('A4:D4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF2c7da0');
    $sheet3->getStyle('A4:D4')->getFont()->getColor()->setARGB('FFFFFFFF');
    
    $dateQuery = "SELECT DATE(insertdate) as audit_date, COUNT(DISTINCT sku) as sku_count, COUNT(*) as total_records
    FROM price_audit 
    WHERE DATE(insertdate) BETWEEN ? AND ? AND scanfrom = 'price_checker'";
    $dateParams = [$date_start, $date_end];
    if (!empty($sku_filter)) { $dateQuery .= " AND sku LIKE ?"; $dateParams[] = "%$sku_filter%"; }
    if (!empty($location_filter)) { $dateQuery .= " AND id_location = ?"; $dateParams[] = $location_filter; }
    $dateQuery .= " GROUP BY DATE(insertdate) ORDER BY audit_date DESC";
    $stmt = $connec->prepare($dateQuery);
    $stmt->execute($dateParams);
    $dateData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $row = 5;
    $no = 1;
    foreach($dateData as $d) {
        $sheet3->setCellValue('A' . $row, $no);
        $sheet3->setCellValue('B' . $row, date('d-m-Y', strtotime($d['audit_date'])));
        $sheet3->setCellValue('C' . $row, $d['sku_count']);
        $sheet3->setCellValue('D' . $row, $d['total_records']);
        $row++;
        $no++;
    }
    
    foreach(range('A','D') as $col) {
        $sheet3->getColumnDimension($col)->setAutoSize(true);
    }
    
    // Set active sheet ke sheet pertama
    $spreadsheet->setActiveSheetIndex(0);
    
    // Output file Excel
    $filename = 'price_checker_report_' . date('Y-m-d') . '.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}
?>


<?php
// show error
$isFilter = isset($_GET['filter']);
?>
<!DOCTYPE html>
<html lang="id" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="assets/" data-template="vertical-menu-template-free">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Price Checker · Statistik Audit</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            background: #f4f7fc;
            font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
        }

        /* statistik card modern */
        .stat-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 1.25rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            flex: 1;
            min-width: 180px;
            background: white;
            border-radius: 24px;
            padding: 1.25rem 1rem;
            box-shadow: 0 8px 20px rgba(0,0,0,0.02), 0 2px 6px rgba(0,0,0,0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border: 1px solid rgba(0,0,0,0.05);
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -12px rgba(0,0,0,0.1);
        }

        .stat-title {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
            color: #5b6e8c;
            margin-bottom: 0.5rem;
        }

        .stat-number {
            font-size: 2.2rem;
            font-weight: 800;
            color: #1a2c3e;
            line-height: 1.2;
            letter-spacing: -0.5px;
        }

        .stat-unit {
            font-size: 0.9rem;
            font-weight: 500;
            color: #7e8b9e;
            margin-left: 4px;
        }

        /* summary cards dengan border halus */
        .insight-card {
            background: white;
            border-radius: 28px;
            padding: 1.25rem 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03), 0 1px 2px rgba(0,0,0,0.05);
            border: 1px solid #eef2f6;
        }

        .insight-title {
            font-weight: 700;
            font-size: 1.2rem;
            color: #1e2f41;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #eef2f8;
            display: inline-block;
        }

        .badge-stat {
            background: #eef2ff;
            color: #1e40af;
            border-radius: 40px;
            padding: 0.3rem 0.9rem;
            font-weight: 600;
            font-size: 0.8rem;
        }

        .clickable-row {
            cursor: pointer;
            transition: background 0.2s;
        }

        .clickable-row:hover {
            background-color: #f8fafd !important;
        }

        .sku-detail {
            background-color: #fafcff;
            border-left: 4px solid #2c7da0;
        }

        .btn-sm-soft {
            background: #eef2fa;
            border: none;
            border-radius: 40px;
            padding: 0.3rem 1rem;
            font-size: 0.75rem;
            font-weight: 500;
            color: #2c5f8a;
        }

        .btn-sm-soft:hover {
            background: #e0e8f3;
        }

        table.dataTable th {
            font-weight: 600;
            font-size: 0.85rem;
            color: #2c3e50;
        }

        .filter-bar {
            background: #ffffffdd;
            backdrop-filter: blur(2px);
            border-radius: 20px;
            padding: 1rem 1.5rem;
            margin-bottom: 1.8rem;
            border: 1px solid #e9edf2;
        }
        
    </style>
</head>
<?php include "menu/header.php"; ?>
<body>
    <div id="overlay"><div class="cv-spinner"><span class="spinner"></span></div></div>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <?php include "menu/menu.php" ?>
            <div class="layout-page">
                <?php include "menu/navbar.php" ?>
                <div class="content-wrapper">
                    <div class="container-xxl flex-grow-1 container-p-y">
                        <div class="row">
                            <div class="col-lg-12 mb-12">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body p-4">
                                        <!-- FILTER AREA (tetap ada tapi tidak mengganggu) -->
                                        <div class="filter-bar">
                                            <form action="" method="get">
                                                <input type="hidden" name="filter" value="1">
                                                <div class="row g-3 align-items-end">
                                                    <div class="col-md-3">
                                                        <label class="form-label fw-semibold">Start Date</label>
                                                        <input type="date" class="form-control" name="start_date" value="<?php echo $_GET['start_date'] ?? date('Y-m-d', strtotime('-7 days')); ?>">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label fw-semibold">End Date</label>
                                                        <input type="date" class="form-control" name="end_date" value="<?php echo $_GET['end_date'] ?? date('Y-m-d'); ?>">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label fw-semibold">SKU</label>
                                                        <input type="text" class="form-control" name="sku" value="<?php echo $_GET['sku'] ?? ''; ?>" placeholder="Cari SKU">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label fw-semibold">Lokasi</label>
                                                        <select class="form-select" name="location">
                                                            <option value="">Semua Lokasi</option>
                                                            <?php
                                                            $locationQuery = $connec->query("SELECT DISTINCT id_location FROM price_audit ORDER BY id_location");
                                                            foreach ($locationQuery as $loc) {
                                                                $selected = ($_GET['location'] ?? '') == $loc['id_location'] ? 'selected' : '';
                                                                echo "<option value='{$loc['id_location']}' $selected>{$loc['id_location']}</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-12 d-flex gap-2 mt-3">
                                                        <button type="submit" class="btn btn-primary px-4 rounded-pill">Terapkan Filter</button>
                                                        <a href="?" class="btn btn-outline-secondary rounded-pill px-4">Reset</a>
                                                       <a href="export_price_checker.php?<?php echo htmlspecialchars($_SERVER['QUERY_STRING']); ?>" class="btn btn-success px-4 rounded-pill" target="_blank">📎 Export Excel (3 Sheet)</a>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>

                                        <?php
                                        // ambil filter values
                                        $date_start = $_GET['start_date'] ?? date('Y-m-d', strtotime('-7 days'));
                                        $date_end = $_GET['end_date'] ?? date('Y-m-d');
                                        $sku_filter = $_GET['sku'] ?? '';
                                        $location_filter = $_GET['location'] ?? '';

                                        // SUMMARY STATISTIK (murni aktivitas price checker)
                                        $summaryQuery = "SELECT 
                                            COUNT(*) as total_scan,
                                            COUNT(DISTINCT sku) as unique_sku,
                                            COUNT(DISTINCT DATE(insertdate)) as active_days
                                        FROM price_audit 
                                        WHERE DATE(insertdate) BETWEEN ? AND ? 
                                        AND scanfrom = 'price_checker'";
                                        $summaryParams = [$date_start, $date_end];
                                        if (!empty($sku_filter)) { $summaryQuery .= " AND sku LIKE ?"; $summaryParams[] = "%$sku_filter%"; }
                                        if (!empty($location_filter)) { $summaryQuery .= " AND id_location = ?"; $summaryParams[] = $location_filter; }
                                        $stmt = $connec->prepare($summaryQuery);
                                        $stmt->execute($summaryParams);
                                        $summary = $stmt->fetch(PDO::FETCH_ASSOC);
                                        ?>

                                        <!-- KPI UTAMA (tanpa harga / diskon) -->
                                        <div class="stat-grid">
                                            <div class="stat-card">
                                                <div class="stat-title">📊 TOTAL SCAN</div>
                                                <div class="stat-number"><?php echo number_format($summary['total_scan'] ?? 0); ?></div>
                                                <div class="stat-unit">kali pemindaian</div>
                                            </div>
                                            <div class="stat-card">
                                                <div class="stat-title">🏷️ SKU UNIK</div>
                                                <div class="stat-number"><?php echo number_format($summary['unique_sku'] ?? 0); ?></div>
                                                <div class="stat-unit">produk berbeda</div>
                                            </div>
                                            <div class="stat-card">
                                                <div class="stat-title">📆 HARI AKTIF</div>
                                                <div class="stat-number"><?php echo number_format($summary['active_days'] ?? 0); ?></div>
                                                <div class="stat-unit">hari</div>
                                            </div>
                                            <div class="stat-card">
                                                <div class="stat-title">🔄 RATA² SCAN/HARI</div>
                                                <div class="stat-number"><?php 
                                                    $days = max(1, $summary['active_days'] ?? 1);
                                                    echo number_format(($summary['total_scan'] ?? 0) / $days, 1);
                                                ?></div>
                                                <div class="stat-unit">scan/hari</div>
                                            </div>
                                        </div>

                                        <!-- 1. UNIQUE SKU PER KATEGORI (statistik keragaman produk) -->
                                        <div class="insight-card">
                                            <div class="insight-title">📦 Unique SKU per Kategori</div>
                                            <div class="text-muted small mb-3">Jumlah produk unik yang dipindai oleh price checker</div>
                                            <div class="table-responsive">
                                                <table class="table table-hover align-middle" id="categorySummaryTable">
                                                    <thead class="table-dark">
                                                        <tr><th>No</th><th>Kategori</th><th>Unique SKU</th></tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $catQuery = "SELECT ims.cat_name as category, COUNT(DISTINCT pa.sku) as unique_sku
                                                        FROM price_audit pa
                                                        INNER JOIN in_item ims ON pa.sku = ims.sku
                                                        WHERE pa.scanfrom = 'price_checker' AND DATE(pa.insertdate) BETWEEN ? AND ?";
                                                        $catParams = [$date_start, $date_end];
                                                        if (!empty($sku_filter)) { $catQuery .= " AND pa.sku LIKE ?"; $catParams[] = "%$sku_filter%"; }
                                                        if (!empty($location_filter)) { $catQuery .= " AND pa.id_location = ?"; $catParams[] = $location_filter; }
                                                        $catQuery .= " GROUP BY ims.cat_name ORDER BY unique_sku DESC";
                                                        $stmt = $connec->prepare($catQuery);
                                                        $stmt->execute($catParams);
                                                        $catData = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                                        $no=1;
                                                        foreach($catData as $c){
                                                            echo "<tr><td>{$no}</td><td><strong>".htmlspecialchars($c['category'])."</strong></td><td><span class='badge-stat'>".number_format($c['unique_sku'])." SKU</span></td></tr>";
                                                            $no++;
                                                        }
                                                        if(empty($catData)) echo "<tr><td colspan='3' class='text-center'>Tidak ada data kategori</td></tr>";
                                                        ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <!-- 2. TOTAL SCAN PER KATEGORI (frekuensi pengecekan harga) -->
                                        <div class="insight-card">
                                            <div class="insight-title">🔁 Total Scan per Kategori</div>
                                            <div class="text-muted small mb-3">Seberapa sering price checker memindai tiap kategori (termasuk scan berulang)</div>
                                            <div class="table-responsive">
                                                <table class="table table-hover align-middle" id="monitoringCategoryTable">
                                                    <thead class="table-dark">
                                                        <tr><th>No</th><th>Kategori</th><th>Total Scan</th></tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $monQuery = "SELECT ims.cat_name as category, COUNT(*) as total_scan, COUNT(pa.sku) as total_sku_scanned
                                                        FROM price_audit pa
                                                        INNER JOIN in_item ims ON pa.sku = ims.sku
                                                        WHERE pa.scanfrom = 'price_checker' AND DATE(pa.insertdate) BETWEEN ? AND ?";
                                                        $monParams = [$date_start, $date_end];
                                                        if (!empty($sku_filter)) { $monQuery .= " AND pa.sku LIKE ?"; $monParams[] = "%$sku_filter%"; }
                                                        if (!empty($location_filter)) { $monQuery .= " AND pa.id_location = ?"; $monParams[] = $location_filter; }
                                                        $monQuery .= " GROUP BY ims.cat_name ORDER BY total_scan DESC";
                                                        $stmt = $connec->prepare($monQuery);
                                                        $stmt->execute($monParams);
                                                        $monData = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                                        $no=1;
                                                        foreach($monData as $m){
                                                            echo "<tr><td>{$no}</td><td><strong>".htmlspecialchars($m['category'])."</strong></td>
                                                                      <td><span class='badge bg-primary bg-opacity-10 px-3 py-2 rounded-pill'>".number_format($m['total_scan'])." x</span></td></tr>";
                                                            $no++;
                                                        }
                                                        if(empty($monData)) echo "<tr><td colspan='3' class='text-center'>Tidak ada data scan</td></tr>";
                                                        ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <!-- 3. AKTIVITAS PER TANGGAL (trend volume scan) -->
                                        <div class="insight-card">
                                            <div class="insight-title">📅 Aktivitas Price Checker per Tanggal</div>
                                            <div class="table-responsive">
                                                <table class="table table-hover" id="summaryTable">
                                                    <thead class="table-dark">
                                                        <tr><th>No</th><th>Tanggal</th><th>SKU Unik</th><th>Total Scan</th></tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $dateQuery = "SELECT DATE(insertdate) as audit_date, COUNT(DISTINCT sku) as sku_count, COUNT(*) as total_records
                                                        FROM price_audit 
                                                        WHERE DATE(insertdate) BETWEEN ? AND ? AND scanfrom = 'price_checker'";
                                                        $dateParams = [$date_start, $date_end];
                                                        if (!empty($sku_filter)) { $dateQuery .= " AND sku LIKE ?"; $dateParams[] = "%$sku_filter%"; }
                                                        if (!empty($location_filter)) { $dateQuery .= " AND id_location = ?"; $dateParams[] = $location_filter; }
                                                        $dateQuery .= " GROUP BY DATE(insertdate) ORDER BY audit_date DESC";
                                                        $stmt = $connec->prepare($dateQuery);
                                                        $stmt->execute($dateParams);
                                                        $dateData = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                                        
                                                        $no = 1;
                                                        foreach ($dateData as $row) {
                                                            $auditDate = date('d-m-Y', strtotime($row['audit_date']));
                                                            echo "<tr class='clickable-row' data-date='{$row['audit_date']}'>
                                                                    <td>{$no}</td>
                                                                    <td><strong>{$auditDate}</strong></td>
                                                                    <td><span class='badge-stat'>{$row['sku_count']} SKU</span></td>
                                                                    <td>{$row['total_records']} scan</td>
                                                                  </tr>";
                                                            
                                                            // detail SKU (hidden)
                                                            $detailQuery = "SELECT sku, COUNT(*) as audit_count, STRING_AGG(DISTINCT in_master_location.location_name, ', ') as locations_name
                                                            FROM price_audit 
                                                            INNER JOIN in_master_location ON price_audit.id_location = CAST(in_master_location.id_master_location AS VARCHAR)
                                                            WHERE DATE(insertdate) = ?";
                                                            $detailParams = [$row['audit_date']];
                                                            if (!empty($sku_filter)) { $detailQuery .= " AND sku LIKE ?"; $detailParams[] = "%$sku_filter%"; }
                                                            if (!empty($location_filter)) { $detailQuery .= " AND id_location = ?"; $detailParams[] = $location_filter; }
                                                            $detailQuery .= " GROUP BY sku ORDER BY sku";
                                                            $detailStmt = $connec->prepare($detailQuery);
                                                            $detailStmt->execute($detailParams);
                                                            $skuDetails = $detailStmt->fetchAll(PDO::FETCH_ASSOC);
                                                            
                                                            echo "<tr id='detail-{$row['audit_date']}' style='display:none;' class='sku-detail'>
                                                                    <td colspan='4'>
                                                                        <div class='p-3'>
                                                                            <h6 class='mb-2'>📌 SKU yang dipindai pada {$auditDate}</h6>
                                                                            <div class='table-responsive'>
                                                                                <table class='table table-sm table-borderless'>
                                                                                    <thead><tr><th>SKU</th><th>Jumlah Scan</th><th>Lokasi</th><th></th></tr></thead>
                                                                                    <tbody>";
                                                            foreach ($skuDetails as $detail) {
                                                                echo "<tr>
                                                                        <td><code>{$detail['sku']}</code></td>
                                                                        <td>{$detail['audit_count']}x</td>
                                                                        <td>{$detail['locations_name']}</td>
                                                                        <td><button class='btn btn-sm-soft' onclick='viewSkuDetail(\"{$detail['sku']}\", \"{$row['audit_date']}\")'>🔍 Detail</button></td>
                                                                      </tr>";
                                                            }
                                                            echo "</tbody></table></div></div></td></tr>";
                                                            $no++;
                                                        }
                                                        if(empty($dateData)) echo "<tr><td colspan='4' class='text-center'>Tidak ada aktivitas scan untuk periode ini</td></tr>";
                                                        ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php include "menu/footer.php"; ?>
                </div>
            </div>
        </div>
    </div>
    
    <?php include "menu/library.php"; ?>
    <script>
        $(document).ready(function() {
            $('#summaryTable, #categorySummaryTable, #monitoringCategoryTable').DataTable({
                aLengthMenu: [[25, 50, 100, -1], [25, 50, 100, "Semua"]],
                pageLength: 25,
                language: { search: "Cari:", lengthMenu: "Tampilkan _MENU_ data", info: "Menampilkan _START_ - _END_ dari _TOTAL_" }
            });
        });
        
        document.querySelectorAll('.clickable-row').forEach(row => {
            row.addEventListener('click', function(e) {
                if(e.target.tagName === 'BUTTON') return;
                const date = this.getAttribute('data-date');
                const detailRow = document.getElementById('detail-' + date);
                if(detailRow) detailRow.style.display = detailRow.style.display === 'none' ? 'table-row' : 'none';
            });
        });
        
        function viewSkuDetail(sku, date) {
            window.open(`price_audit_detail.php?sku=${sku}&date=${date}`, '_blank');
        }
    </script>
</body>
</html>