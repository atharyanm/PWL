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

// Handle deletion
if (isset($_GET['delete_npp'])) {
    $delete_npp = $_GET['delete_npp'];
    $koneksi->query("DELETE FROM dosen WHERE npp = '$delete_npp'");
    header("Location: data_dosen.php");
    exit();
}

// Fetch all data
$limit = 10; // Jumlah data per halaman
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Hitung total data
$total_result = $koneksi->query("SELECT COUNT(*) AS total FROM dosen");
$total_data = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_data / $limit);

// Hitung nomor urut
$no = ($page - 1) * $limit + 1;

// Modifikasi query untuk mengambil data dengan pagination
$result = $koneksi->query("SELECT * FROM dosen LIMIT $start, $limit");
?>

<div class="container mt-4">
    <h2>Data Dosen</h2>
    
    <a href="#" onclick="loadPage('tambah_dosen')" class="btn btn-primary mb-3">Tambah Data Dosen</a>
    
    <a href="cetak_dosen_pdf.php" class="btn btn-success mb-3" target="_blank">
        <i class="fas fa-print"></i> Cetak PDF
    </a>
    <table class="table table-bordered table-striped">
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
                        <a href="#" onclick="loadPage('edit_dosen&npp=<?php echo $row['npp']; ?>')" class="btn btn-warning btn-sm">Edit</a>
                        <button class="btn btn-danger btn-sm btn-hapus" data-npp="<?php echo $row['npp']; ?>">Hapus</button>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    
    <!-- Pagination -->
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?php echo $page - 1; ?>">Sebelumnya</a>
                </li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?php echo $page + 1; ?>">Selanjutnya</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus data dosen ini?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Hapus</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Notifikasi Berhasil Hapus -->
<div class="modal fade" id="successDeleteModal" tabindex="-1" role="dialog" aria-labelledby="successDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="successDeleteModalLabel">Penghapusan Berhasil</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Data dosen berhasil dihapus.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    // Menangani klik pada tombol hapus dengan modal konfirmasi
    $('.btn-hapus').on('click', function() {
        const deleteNpp = $(this).data('npp');
        
        // Tampilkan modal konfirmasi hapus
        $('#confirmDeleteModal').modal('show');
        
        // Jika tombol konfirmasi diklik
        $('#confirmDeleteBtn').off('click').on('click', function() {
            $.ajax({
                url: 'hapus_dosen.php', // Buat file ini untuk handle penghapusan
                method: 'GET',
                data: { delete_npp: deleteNpp },
                dataType: 'json',
                success: function(response) {
                    $('#confirmDeleteModal').modal('hide');
                    if (response.status === 'success') {
                        $('#successDeleteModal').modal('show');
                        $('#successDeleteModal').on('hidden.bs.modal', function () {
                            location.reload();
                        });
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan saat menghapus data.');
                }
            });
        });
    });
});
</script>

<?php
$koneksi->close();
?>