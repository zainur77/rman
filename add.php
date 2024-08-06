<?php
include 'include/config.php';
require 'google_sheet.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$notification = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $kode_barang = strtoupper(substr(md5(uniqid()), 0, 4));
    $nama_barang = $_POST['nama_barang'];
    $jumlah_barang = intval($_POST['jumlah_barang']);
    $satuan = $_POST['satuan'];

    // Cek ID yang tersedia
    $sql_check_deleted_id = "SELECT id FROM deleted_ids LIMIT 1";
    $result = $conn->query($sql_check_deleted_id);

    if ($result->num_rows > 0) {
        // Ambil ID yang tersedia
        $row = $result->fetch_assoc();
        $new_id = $row['id'];

        // Hapus ID dari tabel deleted_ids
        $sql_delete_id = "DELETE FROM deleted_ids WHERE id = $new_id";
        $conn->query($sql_delete_id);

        // Tambahkan item baru dengan ID yang tersedia
        $sql = "INSERT INTO items (id, kode_barang, nama_barang, jumlah_barang, satuan) VALUES ($new_id, '$kode_barang', '$nama_barang', $jumlah_barang, '$satuan')";
    } else {
        // Tambahkan item baru dengan auto-increment ID
        $sql = "INSERT INTO items (kode_barang, nama_barang, jumlah_barang, satuan) VALUES ('$kode_barang', '$nama_barang', $jumlah_barang, '$satuan')";
    }

    if ($conn->query($sql) === TRUE) {
        // Sinkronisasi dengan Google Sheets
        $spreadsheetId = '1-Zf0u_TJLnP7TOMEtpTutbJYwCs-XERk0leV784Bmc4';
        $googleSheet = new GoogleSheet($spreadsheetId);

        // Ambil data terbaru dari database
        $sql = "SELECT id, kode_barang, nama_barang, jumlah_barang, satuan FROM items ORDER BY id ASC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $values = [];
            while ($row = $result->fetch_assoc()) {
                $values[] = array_values($row);
            }

            $range = 'Sheet1!A2'; // Sesuaikan dengan sheet dan range yang diinginkan
            $googleSheet->updateValues($range, $values);
        }

        $_SESSION['notification'] = 'Barang berhasil ditambahkan dan disinkronkan dengan Google Sheets';
        $_SESSION['notification_type'] = 'success';
        header('Location: index.php');
        exit;
    } else {
        $_SESSION['notification'] = "Error: " . $sql . "<br>" . $conn->error;
        $_SESSION['notification_type'] = 'danger';
        header('Location: index.php');
        exit;
    }

    $conn->close();
}

include 'include/header.php';
?>

<div class="container mt-4">
    <h2>Add Item</h2>
    <form action="add.php" method="post">
        <div class="form-group">
            <label for="kode_barang">Kode Barang</label>
            <input type="text" class="form-control" id="kode_barang" name="kode_barang" readonly value="<?php echo strtoupper(substr(md5(uniqid()), 0, 4)); ?>" placeholder="Kode Barang akan dihasilkan secara otomatis">
        </div>
        <div class="form-group">
            <label for="nama_barang">Nama Barang</label>
            <input type="text" class="form-control" id="nama_barang" name="nama_barang" required placeholder="Masukkan nama barang">
        </div>
        <div class="form-group">
            <label for="jumlah_barang">Jumlah Barang</label>
            <input type="number" class="form-control" id="jumlah_barang" name="jumlah_barang" required placeholder="Masukkan jumlah barang">
        </div>
        <div class="form-group">
            <label for="satuan">Satuan</label>
            <select class="form-control" id="satuan" name="satuan" required>
                <option value="" disabled selected>Pilih satuan</option>
                <option value="meter">Meter</option>
                <option value="kilogram">Kilogram</option>
                <option value="liter">Liter</option>
                <option value="piece">Piece</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Add Item</button>
    </form>
</div>

<?php include 'include/footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js"></script>
