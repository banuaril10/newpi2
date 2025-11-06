<?php
// === SERVER 1 ===
// File: send_backup.php
// Fungsi: ambil data dari DB lokal, kirim hasil JSON ke Server 2

include "../../config/koneksi.php"; // koneksi PDO: $connec
date_default_timezone_set("Asia/Jakarta");


$sql = "SELECT value FROM ad_morg";
$result = $connec->query($sql);
$row = $result->fetch(PDO::FETCH_ASSOC);

if ($row) {
    $value = $row['value']; // misal BOS88

    // Ambil angka setelah BOS
    if (preg_match('/BOS(\d+)/', $value, $match)) {
        $num = $match[1]; // hasil: 88
        $ip = "10.0." . $num . ".3";
    } else {
        $ip = "localhost"; // fallback kalau format ga sesuai
    }
}

//bikin static aja


// === Konfigurasi URL server 2 ===
$server2_url = "http://{$ip}/pi/api/cyber/receive_backup.php";

// === Daftar tabel yang mau diekspor ===
$tables = [
    "pos_dcashierbalance",
    "pos_dsales",
    "pos_dsalesline",
    "pos_dshopsales"
];

// === Fungsi ambil data & kirim ke Server 2 ===
function exportAndSend($pdo, $table, $server2_url)
{
    echo "🔄 Mengekspor tabel {$table}...\n";

    $stmt = $pdo->prepare("SELECT * FROM {$table}");
    $stmt->execute();

    $rows = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $rows[] = $row;
    }

    $json = json_encode($rows, JSON_UNESCAPED_UNICODE);

    // === Kirim ke server 2 ===
    $ch = curl_init($server2_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, [
        'table' => $table,
        'data' => $json
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // jika pakai HTTPS self-signed
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo "❌ CURL Error: " . curl_error($ch) . "\n";
    } else {
        echo "✅ Tabel {$table} terkirim. Respon Server 2: {$response}\n";
    }

    curl_close($ch);
}

// === Eksekusi tiap tabel ===
foreach ($tables as $table) {
    exportAndSend($connec, $table, $server2_url);
}

echo "\n🎉 Semua tabel berhasil dikirim ke Server 2.\n";
?>
