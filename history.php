<?php include 'include/header.php'; ?>
<div class="container mt-4">
    <h2>Riwayat Barang Masuk dan Keluar</h2>
    <div class="d-flex justify-content-between mb-3">
        <form action="clear_history.php" method="POST" onsubmit="return confirm('Anda yakin ingin menghapus semua riwayat?');">
            <button type="submit" class="btn btn-danger">Hapus Semua Riwayat</button>
        </form>
        <form class="form-inline" method="GET" action="">
            <input class="form-control mr-sm-2" type="search" placeholder="Cari Riwayat" aria-label="Search" name="search">
            <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>Kode Barang</th>
                    <th>Nama Barang</th>
                    <th>Jumlah</th>
                    <th>Satuan</th>
                    <th>Tipe</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                <?php
                include 'include/config.php';
                
                $search = isset($_GET['search']) ? $_GET['search'] : '';
                if ($search) {
                    $sql = "SELECT kode_barang, nama_barang, jumlah, satuan, tipe, tanggal FROM item_history WHERE kode_barang LIKE '%$search%' OR nama_barang LIKE '%$search%' OR tipe LIKE '%$search%' ORDER BY tanggal ASC";
                } else {
                    $sql = "SELECT kode_barang, nama_barang, jumlah, satuan, tipe, tanggal FROM item_history ORDER BY tanggal ASC";
                }

                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["kode_barang"] . "</td>";
                        echo "<td>" . $row["nama_barang"] . "</td>";
                        echo "<td>" . $row["jumlah"] . "</td>";
                        echo "<td>" . $row["satuan"] . "</td>";
                        echo "<td>" . $row["tipe"] . "</td>";
                        echo "<td>" . $row["tanggal"] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No history found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'include/footer.php'; ?>
