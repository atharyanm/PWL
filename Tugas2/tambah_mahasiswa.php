<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['status'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Include database connection
require 'koneksi.php';

// Cek permintaan AJAX untuk validasi NIM
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'check_nim' && isset($_POST['nim'])) {
        $nim = $koneksi->real_escape_string($_POST['nim']);
        $result = $koneksi->query("SELECT * FROM mahasiswa WHERE nim = '$nim'");

        if ($result->num_rows > 0) {
            echo 'duplicate'; // NIM sudah terdaftar
        } else {
            echo 'available'; // NIM belum terdaftar
        }
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Mahasiswa</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h2>Tambah Mahasiswa</h2>
    <form id="tambahMahasiswaForm" enctype="multipart/form-data">
        <div class="form-group">
            <label for="nim">NIM:</label>
            <input type="text" class="form-control" id="nim" name="nim" maxlength="14" required style="width: 200px;">
            <small id="nimFeedback" class="form-text text-danger" style="display:none;"></small>
        </div>
        <div class="form-group">
            <label for="nama">Nama:</label>
            <input type="text" class="form-control" id="nama" name="nama" required>
        </div>
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="foto_profil">Foto Profil:</label>
            <input type="file" class="form-control-file" id="foto_profil" name="foto_profil" accept="image/*">
        </div>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Simpan
        </button>
        <button type="button" class="btn btn-danger" onclick="loadPage('data_mahasiswa')">
            <i class="fas fa-times"></i> Batal
        </button>
    </form>
</div>

<div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="errorModalLabel">Informasi</h5>
            </div>
            <div class="modal-body" id="errorMessage">
                <!-- Pesan kesalahan akan ditampilkan di sini -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal" id="modalOkBtn">OK</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    $('#nim').on('blur', function() {
        const nim = $(this).val();
        const nimLength = nim.length;

        if (nimLength < 14) {
            $('#nimFeedback').text("Isian tidak sesuai, silahkan isi dengan benar!").show();
            $(this).focus();
            return;
        } else if (nimLength > 14) {
            $('#nimFeedback').text("Isian NIM tidak sesuai, silahkan isi kembali!").show();
            $(this).focus();
            return;
        } else {
            $('#nimFeedback').hide();
        }

        $.ajax({
            url: 'tambah_mahasiswa.php',
            type: 'POST',
            data: { action: 'check_nim', nim: nim },
            success: function(response) {
                if (response === 'duplicate') {
                    $('#nimFeedback').text("Data sudah ada, silahkan isikan yang lain!").show();
                    $('#nim').focus();
                } else {
                    $('#nimFeedback').hide();
                }
            }
        });
    });

    $('#tambahMahasiswaForm').on('submit', function(event) {
        event.preventDefault();
        
        const nim = $('#nim').val();
        const nimLength = nim.length;

        if (nimLength !== 14) {
            $('#errorMessage').text("Isian NIM harus terdiri dari 14 karakter.");
            $('#errorModal').modal('show');
            $('#nim').focus();
            return;
        }

        var formData = new FormData($(this)[0]);
        $.ajax({
            url: 'proses_tambah_mahasiswa.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                const data = JSON.parse(response);
                $('#errorMessage').text(data.message);
                $('#errorModal').modal('show');
                if (data.status === 'success') {
                    $('#tambahMahasiswaForm')[0].reset();
                }
            },
            error: function() {
                $('#errorMessage').text('Terjadi kesalahan saat meng-upload data.');
                $('#errorModal').modal('show');
            }
        });
    });

    $('#modal OkBtn').on('click', function() {
        $('#errorModal').modal('hide');
    });
});
</script>
</body>
</html>