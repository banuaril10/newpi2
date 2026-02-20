<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type");

include "../../config/koneksi.php";

// Ambil parameter GET
$f3 = $_GET['f3'] ?? ''; // sp name = pos_supervisor_user_get
$f4 = $_GET['f4'] ?? ''; // user id

if (empty($f3)) {
    echo json_encode(["data" => []]);
    exit;
}

try {
    // Panggil function sesuai dengan sp yang diminta
    if ($f3 == 'pos_supervisor_user_get') {
        $sql = "SELECT * FROM proc_pos_supervisor_user_get(
            null, null, null, null, :p_search
        )";
        
        $stmt = $connec->prepare($sql);
        $stmt->bindParam(":p_search", $f4);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $o_data = $result['o_data'] ?? null;
        
        if ($o_data) {
            $data = json_decode($o_data, true);
            echo json_encode(["data" => $data]);
        } else {
            echo json_encode(["data" => []]);
        }
    } else {
        echo json_encode(["data" => []]);
    }
    
} catch(PDOException $e) {
    echo json_encode([
        "data" => [],
        "error" => $e->getMessage()
    ]);
}
?>