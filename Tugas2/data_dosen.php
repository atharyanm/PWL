<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['status'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Include database connection
require 'koneksi.php';

// Cek apakah koneksi berhasil
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Konfigurasi Pagination
$limit = 5; // Jumlah data per halaman
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Hitung total data
$total_result = $koneksi->query("SELECT COUNT(*) AS total FROM dosen");
$total_data = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_data / $limit);

// Hitung nomor urut
$no = ($page - 1) * $limit + 1;

// Query dengan pagination
$result = $koneksi->query("SELECT * FROM dosen LIMIT $start, $limit");
?>

<div class="container mt-4">
    <h2>Data Dosen</h2>
    
    <div class="row mb-3">
        <div class="col">
            <a href="#" onclick="loadPage('tambah_dosen')" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Data Dosen
            </a>
            <a href="cetak_dosen_pdf.php" class="btn btn-success ml-2" target="_blank">
                <i class="fas fa-print"></i> Cetak PDF
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="dosenTable">
            <thead class="thead-dark">
                <tr>
                    <th>No.</th>
                    <th>NPP</th>
                    <th>Nama Dosen</th>
                    <th>Homebase</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo htmlspecialchars($row['npp']); ?></td>
                        <td><?php echo htmlspecialchars($row['namadosen']); ?></td>
                        <td><?php echo htmlspecialchars($row['homebase']); ?></td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="#" 
                                    onclick="loadPage('edit_dosen&npp=<?php echo $row['npp']; ?>')" 
                                    class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                </a>
                                <button class="btn btn-danger btn-sm btn-hapus" 
                                        data-npp="<?php echo $row['npp']; ?>">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="#" onclick="loadPage('data_dosen', '<?php echo $page-1; ?>')">Sebelumnya</a>
                </li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                    <a class="page-link" href="#" onclick="loadPage('data_dosen', '<?php echo $i; ?>')"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <li class="page-item">
                    <a class="page-link" href="#" onclick="loadPage('data_dosen', '<?php echo $page+1; ?>')">Selanjutnya</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus data dosen?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Hapus</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Sukses -->
<div class="modal fade" id="successDeleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h5 class="modal-title">Berhasil</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Data dosen berhasil dihapus
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Error -->
<div class="modal fade" id="errorDeleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h5 class="modal-title">Gagal Menghapus</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Pesan error akan diisi dinamis -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Fungsi load page dengan parameter opsional
    function loadPage(page, param = null) {
        $.ajax({
            url: page + '.php',
            method: 'GET',
            data : { 
                page: param  // Kirim parameter halaman
            },
            success: function(response) {
                $('#contentPage').html(response);
            },
            error: function() {
                alert('Gagal memuat halaman');
            }
        });
    }

    // Konfirmasi Hapus
    let nppToDelete;
    $('#dosenTable').on('click', '.btn-hapus', function() {
        nppToDelete = $(this).data('npp');
        $('#confirmDeleteModal').modal('show');
    });

    $('#confirmDeleteBtn').on('click', function() {
        $.ajax({
            url: 'proses_hapus_dosen.php',
            method: 'POST',
            data: { npp: nppToDelete },
            success: function(response) {
                $('#confirmDeleteModal').modal('hide');
                if (response.status === 'success') {
                    $('#successDeleteModal').modal('show');
                    loadPage('data_dosen'); // Refresh data
                } else {
                    $('#errorDeleteModal .modal-body').text(response.message);
                    $('#errorDeleteModal').modal('show');
                }
            },
            error: function() {
                alert('Gagal menghapus data');
            }
        });
    });
});
</script>