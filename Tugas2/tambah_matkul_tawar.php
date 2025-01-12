<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['status'] != 'admin') {
    header("Location: index.php");
    exit();
}

require 'koneksi.php';

// Get matkul list with SKS
$matkul_query = "SELECT idmatkul, namamatkul, sks FROM matkul ORDER BY namamatkul";
$matkul_result = $koneksi->query($matkul_query);

// Get dosen list
$dosen_query = "SELECT npp, namadosen FROM dosen ORDER BY namadosen";
$dosen_result = $koneksi->query($dosen_query);

// Arrays for dropdowns
$hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
$gedung = range('A', 'J');
$lantai = range(1, 7);
$ruang = range(1, 15);
?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3>Tambah Jadwal Mata Kuliah</h3>
        </div>
        <div class="card-body">
            <form id="formTambahJadwal" method="POST">
                <div class="row">
                    <!-- Left Column -->
                    <div class="col-md-6">
                        <!-- Mata Kuliah -->
                        <div class="form-group">
                            <label>Mata Kuliah</label>
                            <select name="idmatkul" id="idmatkul" class="form-control" required>
                                <option value="">Pilih Mata Kuliah</option>
                                <?php while($row = $matkul_result->fetch_assoc()) { ?>
                                    <option value="<?= $row['idmatkul'] ?>" data-sks="<?= $row['sks'] ?>">
                                        <?= $row['namamatkul'] ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <!-- Dosen -->
                        <div class="form-group">
                            <label>Dosen</label>
                            <select name="npp" class="form-control" required>
                                <option value="">Pilih Dosen</option>
                                <?php while($row = $dosen_result->fetch_assoc()) { ?>
                                    <option value="<?= $row['npp'] ?>"><?= $row['namadosen'] ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <!-- Kelompok -->
                        <div class="form-group">
                            <label>Kelompok</label>
                            <div class="input-group">
                                <input type="text" id="kodeKelompok" name="kodeKelompokBase" class="form-control" readonly>
                                <div class="input-group-append">
                                    <span class="input-group-text">.</span>
                                </div>
                                <select name="noKelompok" class="form-control col-md-3" required>
                                    <?php for($i = 1; $i <= 10; $i++) { ?>
                                        <option value="<?= sprintf("%02d", $i) ?>"><?= sprintf("%02d", $i) ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-md-6">
                        <!-- Hari -->
                        <div class="form-group">
                            <label>Hari</label>
                            <select name="hari" class="form-control" required>
                                <?php foreach($hari as $h) { ?>
                                    <option value="<?= $h ?>"><?= $h ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <!-- Jam -->
                        <div class="form-group">
                            <label>Jam</label>
                            <select name="jam" id="jamKuliah" class="form-control" required>
                                <option value="">Pilih Jam Kuliah</option>
                            </select>
                        </div>

                        <!-- Ruang -->
                        <div class="form-group">
                            <label>Ruang</label>
                            <div class="input-group">
                                <select name="gedung" class="form-control" required>
                                    <?php foreach($gedung as $g) { ?>
                                        <option value="<?= $g ?>"> <?= $g ?></option>
                                    <?php } ?>
                                </select>
                                <div class="input-group-append">
                                    <span class="input-group-text">.</span>
                                </div>
                                <select name="lantai" class="form-control" required>
                                    <?php foreach($lantai as $l) { ?>
                                        <option value="<?= $l ?>"><?= $l ?></option>
                                    <?php } ?>
                                </select>
                                <div class="input-group-append">
                                    <span class="input-group-text">.</span>
                                </div>
                                <select name="ruang" class="form-control" required>
                                    <?php foreach($ruang as $r) { ?>
                                        <option value="<?= $r ?>"><?= $r ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                        <button type="button" class="btn btn-danger" onclick="loadPage('data_matkul_tawar')">
                            <i class="fas fa-times"></i> Batal
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modals -->
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
                Data jadwal berhasil disimpan!
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
    function generateTimeSlots(sks) {
        const slots = [];
        const duration = sks * 50;
        const startTime = 7 * 60;
        const endTime = 21 * 60;
        
        for(let time = startTime; time <= endTime - duration; time += 50) {
            const start = `${Math.floor(time/60)}:${(time%60).toString().padStart(2,'0')}`;
            const end = `${Math.floor((time+duration)/60)}:${((time+duration)%60).toString().padStart(2,'0')}`;
            slots.push(`${start}-${end}`);
        }
        return slots;
    }

    $('#idmatkul').change(function() {
    const selectedMatkul = $(this).val();
    const sks = $('option:selected', this).data('sks');
    
    if(selectedMatkul) {
        const parts = selectedMatkul.split('.');
        const prefix = parts[0];  // Gets A12
        const number = parts[1];  // Gets full number like 54101
        
        // Ambil digit ke-4 dan ke-5 dari kode matkul
        const kelompokDigits = number.substring(1, 3); // Mengambil '41' dari '54101'
        const kelompokBase = prefix + '.' + kelompokDigits;
        $('#kodeKelompok').val(kelompokBase);
        
        const timeSlots = generateTimeSlots(sks);
        const jamSelect = $('#jamKuliah');
        jamSelect.empty().append('<option value="">Pilih Jam Kuliah</option>');
        timeSlots.forEach(slot => jamSelect.append(`<option value="${slot}">${slot}</option>`));
    }
});

    $('#formTambahJadwal').on('submit', function(e) {
        e.preventDefault();
        
        // Get form data
        const formData = {
            idmatkul: $('#idmatkul').val(),
            npp: $('select[name="npp"]').val(),
            hari: $('select[name="hari"]').val(),
            jam: $('select[name="jam"]').val(),
            gedung: $('select[name="gedung"]').val(),
            lantai: $('select[name="lantai"]').val(),
            ruang: $('select[name="ruang"]').val(),
            kodeKelompokBase: $('#kodeKelompok').val(),
            noKelompok: $('select[name="noKelompok"]').val()
        };
    
        $.ajax({
            url: 'proses_tambah_matkul_tawar.php',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if(response.status === 'success') {
                    $('#successModal').modal('show');
                } else {
                    $('#errorMessage').text(response.message);
                    $('#errorModal').modal('show');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', xhr.responseText);
                $('#errorMessage').text('Terjadi kesalahan sistem. Silakan coba lagi.');
                $('#errorModal').modal('show');
            }
        });
    });

    $('#successModal').on('hidden.bs.modal', function() {
        loadPage('data_matkul_tawar');
    });
});
</script>