<?php
include "config/koneksi.php";
header("Content-type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$id_morg = isset($_POST['id_morg']) ? $_POST['id_morg'] : '';
$skus = isset($_POST['skus']) ? $_POST['skus'] : ''; // SKU dari frontend, pisah koma

if (empty($id_morg)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'id_morg is required'
    ]);
    exit;
}

// Jika ada SKU yang dikirim, filter berdasarkan SKU tersebut
if (!empty($skus)) {
    $sku_array = explode(',', $skus);
    $placeholders = implode(',', array_fill(0, count($sku_array), '?'));
    
    $sql = "SELECT 
                sku,
                COALESCE(stockqty, 0) as stockqty,
				postdate as last_sync
            FROM pos_mproduct 
            WHERE ad_morg_key = ? 
            AND isactived = '1'
            AND sku IN ($placeholders)";
    
    $query = $connec->prepare($sql);
    $params = array_merge([$id_morg], $sku_array);
    $query->execute($params);
}

$stockData = array();
while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    $stockData[$row['sku']] = [
        'stockqty' => (float)$row['stockqty'],
        'last_sync' => $row['last_sync']
    ];
}

echo json_encode([
    'status' => 'success',
    'data' => $stockData
]);
?>