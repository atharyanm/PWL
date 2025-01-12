<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['status'] != 'admin') {
    header("Location: index.php");
    exit();
}

require 'koneksi.php';

// Fungsi untuk mengambil total data
function getTotalData($koneksi, $table) {
    $query = $koneksi->query("SELECT COUNT(*) as total FROM $table");
    $data = $query->fetch_assoc();
    return $data['total'];
}
?>

<style>
.card:hover {
    transform: scale(1.03);
    cursor: pointer;
}
</style>
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard Admin</h1>
    </div>

    <div class="row">
        <!-- Total Mahasiswa -->
        <div class="col-xl-4 col-md-6 mb-4">
            <a href="#" onclick="loadPage('data_mahasiswa')" class="text-decoration-none">
                <div class="card border-left-primary shadow h-100 py-2" style="transition: transform .2s;">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                    Total Mahasiswa</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php echo getTotalData($koneksi, 'mahasiswa'); ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-secondary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Total Dosen -->
        <div class="col-xl-4 col-md-6 mb-4">
            <a href="#" onclick="loadPage('data_dosen')" class="text-decoration-none">
                <div class="card border-left-success shadow h-100 py-2" style="transition: transform .2s;">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                    Total Dosen</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php echo getTotalData($koneksi, 'dosen'); ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-chalkboard-teacher fa-2x text-secondary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Total Mata Kuliah -->
        <div class="col-xl-4 col-md-6 mb-4">
            <a href="#" onclick="loadPage('data_matkul')" class="text-decoration-none">
                <div class="card border-left-secondary shadow h-100 py-2" style="transition: transform .2s;">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                    Total Mata Kuliah</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php echo getTotalData($koneksi, 'matkul'); ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-book fa-2x text-secondary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Total Mata Kuliah Tawar-->
        <div class="col-xl-4 col-md-6 mb-4">
            <a href="#" onclick="loadPage('data_matkul_tawar')" class="text-decoration-none">
                <div class="card border-left-secondary shadow h-100 py-2" style="transition: transform .2s;">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                    Total Mata Kuliah Tawar</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    <?php echo getTotalData($koneksi, 'matakuliah_tawar'); ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-book-open fa-2x text-secondary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Total Admin -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                Total Admin</div>
                            <div class="h5 mb-0 font-weight-bold text-info">
                                <?php echo getTotalData($koneksi, 'admin'); ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-shield fa-2x text-secondary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Informasi Sistem -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-secondary">Informasi Sistem</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <p><strong>Versi Sistem:</strong> 1.0.0</p>
                            <p><strong>Terakhir Diperbarui:</strong> 10 Januari 2025</p>
                        </div>
                        <div class="col-6">
                            <p><strong>Username Admin:</strong> <?php echo $_SESSION['username']; ?></p>
                            <p><strong>Status Sistem:</strong> <span class="text-success">Aktif</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Animasi counter
    $('.h5').each(function() {
        $(this).prop('Counter', 0).animate({
            Counter: $(this).text()
        }, {
            duration: 1500,
            easing: 'swing',
            step: function(now) {
                $(this).text(Math.ceil(now));
            }
        });
    });
});
</script>