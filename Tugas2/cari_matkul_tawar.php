<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['status'] != 'admin') {
    header("Location: index.php");
    exit();
}
?>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-12">
            <a href="#" onclick="loadPage('data_matkul_tawar')" class="btn btn-secondary mb-4">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <h2 class="mb-4">
                <i class="fas fa-search"></i> Pencarian Mata Kuliah Tawar
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
                                   placeholder="Masukkan kata kunci..." 
                                   required>
                        </div>
                        <div class="col-md-5 mb-2">
                            <select name="searchBy" id="searchBy" class="form-control">
                                <option value="m.namamatkul">Cari berdasarkan Nama MK</option>
                                <option value="d.namadosen">Cari berdasarkan Nama Dosen</option>
                                <option value="mt.hari">Cari berdasarkan Hari</option>
                                <option value="mt.ruang">Cari berdasarkan Ruang</option>
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

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus jadwal mata kuliah ini?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Hapus</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Sukses -->
<div class="modal fade" id="successDeleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Sukses</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Data jadwal mata kuliah berhasil dihapus!
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    let idToDelete;
    
    // Search functionality
    $('#searchForm').on('submit', function(event) {
        event.preventDefault();
        let searchTerm = $('#searchTerm').val();
        let searchBy = $('#searchBy').val();

        $.ajax({
            url: 'proses_cari_matkul_tawar.php',
            type: 'POST',
            data: { 
                searchTerm: searchTerm, 
                searchBy: searchBy 
            },
            beforeSend: function() {
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
                $('#errorMessage').text('Terjadi kesalahan saat mencari data.');
                $('#errorModal').modal('show');
            }
        });
    });

    // Delete handling
    $(document).on('click', '.btn-hapus', function() {
        idToDelete = $(this).data('id');
        $('#confirmDeleteModal').modal('show');
    });

    $('#confirmDeleteBtn').on('click', function() {
        $.ajax({
            url: 'hapus_matkul_tawar.php',
            type: 'POST',
            data: { id_tawar: idToDelete },
            dataType: 'json',
            success: function(response) {
                $('#confirmDeleteModal').modal('hide');
                if (response.status === 'success') {
                    setTimeout(function() {
                        $('#successDeleteModal').modal('show');
                    }, 500);
                } else {
                    $('#errorMessage').text(response.message);
                    $('#errorModal').modal('show');
                }
            },
            error: function() {
                $('#confirmDeleteModal').modal('hide');
                $('#errorMessage').text('Terjadi kesalahan saat menghapus data.');
                $('#errorModal').modal('show');
            }
        });
    });

    // Refresh after successful delete
    $('#successDeleteModal').on('hidden.bs.modal', function() {
        $('#searchForm').submit();
    });
});
</script>