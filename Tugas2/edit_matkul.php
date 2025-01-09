<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['status'] != 'admin') {
    header("Location: index.php");
    exit();
}

require 'koneksi.php';

if (!isset($_GET['kode'])) {
    echo "Kode mata kuliah tidak ditemukan";
    exit();
}

$idmatkul = $_GET['kode'];
$query = "SELECT * FROM matkul WHERE idmatkul = ?";
$stmt = $koneksi->prepare($query);
$stmt->bind_param("s", $idmatkul);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Data mata kuliah tidak ditemukan";
    exit();
}

$matkul = $result->fetch_assoc();
?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3>Edit Data Mata Kuliah</h3>
        </div>
        <div class="card-body">
            <form id="formEditMatkul" action="proses_edit_matkul.php" method="POST">
                <div class="form-group">
                    <label>Kode Mata Kuliah</label>
                    <input type="text" 
                           name="idmatkul" 
                           class="form-control"
                           value="<?php echo htmlspecialchars($matkul['idmatkul']); ?>" 
                           readonly>
                </div>

                <div class="form-group">
                    <label>Nama Mata Kuliah</label>
                    <input type="text" 
                           name="namamatkul" 
                           class="form-control"
                           value="<?php echo htmlspecialchars($matkul['namamatkul']); ?>" 
                           required>
                </div>

                <div class="form-group">
                    <label>SKS</label>
                    <select name="sks" class="form-control col-md-3" required>
                        <?php
                        for($s=1; $s<=6; $s++){
                            $selected = ($matkul['sks'] == $s) ? 'selected' : '';
                            echo "<option value='$s' $selected>$s</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Jenis</label>
                    <select name="jns" class="form-control col-md-7" required>
                        <?php
                        $jenis = array('T' => 'Teori', 'P' => 'Praktikum', 'TP' => 'Teori & Praktikum');
                        foreach($jenis as $k => $v) {
                            $selected = ($matkul['jns'] == $k) ? 'selected' : '';
                            echo "<option value='$k' $selected>$v</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Semester</label>
                    <select name="smt" class="form-control col-md-3" required>
                        <?php
                        for($s=1; $s<=8; $s++){
                            $selected = ($matkul['smt'] == $s) ? 'selected' : '';
                            echo "<option value='$s' $selected>$s</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                    <a href="#" onclick="loadPage('data_matkul')" class="btn btn-danger">
                        <i class="fas fa-times"></i> Batal
                    </a>
                </div>
            </form>
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
                Data mata kuliah berhasil diperbarui!
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
    $('#formEditMatkul').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            method: $(this).attr('method'),
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if(response.status === 'success') {
                    $('#successModal').modal('show');
                } else {
                    $('#errorMessage').text(response.message);
                    $('#errorModal').modal('show');
                }
            },
            error: function() {
                $('#errorMessage').text('Terjadi kesalahan sistem');
                $('#errorModal').modal('show');
            }
        });
    });

    $('#successModal').on('hidden.bs.modal', function () {
        loadPage('data_matkul');
    });
});
</script>