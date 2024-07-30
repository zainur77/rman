<?php
include 'include/config.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "DELETE FROM items WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        $_SESSION['notification'] = 'Barang berhasil dihapus';
        $_SESSION['notification_type'] = 'success';
    } else {
        $_SESSION['notification'] = "Error: " . $sql . "<br>" . $conn->error;
        $_SESSION['notification_type'] = 'danger';
    }

    header('Location: index.php');
    exit;
}
?>
