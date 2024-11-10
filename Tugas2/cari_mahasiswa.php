<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['status'] != 'admin') {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cari Mahasiswa</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h2>Cari Mahasiswa</h2>
    <form id="searchForm" class="form-inline mb-3">
        <input type="text" name="searchTerm" id="searchTerm" class="form-control mr-2" placeholder="Masukkan NIM atau Nama" required>
        <select name="searchBy" id="searchBy" class="form-control mr-2">
            <option value="nim">NIM</option>
            <option value="nama">Nama</option>
        </select>
        <button type="submit" class="btn btn-primary">Cari</button>
    </form>

    <div id="searchResults" class="mt-3">
        <!-- Tabel hasil pencarian akan dimasukkan di sini -->
    </div>
</div>

<!-- jQuery dan AJAX -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
    // Fungsi untuk menangani pencarian mahasiswa
    $(document).ready(function() {
        $('#searchForm').on('submit', function(event) {
            event.preventDefault(); // Mencegah form dari reload halaman

            let searchTerm = $('#searchTerm').val();
            let searchBy = $('#searchBy').val();

            // Menggunakan AJAX untuk melakukan pencarian
            $.ajax({
                url: 'proses_cari_mahasiswa.php',
                type: 'POST',
                data: { searchTerm: searchTerm, searchBy: searchBy },
                success: function(response) {
                    $('#searchResults').html(response); // Menampilkan hasil pencarian
                },
                error: function() {
                    $('#searchResults').html('<p class="text-danger">Terjadi kesalahan. Silakan coba lagi.</p>');
                }
            });
        });
    });
</script>
</body>
</html>
