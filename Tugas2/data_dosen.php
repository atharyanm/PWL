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
            <a href="#" onclick="loadPage('cari_dosen')" class="btn btn-primary ml-2">
                <i class="fas fa-search"></i> Cari Data Dosen
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
                    <th>Username</th>
                    <th>Password</th>
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
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['password']); ?></td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="#" 
                                    onclick="loadPage('edit_dosen&npp=<?php echo $row['npp']; ?>')" 
                                    class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                </a>
                                <button type="button" class="btn btn-danger btn-sm btn-hapus" 
                                        data-npp="<?= $row['npp'] ?>">
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
                <a class="page-link" href="#" onclick="loadPaginatedData(<?= $page-1 ?>)">Sebelumnya</a>
            </li>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                <a class="page-link" href="#" onclick="loadPaginatedData(<?= $i ?>)"><?= $i ?></a>
            </li>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
            <li class="page-item">
                <a class="page-link" href="#" onclick="loadPaginatedData(<?= $page+1 ?>)">Selanjutnya</a>
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
                Apakah Anda yakin ingin menghapus data dosen ini?
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
                Data dosen berhasil dihapus!
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
    // Initialize delete handlers
    bindDeleteButtons();

    // Pagination handler
    $(document).on('click', '.pagination .page-link', function(e) {
        e.preventDefault();
        if ($(this).closest('li').hasClass('active')) return;

        let page;
        const text = $(this).text();
        const currentPage = parseInt($('.pagination .active .page-link').text());

        if (text === 'Sebelumnya') {
            page = currentPage - 1;
        } else if (text === 'Selanjutnya') {
            page = currentPage + 1;
        } else {
            page = parseInt(text);
        }

        loadPaginatedData(page);
    });
});

function bindDeleteButtons() {
    $('.btn-hapus').off('click').on('click', function() {
        const npp = $(this).data('npp');
        $('#confirmDeleteModal').modal('show');

        $('#confirmDeleteBtn').off('click').on('click', function() {
            $.ajax({
                url: 'hapus_dosen.php',
                method: 'POST',
                data: { npp: npp },
                dataType: 'json',
                success: function(response) {
                    $('#confirmDeleteModal').modal('hide');
                    if (response.status === 'success') {
                        setTimeout(function() {
                            $('#successModal').modal('show');
                        }, 500);
                    } else {
                        $('#errorMessage').text(response.message);
                        $('#errorModal').modal('show');
                    }
                },
                error: function() {
                    $('#confirmDeleteModal').modal('hide');
                    $('#errorMessage').text('Terjadi kesalahan sistem');
                    $('#errorModal').modal('show');
                }
            });
        });
    });
}

function loadPaginatedData(page) {
    $.ajax({
        url: 'data_dosen.php',
        method: 'GET',
        data: { page: page },
        beforeSend: function() {
            $('table, .pagination').addClass('opacity-50');
        },
        success: function(response) {
            const $newContent = $(response);
            $('table').replaceWith($newContent.find('table'));
            $('.pagination').replaceWith($newContent.find('.pagination'));
            bindDeleteButtons();
        },
        error: function() {
            alert('Gagal memuat data');
        },
        complete: function() {
            $('table, .pagination').removeClass('opacity-50');
        }
    });
}

// Modal events
$('#successModal').on('hidden.bs.modal', function() {
    loadPaginatedData(1);
});

$('#errorModal').on('hidden.bs.modal', function() {
    loadPaginatedData(1);
});
</script>


