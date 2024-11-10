<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['status'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Include database connection
require 'koneksi.php';

// Cek apakah ID mahasiswa ada
if (!isset($_GET['id'])) {
    die("ID mahasiswa tidak ditemukan.");
}

$id = $_GET['id'];
$result = $koneksi->query("SELECT * FROM mahasiswa WHERE id = '$id'");

if ($result->num_rows === 0) {
    die("Data mahasiswa tidak ditemukan.");
}

$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Mahasiswa</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h2>Edit Mahasiswa</h2>
    <form id="editMahasiswaForm" action="proses_edit_mahasiswa.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
        <div class="form-group">
            <label for="nim">NIM:</label>
            <input type="text" class="form-control" id="nim" name="nim" value="<?php echo htmlspecialchars($row['nim']); ?>" maxlength="14" required>
            <small id="nimFeedback" class="form-text text-danger" style="display:none;"></small>
        </div>
        <div class="form-group">
            <label for="nama">Nama:</label>
            <input type="text" class="form-control" id="nama" name="nama" value="<?php echo htmlspecialchars($row['nama']); ?>" required>
        </div>
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($row['username']); ?>" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Kosongi jika tidak ingin diganti...">
        </div>
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" required>
        </div>
        <div class="form-group">
            <label for="foto_profil">Foto Profil:</label>
            <?php if ($row['foto_profil']) { ?>
                <div>
                    <img src="uploads/<?php echo htmlspecialchars($row['foto_profil']); ?>" alt="Foto Profil" width="100" height="100">
                </div>
                <div>
                    <input type="checkbox" name="delete_foto" id="delete_foto" value="1">
                    <label for="delete_foto">Hapus Foto Profil</label>
                </div>
            <?php } else { ?>
                <span>No Image</span>
            <?php } ?>
            <input type="file" class="form-control-file" id="foto_profil" name="foto_profil" accept="image/*">
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    $('#nim').on('blur', function() {
        const nim = $(this).val();
        const nimLength = nim.length;

        // Validasi panjang NIM
        if (nimLength < 14) {
            $('#nimFeedback').text("NIM harus terdiri dari 14 karakter.").show();
            $(this).focus(); // Mengarahkan kursor kembali ke field NIM
        } else if (nimLength > 14) {
            $('#nimFeedback').text("NIM tidak boleh lebih dari 14 karakter.").show();
        } else {
            $('#nimFeedback').hide();
        }
    });

    $('#editMahasiswaForm').on ('submit', function(event) {
        const nim = $('#nim').val();
        const nimLength = nim.length;

        if (nimLength !== 14) {
            event.preventDefault();
            $('#nimFeedback').text("Isian NIM harus terdiri dari 14 karakter.").show();
            $('#nim').focus(); // Mengarahkan kursor kembali ke field NIM
        }
    });
});
</script>
</body>
</html>