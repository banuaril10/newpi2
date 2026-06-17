<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require '../vendor/autoload.php';
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;

// $html = $_POST['html'];
$html = "testestse";
// $bt_port = $_POST['bt_port']; // COM5, COM8, dll
$bt_port = "COM6"; // COM5, COM8, dll

try {
    // Untuk Bluetooth printer di Windows, gunakan WindowsPrintConnector dengan nama share printer
    // Atau jika terdeteksi sebagai USB printer, gunakan nama printer-nya
    
    // Cara 1: Jika printer sudah terinstall sebagai printer Windows (share name)
    $connector = new WindowsPrintConnector($bt_port); // misal "Bluetooth Printer"
    
    // Cara 2: Jika menggunakan COM port (jarang berhasil untuk Bluetooth)
    // $connector = new FilePrintConnector($bt_port);
    
    $printer = new Printer($connector);
    $printer->initialize();
    
    $printer->setFont(Printer::FONT_B);
    $printer->setTextSize(1, 1);
    $printer->text($html);
    
    $printer->cut();
    $printer->close();
    
    echo json_encode(["success" => true, "message" => "Print ke Bluetooth berhasil"]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>