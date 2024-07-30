<?php
include 'include/config.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM items WHERE id = $id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
    } else {
        $_SESSION['notification'] = "Barang tidak ditemukan.";
        $_SESSION['notification_type'] = 'danger';
        header('Location: index.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $nama_barang = $_POST['nama_barang'];
    $jumlah_barang = intval($_POST['jumlah_barang']);
    $satuan = $_POST['satuan'];

    $sql = "UPDATE items SET nama_barang = '$nama_barang', jumlah_barang = $jumlah_barang, satuan = '$satuan' WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        $_SESSION['notification'] = 'Barang berhasil diperbarui';
        $_SESSION['notification_type'] = 'success';
        header('Location: index.php');
        exit;
    } else {
        $_SESSION['notification'] = "Error: " . $sql . "<br>" . $conn->error;
        $_SESSION['notification_type'] = 'danger';
        header('Location: index.php');
        exit;
    }
}

include 'include/header.php';
?>

<div class="container mt-4">
    <h2>Edit Item</h2>
    <form action="edit.php" method="post">
        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
        <div class="form-group">
            <label for="nama_barang">Nama Barang</label>
            <input type="text" class="form-control" id="nama_barang" name="nama_barang" value="<?php echo $row['nama_barang']; ?>" required>
        </div>
        <div class="form-group">
            <label for="jumlah_barang">Jumlah Barang</label>
            <input type="number" class="form-control" id="jumlah_barang" name="jumlah_barang" value="<?php echo $row['jumlah_barang']; ?>" required>
        </div>
        <div class="form-group">
            <label for="satuan">Satuan</label>
            <select class="form-control" id="satuan" name="satuan" required>
                <option value="meter" <?php if($row['satuan'] == 'meter') echo 'selected'; ?>>Meter</option>
                <option value="kilogram" <?php if($row['satuan'] == 'kilogram') echo 'selected'; ?>>Kilogram</option>
                <option value="liter" <?php if($row['satuan'] == 'liter') echo 'selected'; ?>>Liter</option>
                <option value="piece" <?php if($row['satuan'] == 'piece') echo 'selected'; ?>>Piece</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
</div>

<?php include 'include/footer.php'; ?>
