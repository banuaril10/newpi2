<?php
include "../../config/koneksi.php";
header('Content-Type: application/json');

// Fungsi untuk format Rupiah
function formatRupiah($angka)
{
    return "Rp" . number_format($angka, 2, ',', '.');
}

if (isset($_GET['sku']) || isset($_GET['barcode'])) {
    $sku_or_barcode = isset($_GET['sku']) ? $_GET['sku'] : $_GET['barcode'];

    $sqlPrice = "SELECT price, sku, name FROM pos_mproduct WHERE (sku = :sku_or_barcode OR barcode = :sku_or_barcode) AND isactived = '1'";
    $stmtPrice = $connec->prepare($sqlPrice);
    $stmtPrice->bindParam(':sku_or_barcode', $sku_or_barcode);
    $stmtPrice->execute();
    $product = $stmtPrice->fetch(PDO::FETCH_ASSOC);

    if ($product) {
        $name = $product['name'];
        $regularPrice = $product['price'];
        $sku = $product['sku'];

        $sqlDiscount = "SELECT discount, fromdate, todate FROM pos_mproductdiscount 
                        WHERE sku = :sku AND isactived = '1' 
                        AND CURRENT_DATE BETWEEN fromdate AND todate";
        $stmtDiscount = $connec->prepare($sqlDiscount);
        $stmtDiscount->bindParam(':sku', $sku);
        $stmtDiscount->execute();
        $discount = $stmtDiscount->fetch(PDO::FETCH_ASSOC);

        if ($discount) {
            $discountedPrice = $regularPrice - $discount['discount'];
            $response = [
                'sku' => $sku,
                'name' => $name,
                'regular_price' => formatRupiah($regularPrice),
                'discount' => formatRupiah($discount['discount']),
                'discounted_price' => formatRupiah($discountedPrice),
                'valid_from' => $discount['fromdate'],
                'valid_to' => $discount['todate']
            ];
        } else {
            $response = [
                'sku' => $sku,
                'name' => $name,
                'regular_price' => formatRupiah($regularPrice),
                'discount' => null,
                'discounted_price' => null,
                'valid_from' => null,
                'valid_to' => null
            ];
        }

        echo json_encode($response);
    } else {
        echo json_encode(['error' => 'Product not found']);
    }
} else {
    echo json_encode(['error' => 'SKU or Barcode parameter is required']);
}
?>