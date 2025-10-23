<?php
header('Content-Type: application/json');
include "../../config/koneksi.php"; // pastikan sudah ada koneksi $connec (PDO PostgreSQL)

// Ambil data dari POST
$p_ad_mclient_key = "D089DFFA729F4A22816BD8838AB0813C"; // bisa diambil dari session kalau mau
$p_ad_morg_key = ""; // kalau perlu bisa juga diisi otomatis dari org aktif
$p_serialno = $_POST['billseqno'] ?? '';
$p_pos_mcashier_key = $_POST['cashierid'] ?? '';
$p_billno = $_POST['lastbillno'] ?? '';
$p_billamount = $_POST['totalpay'] ?? '0';
$p_voucheramount = $_POST['voucheramount'] ?? '0';
$p_paygiven = $_POST['paycashgiven'] ?? '0';
$p_vouchercode = $_POST['vouchercode'] ?? '';
$p_approvecode = $_POST['approvecode'] ?? '';
$p_pos_medc_key = $_POST['edcname'] ?? '';
$p_pos_mbank_key = $_POST['bankname'] ?? '';
$p_donasiamount = $_POST['donasiamount'] ?? '0';
$p_membercard = $_POST['memberid'] ?? '';
$p_point = $_POST['memberpoint'] ?? '0';
$p_postby = 'SYSTEM';

try {
    $sql = "SELECT * FROM public.proc_pos_dsales_voucher_insert(
        :p_ad_mclient_key,
        :p_ad_morg_key,
        :p_serialno,
        :p_pos_mcashier_key,
        :p_billno,
        :p_billamount,
        :p_voucheramount,
        :p_paygiven,
        :p_vouchercode,
        :p_approvecode,
        :p_pos_medc_key,
        :p_pos_mbank_key,
        :p_donasiamount,
        :p_membercard,
        :p_point,
        :p_postby
    )";

    $stmt = $connec->prepare($sql);
    $stmt->bindParam(':p_ad_mclient_key', $p_ad_mclient_key);
    $stmt->bindParam(':p_ad_morg_key', $p_ad_morg_key);
    $stmt->bindParam(':p_serialno', $p_serialno);
    $stmt->bindParam(':p_pos_mcashier_key', $p_pos_mcashier_key);
    $stmt->bindParam(':p_billno', $p_billno);
    $stmt->bindParam(':p_billamount', $p_billamount);
    $stmt->bindParam(':p_voucheramount', $p_voucheramount);
    $stmt->bindParam(':p_paygiven', $p_paygiven);
    $stmt->bindParam(':p_vouchercode', $p_vouchercode);
    $stmt->bindParam(':p_approvecode', $p_approvecode);
    $stmt->bindParam(':p_pos_medc_key', $p_pos_medc_key);
    $stmt->bindParam(':p_pos_mbank_key', $p_pos_mbank_key);
    $stmt->bindParam(':p_donasiamount', $p_donasiamount);
    $stmt->bindParam(':p_membercard', $p_membercard);
    $stmt->bindParam(':p_point', $p_point);
    $stmt->bindParam(':p_postby', $p_postby);

    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result && $result['o_message'] == 'success') {
        echo json_encode([
            "success" => true,
            "billno" => $result['o_data'],
            "result" => "OK"
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "result" => $result['o_message'] ?? 'unknown error'
        ]);
    }

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "result" => $e->getMessage()
    ]);
}
?>