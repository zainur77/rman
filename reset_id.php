<?php
include 'include/config.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

try {
    // Mulai transaksi
    $conn->begin_transaction();

    // Buat tabel sementara
    $create_temp_table = "CREATE TEMPORARY TABLE temp_items AS SELECT * FROM items";
    if (!$conn->query($create_temp_table)) {
        throw new Exception("Error creating temporary table: " . $conn->error);
    }

    // Hapus data dari tabel asli
    $truncate_table = "TRUNCATE TABLE items";
    if (!$conn->query($truncate_table)) {
        throw new Exception("Error truncating table: " . $conn->error);
    }

    // Reset auto-increment
    $reset_auto_increment = "ALTER TABLE items AUTO_INCREMENT = 1";
    if (!$conn->query($reset_auto_increment)) {
        throw new Exception("Error resetting auto-increment: " . $conn->error);
    }

    // Salin data kembali dari tabel sementara ke tabel asli dengan ID yang diatur ulang
    $insert_back = "INSERT INTO items (kode_barang, nama_barang, jumlah_barang, satuan, barang_masuk, barang_keluar)
                    SELECT kode_barang, nama_barang, jumlah_barang, satuan, barang_masuk, barang_keluar FROM temp_items";
    if (!$conn->query($insert_back)) {
        throw new Exception("Error inserting data back: " . $conn->error);
    }

    // Commit transaksi
    $conn->commit();

    $_SESSION['notification'] = 'ID berhasil di-reset';
    $_SESSION['notification_type'] = 'success';
} catch (Exception $e) {
    // Rollback transaksi jika ada kesalahan
    $conn->rollback();
    $_SESSION['notification'] = $e->getMessage();
    $_SESSION['notification_type'] = 'danger';
}

header('Location: index.php');
exit;

$conn->close();
?>
