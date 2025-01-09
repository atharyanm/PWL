<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['status'] != 'admin') {
    header("Location: index.php");
    exit();
}

require 'koneksi.php';

// Pagination setup
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

$total_result = $koneksi->query("SELECT COUNT(*) as total FROM matkul");
$total_data = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_data / $limit);

$no = ($page - 1) * $limit + 1;

// Get data with pagination
$result = $koneksi->query("SELECT * FROM matkul ORDER BY idmatkul LIMIT $start, $limit");
?>

<div class="container mt-4">
    <h2>Data Mata Kuliah</h2>
    
    <div class="row mb-3">
        <div class="col">
            <a href="#" onclick="loadPage('tambah_matkul')" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Mata Kuliah
            </a>
            <a href="cetak_matkul_pdf.php" class="btn btn-success ml-2" target="_blank">
                <i class="fas fa-print"></i> Cetak PDF
            </a>
            <a href="#" onclick="loadPage('cari_matkul')" class="btn btn-primary ml-2">
                <i class="fas fa-search"></i> Cari Mata Kuliah
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>No.</th>
                    <th>Kode MK</th>
                    <th>Nama Mata Kuliah</th>
                    <th>SKS</th>
                    <th>Jenis</th>
                    <th>Semester</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) { 
                ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['idmatkul']) ?></td>
                        <td><?= htmlspecialchars($row['namamatkul']) ?></td>
                        <td><?= htmlspecialchars($row['sks']) ?></td>
                        <td><?= htmlspecialchars($row['jns']) ?></td>
                        <td><?= htmlspecialchars($row['smt']) ?></td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="#" 
                                    onclick="loadPage('edit_matkul&kode=<?= $row['idmatkul'] ?>')" 
                                    class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                </a>
                                <button type="button" 
                                        class="btn btn-danger btn-sm btn-hapus" 
                                        data-kode="<?= $row['idmatkul'] ?>">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php 
                    }
                } else {
                ?>
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada data mata kuliah</td>
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
                    <a class="page-link" href="#" onclick="loadPage('data_matkul', '<?= $page-1 ?>')">Previous</a>
                </li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                    <a class="page-link" href="#" onclick="loadPage('data_matkul', '<?= $i ?>')"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <li class="page-item">
                    <a class="page-link" href="#" onclick="loadPage('data_matkul', '<?= $page+1 ?>')">Next</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus mata kuliah ini?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Hapus</button>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Sukses</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Data mata kuliah berhasil dihapus!
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<!-- Error Modal -->
<div class="modal fade" id="errorModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Error</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="errorMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    let kodeToDelete;
    
    $('.btn-hapus').on('click', function() {
        kodeToDelete = $(this).data('kode');
        $('#confirmDeleteModal').modal('show');
    });

    $('#confirmDeleteBtn').on('click', function() {
        $.ajax({
            url: 'hapus_matkul.php',
            method: 'POST',
            data: { idmatkul: kodeToDelete },
            dataType: 'json',
            success: function(response) {
                $('#confirmDeleteModal').modal('hide');
                setTimeout(function() {
                    if (response.status === 'success') {
                        $('#successModal').modal('show');
                    } else {
                        $('#errorMessage').text(response.message);
                        $('#errorModal').modal('show');
                    }
                }, 500);
            },
            error: function(xhr, status, error) {
                $('#confirmDeleteModal').modal('hide');
                $('#errorMessage').text('Terjadi kesalahan sistem: ' + error);
                $('#errorModal').modal('show');
            }
        });
    });

    // Single modal hidden handler
    $('#successModal').on('hidden.bs.modal', function () {
        loadPage('data_matkul');
    });
});
</script>