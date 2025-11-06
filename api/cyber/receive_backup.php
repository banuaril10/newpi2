<?php
// === SERVER 2 ===
// File: receive_backup.php
// Fungsi: menerima data JSON dari Server 1 dan menyimpannya ke file lokal

date_default_timezone_set("Asia/Jakarta");
header('Content-Type: application/json');

// === Buat folder backup ===
$date = date("Ymd");
$backupDir = __DIR__ . "/../../../backupdb/{$date}/";

if (!is_dir($backupDir)) {
    mkdir($backupDir, 0777, true);
}

// === Terima payload dari Server 1 ===
$table = $_POST['table'] ?? null;
$jsonData = $_POST['data'] ?? null;

if (!$table || !$jsonData) {
    http_response_code(400);
    echo json_encode(["error" => "Parameter tidak lengkap."]);
    exit;
}

// === Simpan file JSON ===
$filePath = "{$backupDir}{$table}.json";
if (file_put_contents($filePath, $jsonData)) {
    echo json_encode([
        "status" => "success",
        "table" => $table,
        "path" => $filePath
    ]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Gagal menyimpan file untuk tabel {$table}."]);
}
?>