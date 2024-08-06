<?php include "../../config/koneksi.php";
$tanggal = $_GET['date'];

function push_to_cashierbalance($cashierbalance)
{
    $postData = array(
        "cashierbalance" => $cashierbalance
    );
    $fields_string = http_build_query($postData);

    $curl = curl_init();

    curl_setopt_array(
        $curl,
        array(
            CURLOPT_URL => 'https://intransit.idolmartidolaku.com/salesorderidolmart/sync_sales_cashierbalance.php?id=OHdkaHkyODczeWQ3ZDM2NzI4MzJoZDk3MzI4OTc5eDcyOTdyNDkycjc5N3N1MHI',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $fields_string,
        )
    );

    $response = curl_exec($curl);

    curl_close($curl);
    return $response;
}

$jj_cashierbalance = array();

$list_cashierbalance = "select * from pos_dcashierbalance where date(insertdate) = '" . $tanggal . "'";
foreach ($connec->query($list_cashierbalance) as $row4) {
    $jj_cashierbalance[] = array(
        "pos_dcashierbalance_key" => $row4['pos_dcashierbalance_key'],
        "ad_mclient_key" => $row4['ad_mclient_key'],
        "ad_morg_key" => $row4['ad_morg_key'],
        "isactived" => $row4['isactived'],
        "insertdate" => $row4['insertdate'],
        "insertby" => $row4['insertby'],
        "postby" => $row4['postby'],
        "postdate" => $row4['postdate'],
        "pos_mcashier_key" => $row4['pos_mcashier_key'],
        "ad_muser_key" => $row4['ad_muser_key'],
        "pos_mshift_key" => $row4['pos_mshift_key'],
        "startdate" => $row4['startdate'],
        "enddate" => $row4['enddate'],
        "balanceamount" => $row4['balanceamount'],
        "salesamount" => $row4['salesamount'],
        "status" => $row4['status'],
        "salescashamount" => $row4['salescashamount'],
        "salesdebitamount" => $row4['salesdebitamount'],
        "salescreditamount" => $row4['salescreditamount'],
        "actualamount" => $row4['actualamount'],
        "issync" => $row4['issync'],
        "refundamount" => $row4['refundamount'],
        "discountamount" => $row4['discountamount'],
        "cancelcount" => $row4['cancelcount'],
        "cancelamount" => $row4['cancelamount'],
        "donasiamount" => $row4['donasiamount'],
        "pointamount" => $row4['pointamount'],
        "pointdebitamout" => $row4['pointdebitamout'],
        "pointcreditamount" => $row4['pointcreditamount'],
        "status_intransit" => $row4['status_intransit']
    );
}


if (!empty($jj_cashierbalance)) {
    $array_cashierbalance = array("cashierbalance" => $jj_cashierbalance);
    $array_cashierbalance_json = json_encode($array_cashierbalance);
    $hasil_cashierbalance = push_to_cashierbalance($array_cashierbalance_json);
    $j_hasil_cashierbalance = json_decode($hasil_cashierbalance, true);

    print_r($j_hasil_cashierbalance);

    if (!empty($j_hasil_cashierbalance)) {
        foreach ($j_hasil_cashierbalance as $r) {
            $statement1 = $connec->query("update pos_dcashierbalance set status_intransit = '1' 
        where pos_dcashierbalance_key = '" . $r . "'");
        }
    }
}






