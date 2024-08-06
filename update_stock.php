<?php
include 'include/config.php';
require 'sync_to_google_sheet.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $jumlah_masuk = intval($_POST['jumlah_masuk']);
    $jumlah_keluar = intval($_POST['jumlah_keluar']);

    // Dapatkan informasi barang
    $sql = "SELECT kode_barang, nama_barang, satuan FROM items WHERE id = $id";
    $result = $conn->query($sql);
    $item = $result->fetch_assoc();

    // Update jumlah barang
    $sql_update = "UPDATE items SET 
                    jumlah_barang = jumlah_barang + $jumlah_masuk - $jumlah_keluar, 
                    barang_masuk = barang_masuk + $jumlah_masuk, 
                    barang_keluar = barang_keluar + $jumlah_keluar 
                WHERE id = $id";

    if ($conn->query($sql_update) === TRUE) {
        // Masukkan riwayat barang masuk
        if ($jumlah_masuk > 0) {
            $sql_history_in = "INSERT INTO item_history (kode_barang, nama_barang, jumlah, tipe, tanggal, satuan) 
                                VALUES ('{$item['kode_barang']}', '{$item['nama_barang']}', $jumlah_masuk, 'masuk', NOW(), '{$item['satuan']}')";
            $conn->query($sql_history_in);
        }

        // Masukkan riwayat barang keluar
        if ($jumlah_keluar > 0) {
            $sql_history_out = "INSERT INTO item_history (kode_barang, nama_barang, jumlah, tipe, tanggal, satuan) 
                                VALUES ('{$item['kode_barang']}', '{$item['nama_barang']}', $jumlah_keluar, 'keluar', NOW(), '{$item['satuan']}')";
            $conn->query($sql_history_out);
        }

        // Sinkronisasi dengan Google Sheets
        $spreadsheetId = '1-Zf0u_TJLnP7TOMEtpTutbJYwCs-XERk0leV784Bmc4';
        $googleSheet = new GoogleSheet($spreadsheetId);

        // Ambil data terbaru dari database
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

        echo "Stock updated and synchronized successfully";
    } else {
        echo "Error: " . $sql_update . "<br>" . $conn->error;
    }

    $conn->close();
}
?>
