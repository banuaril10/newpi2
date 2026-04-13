<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

include "../../config/koneksi.php";

// Ambil idstore
$ll = "select * from ad_morg where isactived = 'Y' LIMIT 1";
$query = $connec->query($ll);
$idstore = null;
if ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    $idstore = $row['ad_morg_key'];
}

if (!$idstore) {
    echo json_encode([
        'valid' => false,
        'message' => 'Store tidak ditemukan',
        'amount' => 0
    ]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

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

/* =========================
   1️⃣ CEK KE SERVER (FINAL)
   ========================= */
$serverUrl = $base_url . "/store/voucher/check_status_withnohp.php?id=OHdkaHkyODczeWQ3ZDM2NzI4MzJoZDk3";

$payload = json_encode([
    "vouchercode" => $voucherCode,
    "idstore" => $idstore
]);

$ch = curl_init($serverUrl);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
    CURLOPT_POSTFIELDS     => $payload,
    CURLOPT_TIMEOUT        => 5
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200 || !$response) {
    echo json_encode([
        'valid' => false,
        'message' => 'Gagal koneksi ke server pusat',
        'amount' => 0
    ]);
    exit;
}

$server = json_decode($response, true);

if (!$server || ($server['valid'] ?? false) !== true) {
    echo json_encode([
        'valid' => false,
        'message' => $server['message'] ?? 'Gagal cek voucher ke server',
        'amount' => 0
    ]);
    exit;
}

/* =========================
   2️⃣ AMBIL DATA LOKAL
   ========================= */
$voucher = null;
$foundBy = 'voucher_code';

// STEP 1: Cari berdasarkan voucher code dulu
$sql = "SELECT * FROM pos_dvoucher WHERE voucher_code = ? LIMIT 1";
$stmt = $connec->prepare($sql);
$stmt->execute([$voucherCode]);
$voucher = $stmt->fetch(PDO::FETCH_ASSOC);

// STEP 2: Jika tidak ketemu, cek apakah input adalah nomor HP
if (!$voucher) {
    // Bersihkan input dari spasi dan strip
    $cleaned = preg_replace('/[\s\-]/', '', $voucherCode);
    $datenow = date('Y-m-d');
    

        // Cari berdasarkan nohp (ambil voucher NEW dengan expired paling dekat)
        $sqlPhone = "SELECT * FROM pos_dvoucher 
                     WHERE nohp = ? 
                     AND status = 'NEW' 
                     AND useddate IS NULL 
                     AND valid_from <= date(now())
                     AND valid_until >= date(now())
                     ORDER BY valid_until ASC 
                     LIMIT 1";
        $stmtPhone = $connec->prepare($sqlPhone);
        $stmtPhone->execute([$cleaned]); // Pakai $cleaned yang sudah dibersihkan
        $voucher = $stmtPhone->fetch(PDO::FETCH_ASSOC);
        $foundBy = 'phone_number';

}

// Jika tidak ditemukan di lokal, berarti belum sinkron
if (!$voucher) {
    echo json_encode([
        'valid' => false,
        'message' => 'Voucher valid di server tapi belum tersinkron di lokal. Silakan sinkronkan data terlebih dahulu.',
        'amount' => 0,
        'need_sync' => true
    ]);
    exit;
}

/* =========================
   3️⃣ VALIDASI LOKAL
   ========================= */
$today = date('Y-m-d');

// Cek status
if ($voucher['status'] !== 'NEW' || $voucher['useddate'] !== null) {
    echo json_encode([
        'valid' => false,
        'message' => 'Voucher sudah digunakan',
        'amount' => 0
    ]);
    exit;
}

// Cek masa berlaku
if ($today < $voucher['valid_from']) {
    echo json_encode([
        'valid' => false,
        'message' => 'Voucher belum berlaku',
        'amount' => 0
    ]);
    exit;
}

if ($today > $voucher['valid_until']) {
    echo json_encode([
        'valid' => false,
        'message' => 'Voucher sudah kadaluarsa',
        'amount' => 0
    ]);
    exit;
}

// Cek location jika ada claim_location
if (!empty($voucher['claim_location']) && $voucher['claim_location'] != $idstore) {
    echo json_encode([
        'valid' => false,
        'message' => 'Voucher tidak bisa digunakan di lokasi ini',
        'amount' => 0
    ]);
    exit;
}

/* =========================
   4️⃣ HITUNG DISKON
   ========================= */
$voucherAmount = 0;

if (!empty($voucher['percent']) && $voucher['percent'] > 0) {
    $calc = $totalAmount * ($voucher['percent'] / 100);
    $voucherAmount = ($voucher['voucher_amount'] > 0 && $calc > $voucher['voucher_amount'])
        ? $voucher['voucher_amount']
        : $calc;
} else {
    $voucherAmount = $voucher['voucher_amount'] ?? 0;
}

$voucherAmount = min($voucherAmount, $totalAmount);
$voucherAmount = round($voucherAmount, 2);

/* =========================
   5️⃣ RESPONSE OK
   ========================= */
$responseData = [
    'valid' => true,
    'message' => 'Voucher valid',
    'amount' => $voucherAmount,
    'voucher_data' => [
        'voucher_key' => $voucher['pos_dvoucher_key'],
        'voucher_code' => $voucher['voucher_code'],
        'percent' => $voucher['percent'],
        'valid_until' => $voucher['valid_until']
    ]
];

// Tambahkan info jika ditemukan via nohp
if ($foundBy == 'phone_number') {
    $responseData['found_by'] = 'phone_number';
    $responseData['message'] = 'Voucher ditemukan via nomor HP';
}

echo json_encode($responseData);