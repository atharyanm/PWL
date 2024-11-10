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
$result = $koneksi->query("SELECT * FROM mahasiswa");
?>

<div class="container mt-4">
    <h2>Data Mahasiswa</h2>
    
    <a href="#" onclick="loadPage('tambah_mahasiswa')" class="btn btn-primary mb-3">Tambah Data</a>
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
            <?php $no = 1; while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo htmlspecialchars($row['nim']); ?></td>
                    <td>
                        <?php if ($row['foto_profil']) { ?>
                            <img src="uploads/<?php echo htmlspecialchars($row['foto_profil']); ?>" alt="Foto Profil" width="50" height="50">
                        <?php } else { ?>
                            <span>No Image</span>
                        <?php } ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['nama']); ?></td>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td><?php echo htmlspecialchars($row['password']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td>
                        <a href="#" onclick="loadPage('edit_mahasiswa&id=<?php echo $row['id']; ?>')" class="btn btn-warning btn-sm">Edit</a>
                        <button class="btn btn-danger btn-sm btn-hapus" data-id="<?php echo $row['id']; ?>">Hapus</button>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
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
</script>

<?php
$koneksi->close();
?>
