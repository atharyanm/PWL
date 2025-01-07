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
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $koneksi->query("DELETE FROM mahasiswa WHERE id = '$delete_id'");
    header("Location: homepage_admin.php"); // Arahkan ke data_mahasiswa.php setelah penghapusan
    exit();
}

// Fetch all data
$limit = 10; // Jumlah data per halaman
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Hitung total data
$total_result = $koneksi->query("SELECT COUNT(*) AS total FROM mahasiswa");
$total_data = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_data / $limit);

// Hitung nomor urut
$no = ($page - 1) * $limit + 1;

// Modifikasi query untuk mengambil data dengan pagination
$result = $koneksi->query("SELECT * FROM mahasiswa LIMIT $start, $limit");
?>

<div class="container mt-4">
    <h2>Data Mahasiswa</h2>
    
    <div class="row mb-3">
        <div class="col">
            <a href="#" onclick="loadPage('tambah_mahasiswa')" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Data Mahasiswa
            </a>
            <a href="cetak_dosen_pdf.php" class="btn btn-success ml-2" target="_blank">
                <i class="fas fa-print"></i> Cetak PDF
            </a>
        </div>
    </div>
    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th>No.</th>
                <th>NIM</th>
                <th>Foto Profil</th>
                <th>Nama</th>
                <th>Username</th>
                <th>Password</th>
                <th>Email</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo htmlspecialchars($row['nim']); ?></td>
                    <td>
                        <?php if ($row['foto_profil']) { ?>
                            <img src="uploads/<?php echo htmlspecialchars($row['foto_profil']); ?>" alt="Foto Profil" width="50" height="50">
                        <?php } else { ?>
                            <span class="badge badge-secondary">Tidak ada foto</span>
                        <?php } ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['nama']); ?></td>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td><?php echo htmlspecialchars($row['password']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td>
                    <div class="btn-group" role="group">
                        <a href="#" 
                        onclick="loadPage('edit_mahasiswa&id=<?php echo $row['id']; ?>')" 
                        class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <button class="btn btn-danger btn-sm btn-hapus" 
                                data-id="<?php echo $row['id']; ?>">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </div>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <!-- Setelah tabel, tambahkan kode pagination -->
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
                Apakah Anda yakin ingin menghapus data ini?
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
                Data mahasiswa berhasil dihapus.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk Pemberitahuan Berhasil Edit -->
<div class="modal fade" id="editSuccessModal" tabindex="-1" role="dialog" aria-labelledby="editSuccessModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSuccessModalLabel">Pemberitahuan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Data mahasiswa berhasil diedit.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
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
        const deleteId = $(this).data('id');
        
        // Tampilkan modal konfirmasi hapus
        $('#confirmDeleteModal').modal('show');
        
        // Jika tombol konfirmasi diklik
        $('#confirmDeleteBtn').off('click').on('click', function() {
            $.ajax({
                url: 'hapus_mahasiswa.php',
                method: 'GET',
                data: { delete_id: deleteId },
                dataType: 'json',
                success: function(response) {
                    $('#confirmDeleteModal').modal('hide'); // Sembunyikan modal konfirmasi hapus
                    if (response.status === 'success') {
                        $('#successDeleteModal').modal('show'); // Tampilkan modal berhasil hapus
                        $('#successDeleteModal').on('hidden.bs.modal', function () {
                            location.reload(); // Reload halaman setelah modal ditutup
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
function loadPaginatedData(page) {
    $.ajax({
        url: 'data_mahasiswa.php',
        method: 'GET',
        data: { page: page },
        success: function(response) {
            // Ekstrak tabel dan pagination dari response
            var $newTable = $(response).find('table');
            var $newPagination = $(response).find('nav');
            
            // Ganti konten tabel dan pagination
            $('table').replaceWith($newTable);
            $('nav').replaceWith($newPagination);
            
            // Tambahkan event listener baru untuk tombol hapus
            bindDeleteButtons();
        },
        error: function() {
            alert('Gagal memuat data');
        }
    });
}

function bindDeleteButtons() {
    $('.btn-hapus').on('click', function() {
        const deleteId = $(this).data('id');
        
        // Tampilkan modal konfirmasi hapus
        $('#confirmDeleteModal').modal('show');
        
        // Jika tombol konfirmasi diklik
        $('#confirmDeleteBtn').off('click').on('click', function() {
            $.ajax({
                url: 'hapus_mahasiswa.php',
                method: 'GET',
                data: { delete_id: deleteId },
                dataType: 'json',
                success: function(response) {
                    $('#confirmDeleteModal').modal('hide');
                    if (response.status === 'success') {
                        $('#successDeleteModal').modal('show');
                        $('#successDeleteModal').on('hidden.bs.modal', function () {
                            // Muat ulang halaman pertama setelah hapus
                            loadPaginatedData(1);
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
}

$(document).ready(function() {
    // Inisialisasi event handler tombol hapus
    bindDeleteButtons();

    // Handler untuk pagination
    $(document).on('click', '.pagination .page-link', function(e) {
        e.preventDefault();
        
        // Hindari reload jika mengklik halaman aktif saat ini
        if ($(this).closest('li').hasClass('active')) return;

        var page = $(this).text();
        
        // Cek jika tombol sebelumnya/selanjutnya
        if ($(this).text() === 'Sebelumnya') {
            page = parseInt($('.pagination .active .page-link').text()) - 1;
        } else if ($(this).text() === 'Selanjutnya') {
            page = parseInt($('.pagination .active .page-link').text()) + 1;
        }
        
        loadPaginatedData(page);
    });
});
</script>

<?php
$koneksi->close();
?>
