<?php 
include "../config/koneksi.php";

$date = date('Y-m-d');
$prev_date = date('Y-m-d', strtotime($date .' -1 day'));


$text = "delete from pos_dsales where date(insertdate) ";
$cp = $connec->query($text);


if($cp){
	$json = array('result'=>'1', 'msg'=>'Berhasil sync');
}else{

	$json = array('result'=>'0', 'msg'=>'Gagal sync');	
}

$json_string = json_encode($json);
echo $json_string;



