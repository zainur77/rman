<?php
include 'include/config.php';

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

        echo "Stock updated successfully";
    } else {
        echo "Error: " . $sql_update . "<br>" . $conn->error;
    }

    $conn->close();
}
?>
