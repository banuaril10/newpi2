<?php 
include "../../config/koneksi.php";
ini_set('memory_limit', '512M');
ini_set('max_execution_time', '300');
$connec->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Ambil idstore
$ll = "select * from ad_morg where isactived = 'Y'";
$query = $connec->query($ll);
$idstore = null;
while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    $idstore = $row['ad_morg_key'];
}

function get($url)
{
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}

$url = $base_url . '/store/items/get_items.php?idstore=' . $idstore;
$hasil = get($url);
$j_hasil = json_decode($hasil, true);

$total_insert = 0;
$total_update = 0;

try {
    foreach ($j_hasil as $key => $value) {
        $id = $value['id'];
        $sku = $value['sku'];
        $barcode = $value['barcode'];
        $shortcut = $value['shortcut'];
        $name = str_replace("'", "''", $value['name']);
        $idcat = !empty($value['idcat']) ? $value['idcat'] : 0;
        $idsubcat = !empty($value['idsubcat']) ? $value['idsubcat'] : 0;
        $idsubitem = !empty($value['idsubitem']) ? $value['idsubitem'] : 0;
        $tag = $value['tag'];
        $isactived = $value['isactived'];

        // CEK APAKAH DATA SUDAH ADA
        $check = "SELECT COUNT(*) as jum FROM pos_mproduct WHERE m_product_id = '$id'";
        $stmt_check = $connec->query($check);
        $row_check = $stmt_check->fetch(PDO::FETCH_ASSOC);
        
        if ($row_check['jum'] > 0) {
            // UPDATE
            $sql = "UPDATE pos_mproduct SET 
                ad_mclient_key = '$ad_mclient_key',
                ad_morg_key = '$idstore',
                postby = 'SYSTEM',
                postdate = '" . date("Y-m-d H:i:s") . "',
                m_product_category_id = '$idcat',
                sku = '$sku',
                name = '$name',
                shortcut = '$shortcut',
                barcode = '$barcode',
                tag = '$tag',
                idcat = '$idcat',
                idsubcat = '$idsubcat',
                idsubitem = '$idsubitem'
                WHERE m_product_id = '$id'";
            
            $connec->query($sql);
            $total_update++;
        } else {
            // INSERT
            $sql = "INSERT INTO pos_mproduct (
                ad_mclient_key, ad_morg_key, isactived, insertdate, insertby, postby, postdate,
                m_product_id, m_product_category_id, sku, name, description, price, stockqty,
                shortcut, barcode, tag, idcat, idsubcat, idsubitem
            ) VALUES (
                '$ad_mclient_key',
                '$idstore',
                '$isactived',
                '" . date("Y-m-d H:i:s") . "',
                'SYSTEM',
                'SYSTEM',
                '" . date("Y-m-d H:i:s") . "',
                '$id',
                '$idcat',
                '$sku',
                '$name',
                '',
                0,
                0,
                '$shortcut',
                '$barcode',
                '$tag',
                '$idcat',
                '$idsubcat',
                '$idsubitem'
            )";
            
            $connec->query($sql);
            $total_insert++;
        }
        
        // LANGSUNG COMMIT SATU PER SATU
        // Kalo pake PDO, setiap query langsung commit otomatis kok
        // Kecuali pake beginTransaction, nah ini ga pake
    }

    $json = array(
        "status" => "OK",
        "message" => "Data Inserted/Updated",
        "inserted" => $total_insert,
        "updated" => $total_update
    );
    
    echo json_encode($json);
    
} catch (PDOException $e) {
    echo json_encode([
        "status" => "FAILED",
        "message" => $e->getMessage()
    ]);
}
?>