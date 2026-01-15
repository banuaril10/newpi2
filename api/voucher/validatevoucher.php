<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

include "../../config/koneksi.php";

// Tangkap request
$input = json_decode(file_get_contents('php://input'), true);

// Validasi input - hanya vouchercode dan totalamount yang wajib
if (!isset($input['vouchercode']) || !isset($input['totalamount'])) {
    echo json_encode([
        'valid' => false,
        'message' => 'Data tidak lengkap',
        'amount' => 0
    ]);
    exit;
}

$voucherCode = trim($input['vouchercode']);
$totalAmount = floatval($input['totalamount']);
$currentDate = date('Y-m-d');

try {
    // 1. Cek voucher di database
    $sql = "SELECT * FROM pos_dvoucher 
            WHERE voucher_code = ? 
            AND status = 'NEW'
            AND valid_from <= ?
            AND valid_until >= ?
            LIMIT 1";
    
    $stmt = $connec->prepare($sql);
    $stmt->execute([$voucherCode, $currentDate, $currentDate]);
    $voucher = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$voucher) {
        echo json_encode([
            'valid' => false,
            'message' => 'Voucher blm berlaku, kadaluarsa, atau sudah digunakan',
            'amount' => 0
        ]);
        exit;
    }
    
    // 2. Cek apakah voucher sudah dipakai (cek kolom useddate)
    if ($voucher['useddate'] !== null) {
        echo json_encode([
            'valid' => false,
            'message' => 'Voucher sudah digunakan',
            'amount' => 0
        ]);
        exit;
    }
    
    // 3. Validasi minimal pembelian (opsional)
    $minPurchase = 0; // Disable minimal pembelian
    
    if ($minPurchase > 0 && $totalAmount < $minPurchase) {
        echo json_encode([
            'valid' => false,
            'message' => 'Minimal pembelian Rp ' . number_format($minPurchase, 0, ',', '.') . ' untuk menggunakan voucher',
            'amount' => 0
        ]);
        exit;
    }
    
    // 4. Hitung nilai voucher berdasarkan tipe (amount atau percent)
    $voucherAmount = 0;
    
    if (isset($voucher['percent']) && $voucher['percent'] > 0) {
        // Jika ada field percent dan nilainya > 0, gunakan persentase
        $percent = floatval($voucher['percent']);
        
        // Hitung potongan berdasarkan persentase
        $calculatedAmount = $totalAmount * ($percent / 100);
        
        // Jika ada voucher_amount (maksimum), gunakan yang lebih kecil
        if (isset($voucher['voucher_amount']) && $voucher['voucher_amount'] > 0) {
            $maxAmount = floatval($voucher['voucher_amount']);
            $voucherAmount = min($calculatedAmount, $maxAmount);
        } else {
            // Jika tidak ada batas maksimum, gunakan hasil perhitungan persentase
            $voucherAmount = $calculatedAmount;
        }
        
        // Pastikan tidak melebihi total amount
        $voucherAmount = min($voucherAmount, $totalAmount);
        
    } else {
        // Jika tidak ada percent, gunakan voucher_amount biasa
        $voucherAmount = floatval($voucher['voucher_amount']);
        
        // Aturan: Voucher bisa digunakan untuk total belanja berapapun
        // Nilai akhir = min(nilai voucher, total belanja)
        $voucherAmount = min($voucherAmount, $totalAmount);
    }
    
    // 5. Response sukses
    echo json_encode([
        'valid' => true,
        'message' => 'Voucher valid',
        'amount' => $voucherAmount,
        'voucher_data' => [
            'voucher_key' => $voucher['pos_dvoucher_key'],
            'original_amount' => $voucher['voucher_amount'] ?? 0,
            'percent' => $voucher['percent'] ?? 0,
            'valid_until' => $voucher['valid_until']
        ]
    ]);
    
} catch (PDOException $e) {
    echo json_encode([
        'valid' => false,
        'message' => 'Error database: ' . $e->getMessage(),
        'amount' => 0
    ]);
}
?>