<?php
include "config/koneksi.php";
header("Content-type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$pb_number = isset($_POST['pb_number']) ? $_POST['pb_number'] : '';
$id_morg = isset($_POST['id_morg']) ? $_POST['id_morg'] : '';
$items = isset($_POST['items']) ? json_decode($_POST['items'], true) : [];
$tanggal = isset($_POST['tanggal']) ? $_POST['tanggal'] : date('Y-m-d');

if (empty($pb_number) || empty($id_morg) || empty($items)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Missing required parameters'
    ]);
    exit;
}

$connec->beginTransaction();

try {
    foreach ($items as $item) {
        $sku = $item['sku'];
        
        // 1. Ambil stok ERP untuk SKU ini
        $erpUrl = 'https://api.idolmartidolaku.com/apiidolmart/store/items/get_stock_by_sku.php?skus=' . urlencode($sku);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $erpUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $erpResponse = curl_exec($ch);
        curl_close($ch);
        
        $erpData = json_decode($erpResponse, true);
        $stokERP = 0;
        if ($erpData && isset($erpData['data'][0]['stock'])) {
            $stokERP = (int)$erpData['data'][0]['stock'];
        }
        
        // 2. Ambil QTY Sales untuk SKU ini
        $salesSql = "SELECT COALESCE(SUM(qty), 0) as total_sales 
                     FROM pos_dsalesline 
                     WHERE DATE(insertdate) = :tanggal 
                     AND sku = :sku";
        $salesQuery = $connec->prepare($salesSql);
        $salesQuery->bindParam(':tanggal', $tanggal);
        $salesQuery->bindParam(':sku', $sku);
        $salesQuery->execute();
        $salesResult = $salesQuery->fetch(PDO::FETCH_ASSOC);
        $qtySales = (int)$salesResult['total_sales'];
        
        // 3. Hitung stok baru = Stok ERP - QTY Sales
        $newStock = $stokERP - $qtySales;
        if ($newStock < 0) $newStock = 0;
        
        // 4. Update stok di pos_mproduct
        $sql = "UPDATE pos_mproduct 
                SET stockqty = :new_stock,
                    updatedate = NOW(),
                    postdate = NOW(),
                    postby = 'SYNC_PB'
                WHERE sku = :sku 
                AND ad_morg_key = :id_morg
                AND isactived = '1'";
        
        $query = $connec->prepare($sql);
        $query->bindParam(':new_stock', $newStock);
        $query->bindParam(':sku', $sku);
        $query->bindParam(':id_morg', $id_morg);
        $query->execute();
    }
    
    $connec->commit();
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Stock updated successfully for PB: ' . $pb_number
    ]);
    
} catch (Exception $e) {
    $connec->rollBack();
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>