<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['status'] != 'admin') {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>

    <!-- Bootstrap CDN -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome CDN for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/css_dash_admin.css">
</head>
<body>

<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar">
        <h2><i class="fas fa-user-shield"></i> Menu Admin</h2>
        <a href="#" onclick="loadPage('dashboard')"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="#" onclick="loadPage('data_mahasiswa')"><i class="fas fa-user-graduate"></i> Data Mahasiswa</a>
        <a href="#" onclick="loadPage('data_dosen')"><i class="	fas fa-chalkboard-teacher"></i> Data Dosen</a>
        <a href="#" onclick="loadPage('matakuliah')"><i class="fas fa-book"></i> Mata Kuliah</a>
        <a href="#" onclick="loadPage('matakuliah_tawar')"><i class="fas fa-book-open"></i> Mata Kuliah Tawar</a>
        <a href="#" onclick="loadPage('cari_mahasiswa')"><i class="fas fa-search"></i> Cari Mahasiswa</a> <!-- Menu Cari Mahasiswa -->
        <a href="logout.php" class="text-danger"><i class="fas fa-sign-out-alt"></i> Log Out</a>
    </div>

    <!-- Content Area -->
    <div class="content flex-grow-1 p-4" id="content-area">
        <!-- Halaman default akan dimuat di sini -->
    </div>
</div>

<!-- Modal untuk Menampilkan Hasil Pencarian -->
<div class="modal fade" id="searchResultsModal" tabindex="-1" role="dialog" aria-labelledby="searchResultsModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="searchResultsModalLabel">Hasil Pencarian</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modal-body-content">
                <!-- Hasil pencarian akan dimasukkan di sini melalui AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- jQuery dan AJAX -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
// Fungsi untuk memuat halaman dinamis
function loadPage(page) {
    let pageUrl;

    // Tentukan halaman yang dimuat berdasarkan nama
    if (page.startsWith('edit_mahasiswa')) {
        const params = new URLSearchParams(page.split('&').slice(1).join('&'));
        const id = params.get('id');
        pageUrl = `edit_mahasiswa.php?id=${id}`;
    }
    else if (page.startsWith('edit_dosen')) {
        // Pastikan ekstraksi parameter NPP benar
        const npp = page.split('&')[1].split('=')[1];
        pageUrl = `edit_dosen.php?npp=${npp}`;
    }
    else {
        switch (page) {
            case 'dashboard':
                pageUrl = 'default_dashboard.php';
                break;
            case 'data_mahasiswa':
                pageUrl = 'data_mahasiswa.php';
                break;
            case 'tambah_mahasiswa':
                pageUrl = 'tambah_mahasiswa.php';
                break;
            case 'tambah_dosen':
                pageUrl = 'tambah_dosen.php';
                break;
            case 'data_dosen':
                pageUrl = 'data_dosen.php';
                break;
            case 'matakuliah':
                pageUrl = 'data_matkul.php';
                break;
            case 'matakuliah_tawar':
                pageUrl = 'data_matkul_tawar.php';
                break;
            default:
                pageUrl = 'default_dashboard.php';
        }
    }
    
    // Menggunakan AJAX untuk memuat halaman tanpa reload
    $.ajax({
        url: pageUrl,
        method: 'GET',
        success: function(response) {
            $('#content-area').html(response);
        },
        error: function(xhr, status, error) {
            console.error("Error loading page:", error);
            $('#content-area').html('<p class="text-danger">Error loading page. Please try again later.</p>');
        }
    });
}

// Inisialisasi dan event handler saat dokumen siap
$(document).ready(function() {
    // Memuat halaman dashboard secara default
    loadPage('dashboard');

    // Event handler untuk menu navigasi
    $('.nav-link').on('click', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        loadPage(page);
    });

    // Event handler untuk tombol logout
    $('#logout-btn').on('click', function(e) {
        e.preventDefault();
        // Konfirmasi logout
        if(confirm('Apakah Anda yakin ingin logout?')) {
            window.location.href = 'logout.php';
        }
    });
});
</script>

<!-- Bootstrap JS dan Font Awesome JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
