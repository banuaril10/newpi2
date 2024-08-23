<?php include "../../config/koneksi.php";
$tanggal = $_GET['date'];

function push_to_posdshopsales($posdshopsales)
{
    $postData = array(
        "posdshopsales" => $posdshopsales
    );
    $fields_string = http_build_query($postData);

    $curl = curl_init();

    curl_setopt_array(
        $curl,
        array(
            CURLOPT_URL => 'https://intransit.idolmartidolaku.com/salesorderidolmart/sync_sales_posdshopsales.php?id=OHdkaHkyODczeWQ3ZDM2NzI4MzJoZDk3MzI4OTc5eDcyOTdyNDkycjc5N3N1MHI',
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

$jj_posdshopsales = array();



$list_posdshopsales = "select * from pos_dshopsales where date(insertdate) = '" . $tanggal . "'
and status_intransit is null";

foreach ($connec->query($list_posdshopsales) as $row5) {
    $jj_posdshopsales[] = array(
        "pos_dshopsales_key" => $row5['pos_dshopsales_key'],
        "ad_mclient_key" => $row5['ad_mclient_key'],
        "ad_morg_key" => $row5['ad_morg_key'],
        "isactived" => $row5['isactived'],
        "insertdate" => $row5['insertdate'],
        "insertby" => $row5['insertby'],
        "postby" => $row5['postby'],
        "postdate" => $row5['postdate'],
        "pos_mshift_key" => $row5['pos_mshift_key'],
        "ad_muser_key" => $row5['ad_muser_key'],
        "salesdate" => $row5['salesdate'],
        "closedate" => $row5['closedate'],
        "balanceamount" => $row5['balanceamount'],
        "salesamount" => $row5['salesamount'],
        "salescashamount" => $row5['salescashamount'],
        "salesdebitamount" => $row5['salesdebitamount'],
        "salescreditamount" => $row5['salescreditamount'],
        "status" => $row5['status'],
        "actualamount" => $row5['actualamount'],
        "remark" => $row5['remark'],
        "issync" => $row5['issync'],
        "refundamount" => $row5['refundamount'],
        "discountamount" => $row5['discountamount'],
        "cancelcount" => $row5['cancelcount'],
        "cancelamount" => $row5['cancelamount'],
        "donasiamount" => $row5['donasiamount'],
        "variantmin" => $row5['variantmin'],
        "variantplus" => $row5['variantplus'],
        "pointamount" => $row5['pointamount'],
        "pointdebitamout" => $row5['pointdebitamout'],
        "pointcreditamount" => $row5['pointcreditamount'],
        "status_intransit" => $row5['status_intransit']
    );
}

if (!empty($jj_posdshopsales)) {
    $array_posdshopsales = array("posdshopsales" => $jj_posdshopsales);
    $array_posdshopsales_json = json_encode($array_posdshopsales);
    $hasil_posdshopsales = push_to_posdshopsales($array_posdshopsales_json);
    $j_hasil_posdshopsales = json_decode($hasil_posdshopsales, true);
    if (!empty($j_hasil_posdshopsales)) {
        foreach ($j_hasil_posdshopsales as $r) {
            $statement1 = $connec->query("update pos_dshopsales set status_intransit = '1' 
        where pos_dshopsales_key = '" . $r . "'");
        }
    }
}






