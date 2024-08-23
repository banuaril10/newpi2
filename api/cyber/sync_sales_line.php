<?php include "../../config/koneksi.php";
$tanggal = $_GET['date'];


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


$jj_line = array();



$list_line = "select * from pos_dsalesline where date(insertdate) = '" . $tanggal . "' 
and isactived = '1' and status_intransit is null ";
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




if (!empty($jj_line)) {
    $array_line = array("line" => $jj_line);
    $array_line_json = json_encode($array_line);
    $hasil_line = push_to_line($array_line_json);
    $j_hasil_line = json_decode($hasil_line, true);

    print_r($j_hasil_line);

    if (!empty($j_hasil_line)) {
        foreach ($j_hasil_line as $r) {
            $statement2 = $connec->query("update pos_dsalesline set status_intransit = '1' 
            where pos_dsalesline_key = '" . $r . "'");
        }
    }
}



