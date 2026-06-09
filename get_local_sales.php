<?php
include "config/koneksi.php";
header("Content-type: application/json");
header("Access-Control-Allow-Origin: *");

$id_morg = isset($_POST['id_morg']) ? $_POST['id_morg'] : '';
$skus = isset($_POST['skus']) ? $_POST['skus'] : '';
$tanggal = isset($_POST['tanggal']) ? $_POST['tanggal'] : date('Y-m-d');

if (empty($id_morg)) {
    echo json_encode(['status' => 'error', 'message' => 'id_morg required']);
    exit;
}

$sku_array = [];
if (!empty($skus)) {
    $sku_array = explode(',', $skus);
}

if (empty($sku_array)) {
    echo json_encode(['status' => 'success', 'data' => []]);
    exit;
}

$placeholders = implode(',', array_fill(0, count($sku_array), '?'));

$sql = "SELECT 
            sku, 
            COALESCE(SUM(qty), 0) as qty
        FROM pos_dsalesline 
        WHERE DATE(insertdate) = ?
        AND sku IN ($placeholders)
        GROUP BY sku";

$query = $connec->prepare($sql);
$params = array_merge([$tanggal], $sku_array);
$query->execute($params);

$salesData = array();
while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    $salesData[$row['sku']] = (int)$row['qty'];
}

// Default 0 untuk SKU yang tidak ada
foreach ($sku_array as $sku) {
    if (!isset($salesData[$sku])) {
        $salesData[$sku] = 0;
    }
}

echo json_encode([
    'status' => 'success',
    'data' => $salesData
]);
?>