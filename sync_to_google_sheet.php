<?php
require 'include/config.php';
require 'google_sheet.php';

$spreadsheetId = '1-Zf0u_TJLnP7TOMEtpTutbJYwCs-XERk0leV784Bmc4'; // Ganti dengan Spreadsheet ID Anda
$googleSheet = new GoogleSheet($spreadsheetId);

$sql = "SELECT id, kode_barang, nama_barang, jumlah_barang, satuan, barang_masuk, barang_keluar FROM items ORDER BY id ASC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $values = [];
    while ($row = $result->fetch_assoc()) {
        $values[] = array_values($row);
    }

    $range = 'Sheet1!A2'; // Sesuaikan dengan sheet dan range yang diinginkan
    $googleSheet->updateValues($range, $values);
}
?>
