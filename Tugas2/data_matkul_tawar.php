<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['status'] != 'admin') {
    header("Location: index.php");
    exit();
}

require 'koneksi.php';

// Pagination setup
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

$total_result = $koneksi->query("SELECT COUNT(DISTINCT id_tawar) as total FROM matakuliah_tawar");
$total_data = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_data / $limit);

// Query with joins and actual kelompok field
$query = "SELECT mt.id_tawar, 
          REPLACE(mt.kelompok, '.', '') as kelompok,
          mt.hari, mt.jam, mt.ruang,
          m.namamatkul, d.namadosen 
          FROM matakuliah_tawar mt
          INNER JOIN matkul m ON mt.idmatkul = m.idmatkul
          INNER JOIN dosen d ON mt.npp = d.npp
          GROUP BY mt.id_tawar
          ORDER BY mt.hari ASC, mt.jam ASC
          LIMIT $start, $limit";

$result = $koneksi->query($query);
?>

<div class="container mt-4">
    <h2>Data Jadwal Mata Kuliah</h2>
    
    <div class="row mb-3">
        <div class="col">
            <a href="#" onclick="loadPage('tambah_matkul_tawar')" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Jadwal
            </a>
            <a href="cetak_matkul_tawar_pdf.php" class="btn btn-success ml-2" target="_blank">
                <i class="fas fa-print"></i> Cetak PDF
            </a>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>No.</th>
                    <th>Mata Kuliah</th>
                    <th>Dosen</th>
                    <th>Kelompok</th>
                    <th>Hari</th>
                    <th>Jam</th>
                    <th>Ruang</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if ($result->num_rows > 0) {
                    $no = $start + 1;
                    while ($row = $result->fetch_assoc()) {
                ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['namamatkul']) ?></td>
                        <td><?= htmlspecialchars($row['namadosen']) ?></td>
                        <td><?= htmlspecialchars($row['kelompok']) ?></td>
                        <td><?= htmlspecialchars($row['hari']) ?></td>
                        <td><?= htmlspecialchars($row['jam']) ?></td>
                        <td><?= htmlspecialchars($row['ruang']) ?></td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="#" 
                                   onclick="loadPage('edit_matkul_tawar&id=<?= $row['id_tawar'] ?>')" 
                                   class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <button type="button" 
                                        class="btn btn-danger btn-sm btn-hapus" 
                                        data-id="<?= $row['id_tawar'] ?>">
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
                        <td colspan="8" class="text-center">Tidak ada data jadwal</td>
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
                    <a class="page-link" href="#" onclick="loadPaginatedData(<?= $page-1 ?>)">Previous</a>
                </li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                    <a class="page-link" href="#" onclick="loadPaginatedData(<?= $i ?>)"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
                <li class="page-item">
                    <a class="page-link" href="#" onclick="loadPaginatedData(<?= $page+1 ?>)">Next</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
</div>

<!-- Include modals -->
<!-- Confirm Delete Modal -->
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
                <p>Apakah Anda yakin ingin menghapus jadwal ini?</p>
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
                Data jadwal berhasil dihapus!
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
    bindDeleteButtons();
});

function bindDeleteButtons() {
    $('.btn-hapus').off('click').on('click', function() {
        const idToDelete = $(this).data('id');
        $('#confirmDeleteModal').modal('show');

        $('#confirmDeleteBtn').off('click').on('click', function() {
            $.ajax({
                url: 'hapus_matkul_tawar.php',
                method: 'POST',
                data: { id_tawar: idToDelete },
                dataType: 'json',
                success: function(response) {
                    $('#confirmDeleteModal').modal('hide');
                    if (response.status === 'success') {
                        $('#successModal').modal('show');
                    } else {
                        $('#errorMessage').text(response.message);
                        $('#errorModal').modal('show');
                    }
                },
                error: function(xhr, status, error) {
                    $('#confirmDeleteModal').modal('hide');
                    $('#errorMessage').text('Terjadi kesalahan sistem: ' + error);
                    $('#errorModal').modal('show');
                }
            });
        });
    });
}

function loadPaginatedData(page) {
    $.ajax({
        url: 'data_matkul_tawar.php',
        method: 'GET',
        data: { page: page },
        success: function(response) {
            $('.container').html($(response).find('.container').html());
            bindDeleteButtons();
        },
        error: function() {
            alert('Gagal memuat data');
        }
    });
}

$('#successModal').on('hidden.bs.modal', function() {
    loadPaginatedData(1);
});
</script>

<?php $koneksi->close(); ?>