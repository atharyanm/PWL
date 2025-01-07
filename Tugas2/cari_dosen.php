<?php
session_start();
// Cek login dan hak akses
if (!isset($_SESSION['username']) || $_SESSION['status'] != 'admin') {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cari Dosen</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        .table-hover tbody tr:hover {
            background-color: #f5f5f5;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-12">
            <h2 class="mb-4">
                <i class="fas fa-search"></i> Pencarian Dosen
            </h2>
            
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-filter"></i> Filter Pencarian
                </div>
                <div class="card-body">
                    <form id="searchForm" class="form-row align-items-center">
                        <div class="col-md-4 mb-2">
                            <input type="text" name="searchTerm" id="searchTerm" 
                                   class="form-control" 
                                   placeholder="NPP atau Nama..." 
                                   required>
                        </div>
                        <div class="col-md-5 mb-2">
                            <select name="searchBy" id="searchBy" class="form-control">
                                <option value="npp">Cari berdasarkan NPP</option>
                                <option value="nama">Cari berdasarkan Nama</option>
                                <option value="homebase">Cari berdasarkan Homebase</option>
                            </select>
                        </div>
                        <div class="col-md-2 mb-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> Cari
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Hasil Pencarian -->
            <div id="searchResults" class="mt-3">
                <!-- Tabel hasil pencarian akan dimuat di sini -->
            </div>
        </div>
    </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // Fungsi Pencarian
    $('#searchForm').on('submit', function(event) {
        event.preventDefault();

        let searchTerm = $('#searchTerm').val();
        let searchBy = $('#searchBy').val();

        $.ajax({
            url: 'proses_cari_dosen.php',
            type: 'POST',
            data: { 
                searchTerm: searchTerm, 
                searchBy: searchBy 
            },
            beforeSend: function() {
                // Tampilkan loading
                $('#searchResults').html(`
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                `);
            },
            success: function(response) {
                $('#searchResults').html(response);
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Terjadi kesalahan saat mencari data!'
                });
            }
        });
    });

    // Delegasi Event untuk Tombol Hapus
    $(document).on('click', '.btn-hapus', function() {
        let npp = $(this).data('npp');
        
        Swal.fire({
            title: 'Konfirmasi Hapus',
            text: 'Apakah Anda yakin ingin menghapus data dosen ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: 'hapus_dosen.php',
                    type: 'POST',
                    data: { npp: npp },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            // Hapus baris dari tabel
                            $('#row-' + npp).fadeOut(500, function() {
                                $(this).remove();
                            });
                            
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: response.message
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Terjadi kesalahan saat menghapus data!'
                        });
                    }
                });
            }
        });
    });
});
</script>
</body>
</html>