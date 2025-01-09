<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['status'] != 'admin') {
    header("Location: index.php");
    exit();
}
?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3>Tambah Data Mata Kuliah</h3>
        </div>
        <div class="card-body">
            <form id="formTambahMatkul" action="proses_tambah_matkul.php" method="POST">
                <div class="form-group">
                    <label>Kode Mata Kuliah</label>
                    <div class="input-group">
                        <select name="prog_code" class="form-control col-md-3" required>
                            <?php
                            $prog_codes = ['A12','A13','A14','A15','A16'];
                            foreach($prog_codes as $code) {
                                echo "<option value='$code'>$code</option>";
                            }
                            ?>
                        </select>
                        <div class="input-group-prepend">
                            <span class="input-group-text">.</span>
                        </div>
                        <input type="text" name="kode_mk" 
                               class="form-control col-md-3" 
                               maxlength="5"
                               pattern="[0-9]{5}"
                               placeholder="54101" 
                               required>
                    </div>
                    <small id="kode-error" class="form-text"></small>
                </div>

                <div class="form-group">
                    <label>Nama Mata Kuliah</label>
                    <input type="text" name="namamatkul" 
                           class="form-control" 
                           maxlength="50"
                           required>
                </div>

                <div class="form-group">
                    <label>SKS</label>
                    <select name="sks" class="form-control col-md-3" required>
                        <?php
                        for($s=1; $s<=6; $s++){
                            echo "<option value='$s'>$s</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Jenis</label>
                    <select name="jns" class="form-control col-md-7" required>
                        <option value="T">Teori</option>
                        <option value="P">Praktikum</option>
                        <option value="TP">Teori & Praktikum</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Semester</label>
                    <select name="smt" class="form-control col-md-3" required>
                        <?php
                        for($s=1; $s<=8; $s++){
                            echo "<option value='$s'>$s</option>";
                        }
                        ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan
                </button>
                <button type="button" class="btn btn-danger" onclick="loadPage('data_matkul')">
                    <i class="fas fa-times"></i> Batal
                </button>
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
                Data mata kuliah berhasil ditambahkan!
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
    function resetForm() {
        $('#formTambahMatkul')[0].reset();
        $('#kode-error').html('').removeClass('text-danger text-success');
        $('button[type="submit"]').prop('disabled', false);
        $('select[name="prog_code"]').focus();
    }
    // Validate kode_mk on blur
    $('input[name="kode_mk"]').on('blur', function() {
        let progCode = $('select[name="prog_code"]').val();
        let kodeMk = $(this).val();
        
        if(kodeMk.length === 5) {
            let idmatkul = progCode + '.' + kodeMk;
            $.ajax({
                url: 'check_matkul.php',
                method: 'POST',
                data: { idmatkul: idmatkul },
                dataType: 'json',
                success: function(response) {
                    if(response.exists) {
                        $('#kode-error').html('Kode mata kuliah sudah ada!')
                            .addClass('text-danger').removeClass('text-success');
                        $('button[type="submit"]').prop('disabled', true);
                    } else {
                        $('#kode-error').html('Kode mata kuliah tersedia')
                            .addClass('text-success').removeClass('text-danger');
                        $('button[type="submit"]').prop('disabled', false);
                    }
                }
            });
        }
    });

    // Form submission
    $('#formTambahMatkul').on('submit', function(e) {
        e.preventDefault();
        let formData = $(this).serializeArray();
        
        let progCode = formData.find(f => f.name === 'prog_code').value;
        let kodeMk = formData.find(f => f.name === 'kode_mk').value;
        let idmatkul = progCode + '.' + kodeMk;
        
        formData.push({name: 'idmatkul', value: idmatkul});

        $.ajax({
            url: $(this).attr('action'),
            method: $(this).attr('method'),
            data: $.param(formData),
            dataType: 'json',
            success: function(response) {
                if(response.status === 'success') {
                    resetForm();
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
        resetForm();
    });
});
</script>