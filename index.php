<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

include 'include/header.php';
?>
<div class="container mt-4">
    <?php if (isset($_SESSION['notification'])): ?>
        <div class="alert alert-<?php echo $_SESSION['notification_type']; ?> alert-dismissible fade show" role="alert" id="notificationAlert">
            <?php echo $_SESSION['notification']; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php unset($_SESSION['notification'], $_SESSION['notification_type']); ?>
    <?php endif; ?>

    <h2>Daftar Inventori</h2>
    
    <div class="d-flex justify-content-between mb-3">
        <a href="add.php" class="btn btn-success">Add Item</a>
        <div class="d-flex">
            <form action="reset_id.php" method="POST" onsubmit="return confirm('Anda yakin ingin mereset ID?');" class="mr-2">
                <button type="submit" class="btn btn-warning">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </button>
            </form>
            <form class="form-inline" method="GET" action="">
                <input class="form-control mr-sm-2" type="search" placeholder="Cari Barang" aria-label="Search" name="search">
                <button class="btn btn-outline-success my-2 my-sm-0" type="submit">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.415l-3.85-3.85a1.007 1.007 0 0 0-.115-.098zm-5.44 1.055a5.5 5.5 0 1 1 0-11 5.5 5.5 0 0 1 0 11z"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Jumlah Barang</th>
                    <th>Satuan</th>
                    <th>Barang Masuk</th>
                    <th>Barang Keluar</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                include 'include/config.php';
                
                $search = isset($_GET['search']) ? $_GET['search'] : '';
                if ($search) {
                    $sql = "SELECT * FROM items WHERE kode_barang LIKE '%$search%' OR nama_barang LIKE '%$search%' ORDER BY id ASC";
                } else {
                    $sql = "SELECT * FROM items ORDER BY id ASC";
                }

                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td>" . $row["kode_barang"] . "</td>";
                        echo "<td>" . $row["nama_barang"] . "</td>";
                        echo "<td>" . $row["jumlah_barang"] . "</td>";
                        echo "<td>" . $row["satuan"] . "</td>";
                        echo "<td>" . $row["barang_masuk"] . "</td>";
                        echo "<td>" . $row["barang_keluar"] . "</td>";
                        echo "<td>";
                        echo "<a href='edit.php?id=" . $row["id"] . "' class='btn btn-primary btn-sm'>Edit</a> ";
                        echo "<a href='delete.php?id=" . $row["id"] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this item?\")'>Delete</a> ";
                        echo "<button class='btn btn-warning btn-sm' data-toggle='modal' data-target='#updateStockModal' data-id='" . $row["id"] . "' data-nama='" . $row["nama_barang"] . "'>Update Stock</button>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>No items found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Update Stock -->
<div class="modal fade" id="updateStockModal" tabindex="-1" role="dialog" aria-labelledby="updateStockModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateStockModalLabel">Update Stock</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="updateStockForm">
                    <input type="hidden" name="id" id="modalItemId">
                    <div class="form-group">
                        <label for="modalJumlahMasuk">Barang Masuk</label>
                        <input type="number" class="form-control" id="modalJumlahMasuk" name="jumlah_masuk" placeholder="Masukkan jumlah barang masuk" value="0">
                    </div>
                    <div class="form-group">
                        <label for="modalJumlahKeluar">Barang Keluar</label>
                        <input type="number" class="form-control" id="modalJumlahKeluar" name="jumlah_keluar" placeholder="Masukkan jumlah barang keluar" value="0">
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'include/footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js"></script>
<script>
    $('#updateStockModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var nama = button.data('nama');
        var modal = $(this);
        modal.find('#modalItemId').val(id);
        modal.find('#updateStockModalLabel').text('Update Stock for ' + nama);
    });

    $('#updateStockForm').submit(function(e) {
        e.preventDefault(); // Mencegah pengiriman formulir default
        var form = $(this);
        $.ajax({
            type: 'POST',
            url: 'update_stock.php',
            data: form.serialize(),
            success: function(response) {
                alert(response);  // Tampilkan respons dari update_stock.php
                location.reload();  // Refresh halaman setelah update
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText); // Debug jika ada kesalahan
                alert('Error updating stock');
            }
        });
    });

    // Menghilangkan notifikasi setelah 3 detik
    $(document).ready(function() {
        setTimeout(function() {
            $("#notificationAlert").alert('close');
        }, 3000); // 3000 milidetik = 3 detik
    });
</script>