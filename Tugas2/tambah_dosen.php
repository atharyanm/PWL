<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['status'] != 'admin') {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Tambah Data Dosen</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3>Tambah Data Dosen</h3>
            </div>
            <div class="card-body">
                <form id="formTambahDosen" action="proses_tambah_dosen.php" method="POST">
                    <div class="form-group">
                        <label>NPP</label>
                        <div class="input-group">
                            <input type="text" class="form-control col-md-2" 
                                   value="0686.11" readonly>
                            <select name="tahun_npp" class="form-control col-md-2" required>
                                <?php
                                for($th=1990; $th<=2020; $th++){
                                    echo "<option value='$th'>$th</option>";
                                }
                                ?>
                            </select>
                            <input type="text" name="nomor_urut" 
                                   class="form-control col-md-3" 
                                   placeholder="No. Urut" 
                                   required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Nama Dosen</label>
                        <input type="text" name="nama_dosen" 
                               class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label>Homebase</label>
                        <select name="homebase" class="form-control" required>
                            <?php
                            $homebase = ['A11','A12','A14','A15','A16','A17','A22','A24','P31'];
                            foreach($homebase as $hb){
                                echo "<option value='$hb'>$hb</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="data_dosen.php" class="btn btn-secondary">Kembali</a>
                </form>
            </div>
        </div>
    </div>

<!-- Modal Sukses -->
<div class="modal fade" id="modalSukses" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Berhasil</h5>
            </div>
            <div class="modal-body text-center">
                <p id="modalSuksesPesan">Data berhasil ditambahkan</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#formTambahDosen').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            type: 'POST',
            url: 'proses_tambah_dosen.php',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Tampilkan modal sukses
                    $('#modalSukses').modal('show');
                    
                    // Reset form setelah modal ditampilkan
                    $('#formTambahDosen')[0].reset();
                } else {
                    // Tampilkan pesan error
                    alert(response.message);
                }
            },
            error: function() {
                alert('Terjadi kesalahan saat mengirim data');
            }
        });
    });

    // Tambahkan event listener untuk menutup modal
    $('#modalSukses').on('hidden.bs.modal', function () {
        // Tetap di halaman form
        // Tidak perlu redirect
    });
});
</script>
</body>
</html>