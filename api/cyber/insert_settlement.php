<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

while (ob_get_level()) {
    ob_end_clean();
}

header('Content-Type: application/json');

include "../../config/koneksi.php";

try {

    $pos_dcashierbalance_key = $_POST['pos_dcashierbalance_key'] ?? '';
    $pos_medc_key = $_POST['pos_medc_key'] ?? '';
    $amount = $_POST['amount'] ?? '';

    if (
        empty($pos_dcashierbalance_key) ||
        empty($pos_medc_key) ||
        $amount === ''
    ) {
        echo json_encode([
            'success' => false,
            'message' => 'Data tidak lengkap',
            'debug' => [
                'pos_dcashierbalance_key' => $pos_dcashierbalance_key,
                'pos_medc_key' => $pos_medc_key,
                'amount' => $amount
            ]
        ]);
        exit;
    }

    $pos_settlement_key = uniqid() . '_' . date('YmdHis');

    $sql = "
        INSERT INTO pos_settlement
        (
            pos_settlement_key,
            pos_dcashierbalance_key,
            pos_medc_key,
            amount
        )
        VALUES
        (
            :pos_settlement_key,
            :pos_dcashierbalance_key,
            :pos_medc_key,
            :amount
        )
    ";

    $stmt = $connec->prepare($sql);

    $stmt->execute([
        ':pos_settlement_key' => $pos_settlement_key,
        ':pos_dcashierbalance_key' => $pos_dcashierbalance_key,
        ':pos_medc_key' => $pos_medc_key,
        ':amount' => $amount
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Data settlement berhasil disimpan',
        'data' => [
            'pos_settlement_key' => $pos_settlement_key,
            'pos_dcashierbalance_key' => $pos_dcashierbalance_key,
            'pos_medc_key' => $pos_medc_key,
            'amount' => $amount
        ]
    ]);

} catch (Exception $e) {

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);

}

exit;
?>