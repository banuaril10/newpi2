<?php include "../../config/koneksi.php";

header('Content-Type: application/json');

// Mendapatkan SKU dari parameter GET
if (isset($_GET['sku'])) {
    $sku = $_GET['sku'];

    // Query untuk mendapatkan harga reguler dari tabel pos_mproduct
    $sqlPrice = "SELECT price FROM pos_mproduct WHERE sku = :sku AND isactived = '1'";
    $stmtPrice = $connec->prepare($sqlPrice);
    $stmtPrice->bindParam(':sku', $sku);
    $stmtPrice->execute();
    $product = $stmtPrice->fetch(PDO::FETCH_ASSOC);

    // Cek apakah produk ditemukan
    if ($product) {
        $regularPrice = $product['price'];

        // Query untuk mendapatkan diskon dari tabel pos_mproductdiscount
        $sqlDiscount = "SELECT discount, fromdate, todate FROM pos_mproductdiscount 
                        WHERE sku = :sku AND isactived = '1' 
                        AND CURRENT_DATE BETWEEN fromdate AND todate";
        $stmtDiscount = $connec->prepare($sqlDiscount);
        $stmtDiscount->bindParam(':sku', $sku);
        $stmtDiscount->execute();
        $discount = $stmtDiscount->fetch(PDO::FETCH_ASSOC);

        // Cek apakah diskon ditemukan
        if ($discount) {
            // Menghitung harga diskon
            $discountedPrice = $regularPrice - ($regularPrice * ($discount['discount'] / 100));
            $response = [
                'sku' => $sku,
                'regular_price' => $regularPrice,
                'discount' => $discount['discount'],
                'discounted_price' => $discountedPrice,
                'valid_from' => $discount['fromdate'],
                'valid_to' => $discount['todate']
            ];
        } else {
            // Tidak ada diskon, hanya mengembalikan harga reguler
            $response = [
                'sku' => $sku,
                'regular_price' => $regularPrice,
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
    echo json_encode(['error' => 'SKU parameter is required']);
}
?>
