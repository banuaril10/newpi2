<?php include "../../config/koneksi.php";
$tanggal = $_GET['date'];
function push_to_header($header)
{
    $postData = array(
        "header" => $header
    );
    $fields_string = http_build_query($postData);

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://intransit.idolmartidolaku.com/salesorderidolmart/sync_sales_header.php?id=OHdkaHkyODczeWQ3ZDM2NzI4MzJoZDk3MzI4OTc5eDcyOTdyNDkycjc5N3N1MHI',
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

function push_to_line($line)
{
    $postData = array(
        "line" => $line
    );
    $fields_string = http_build_query($postData);

    $curl = curl_init();

    curl_setopt_array(
        $curl,
        array(
            CURLOPT_URL => 'https://intransit.idolmartidolaku.com/salesorderidolmart/sync_sales_line.php?id=OHdkaHkyODczeWQ3ZDM2NzI4MzJoZDk3MzI4OTc5eDcyOTdyNDkycjc5N3N1MHI',
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


function push_to_deleted($deleted)
{
    $postData = array(
        "deleted" => $deleted
    );
    $fields_string = http_build_query($postData);

    $curl = curl_init();

    curl_setopt_array(
        $curl,
        array(
            CURLOPT_URL => 'https://intransit.idolmartidolaku.com/salesorderidolmart/sync_sales_deleted.php?id=OHdkaHkyODczeWQ3ZDM2NzI4MzJoZDk3MzI4OTc5eDcyOTdyNDkycjc5N3N1MHI',
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

$jj_header = array();
$jj_line = array();
$jj_deleted = array();
$jj_cashierbalance = array();
$jj_posdshopsales = array();

$list_header = "select * from pos_dsales where status_intransit is null and date(insertdate) = '" . $tanggal . "' 
and isactived = '1' and status_intransit is null";
foreach ($connec->query($list_header) as $row1) {
    $jj_header[] = array(
        "pos_dsales_key" => $row1['pos_dsales_key'],
        "ad_mclient_key" => $row1['ad_mclient_key'],
        "ad_morg_key" => $row1['ad_morg_key'],
        "isactived" => $row1['isactived'],
        "insertdate" => $row1['insertdate'],
        "insertby" => $row1['insertby'],
        "postby" => $row1['postby'],
        "postdate" => $row1['postdate'],
        "pos_medc_key" => $row1['pos_medc_key'],
        "pos_dcashierbalance_key" => $row1['pos_dcashierbalance_key'],
        "pos_mbank_key" => $row1['pos_mbank_key'],
        "ad_muser_key" => $row1['ad_muser_key'],
        "billno" => $row1['billno'],
        "billamount" => $row1['billamount'],
        "paymentmethodname" => $row1['paymentmethodname'],
        "membercard" => $row1['membercard'],
        "cardno" => $row1['cardno'],
        "approvecode" => $row1['approvecode'],
        "edcno" => $row1['edcno'],
        "bankname" => $row1['bankname'],
        "serialno" => $row1['serialno'],
        "billstatus" => $row1['billstatus'],
        "paycashgiven" => $row1['paycashgiven'],
        "paygiven" => $row1['paygiven'],
        "printcount" => $row1['printcount'],
        "issync" => $row1['issync'],
        "donasiamount" => $row1['donasiamount'],
        "dpp" => $row1['dpp'],
        "ppn" => $row1['ppn'],
        "billcode" => $row1['billcode'],
        "ispromomurah" => $row1['ispromomurah'],
        "point" => $row1['point'],
        "pointgive" => $row1['pointgive'],
        "membername" => $row1['membername'],
        "status_intransit" => $row1['status_intransit']
    );
}

$list_line = "select * from pos_dsalesline where status_intransit is null and date(insertdate) = '" . $tanggal . "' and isactived = '1'
and status_intransit is null";
foreach ($connec->query($list_line) as $row2) {
    $jj_line[] = array(
        "pos_dsalesline_key" => $row2['pos_dsalesline_key'],
        "ad_mclient_key" => $row2['ad_mclient_key'],
        "ad_morg_key" => $row2['ad_morg_key'],
        "isactived" => $row2['isactived'],
        "insertdate" => $row2['insertdate'],
        "insertby" => $row2['insertby'],
        "postby" => $row2['postby'],
        "postdate" => $row2['postdate'],
        "pos_dsales_key" => $row2['pos_dsales_key'],
        "billno" => $row2['billno'],
        "seqno" => $row2['seqno'],
        "sku" => $row2['sku'],
        "qty" => $row2['qty'],
        "price" => $row2['price'],
        "discount" => $row2['discount'],
        "amount" => $row2['amount'],
        "issync" => $row2['issync'],
        "discountname" => $row2['discountname'],
        "status_sales" => $row2['status_sales'],
        "status_intransit" => $row2['status_intransit']
    );
}


$list_deleted = "select * from pos_dsalesdeleted where status_intransit is null and date(insertdate) = '" . $tanggal . "'
and status_intransit is null";
foreach ($connec->query($list_deleted) as $row3) {
    $jj_deleted[] = array(
        "pos_dsalesdeleted_key" => $row3['pos_dsalesdeleted_key'],
        "ad_mclient_key" => $row3['ad_mclient_key'],
        "ad_morg_key" => $row3['ad_morg_key'],
        "isactived" => $row3['isactived'],
        "insertdate" => $row3['insertdate'],
        "insertby" => $row3['insertby'],
        "postby" => $row3['postby'],
        "postdate" => $row3['postdate'],
        "ad_muser_key" => $row3['ad_muser_key'],
        "pos_dcashierbalance_key" => $row3['pos_dcashierbalance_key'],
        "sku" => $row3['sku'],
        "qty" => $row3['qty'],
        "price" => $row3['price'],
        "discount" => $row3['discount'],
        "billno" => $row3['billno'],
        "approvedby" => $row3['approvedby'],
        "issync" => $row3['issync'],
        "status_intransit" => $row3['status_intransit']
    );
}

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

$list_posdshopsales = "select * from pos_dshopsales where status_intransit is null and date(insertdate) = '" . $tanggal . "'
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


if (!empty($jj_header)) {
    $array_header = array("header" => $jj_header);
    $array_header_json = json_encode($array_header);
    $hasil_header = push_to_header($array_header_json);
    $j_hasil_header = json_decode($hasil_header, true);

    if (!empty($j_hasil_header)) {
        foreach ($j_hasil_header as $r) {
            $statement1 = $connec->query("update pos_dsales set status_intransit = '1' 
        where pos_dsales_key = '" . $r . "'");
        }
    }
}

if (!empty($jj_line)) {
    $array_line = array("line" => $jj_line);
    $array_line_json = json_encode($array_line);
    $hasil_line = push_to_line($array_line_json);
    $j_hasil_line = json_decode($hasil_line, true);

    if (!empty($j_hasil_line)) {
        foreach ($j_hasil_line as $r) {
            $statement2 = $connec->query("update pos_dsalesline set status_intransit = '1' 
        where pos_dsalesline_key = '" . $r . "'");
        }
    }
}

if (!empty($jj_deleted)) {
    $array_deleted = array("deleted" => $jj_deleted);
    $array_deleted_json = json_encode($array_deleted);
    $hasil_deleted = push_to_deleted($array_deleted_json);
    $j_hasil_deleted = json_decode($hasil_deleted, true);
    if (!empty($j_hasil_deleted)) {
        foreach ($j_hasil_deleted as $r) {
            $statement1 = $connec->query("update pos_dsalesdeleted set status_intransit = '1' 
        where pos_dsalesdeleted_key = '" . $r . "'");
        }
    }
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






