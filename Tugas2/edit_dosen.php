<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['status'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Include database connection
require 'koneksi.php';

// Ambil NPP dari parameter GET
$npp = isset($_GET['npp']) ? $koneksi->real_escape_string($_GET['npp']) : null;

if (!$npp) {
    echo "NPP tidak valid";
    exit();
}

// Query ambil data dosen
$query = $koneksi->prepare("SELECT * FROM dosen WHERE npp = ?");
$query->bind_param("s", $npp);
$query->execute();
$result = $query->get_result();

if ($result->num_rows == 0) {
    echo "Data dosen tidak ditemukan";
    exit();
}

$dosen = $result->fetch_assoc();
?>

<div class="container mt-5">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3>Edit Data Dosen</h3>
        </div>
        <div class="card-body">
            <form id="formEditDosen">
                <input type="hidden" name="npp" value="<?php echo htmlspecialchars($dosen['npp']); ?>">
                
                <div class="form-group">
                    <label>NPP</label>
                    <input type="text" 
                           class="form-control" 
                           value="<?php echo htmlspecialchars($dosen['npp']); ?>" 
                           readonly>
                </div>

                <div class="form-group">
                    <label>Nama Dosen</label>
                    <input type="text" 
                           name="nama_dosen" 
                           class="form-control" 
                           value="<?php echo htmlspecialchars($dosen['namadosen']); ?>" 
                           required>
                </div>

                <div class="form-group">
                    <label>Homebase</label>
                    <select name="homebase" class="form-control" required>
                        <?php
                        $homebase = ['A11','A12','A14','A15','A16','A17','A22','A24','P31'];
                        foreach($homebase as $hb){
                            $selected = ($dosen['homebase'] == $hb) ? 'selected' : '';
                            echo "<option value='$hb' $selected>$hb</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Username</label>
                    <input type="text" 
                           name="username" 
                           class="form-control" 
                           value="<?php echo htmlspecialchars($dosen['username']); ?>" 
                           required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="text" 
                           name="password" 
                           class="form-control" 
                           value="<?php echo htmlspecialchars($dosen['password']); ?>" 
                           required>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    <a href="#" onclick="loadPage('data_dosen')" class="btn btn-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#formEditDosen').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            type: 'POST',
            url: 'proses_edit_dosen.php',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Tampilkan modal sukses
                    $('#successModal').modal('show');
                    
                    // Setelah modal ditutup, kembali ke halaman data dosen
                    $('#successModal').on('hidden.bs.modal', function () {
                        loadPage('data_dosen');
                    });
                } else {
                    // Tampilkan pesan error
                    alert(response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error("Error:", error);
                alert('Terjadi kesalahan saat mengirim data');
            }
        });
    });

    // Modal Sukses HTML
    $('body').append(`
        <div class="modal fade" id="successModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">Berhasil</h5>
                        <button type="button" class="close text-white" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-center">
                        <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                        <h4>Data Dosen Berhasil Diperbarui</h4>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" data-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>
    `);
});
</script>