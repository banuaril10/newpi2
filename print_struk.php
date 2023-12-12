<?php

$html = $_POST['html'];
// $html = "PRINT PRINT
// PRINT
// PRINTPRINT
// PRINT
// PRINT
// PRINT";

require __DIR__ . '/vendor/escpos-php/vendor/autoload.php';
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;

try {
	
	$connector = new FilePrintConnector("//localhost/pos");

    $printer = new Printer($connector);
	$printer -> initialize();


	$printer -> setFont(Printer::FONT_B);
	$printer -> setTextSize(1, 1);
	$printer -> text($html);
	

    // $printer -> text("test lorem ipsum hahahahaha \n dwdwdwadwa");
    $printer -> cut();
    
    $printer -> close();
	
	
	 echo "Proses Print\n";
} catch (Exception $e) {
    echo "Couldn't print to this printer: " . $e -> getMessage() . "\n";
}


// $tmpdir = sys_get_temp_dir();
// $file =  tempnam($tmpdir, 'ctk');  
// $handle = fopen($file, 'w');

// fwrite($handle, $html);
// fwrite($handle, "lorem ipsum hahahahaha \n dwdwdwadwa");
// fclose($handle);
// copy($file, "//localhost/".$json_data['nama_printer']);
// unlink($file);


?>