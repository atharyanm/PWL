<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['status'] != 'admin') {
    header("Location: index.php");
    exit();
}
?>

<div class="container mt-4">
    <button type="button" class="btn btn-secondary mb-4" onclick="loadPage('data_matkul')">
        <i class="fas fa-arrow-left"></i> Kembali
    </button>
    <h2>Cari Mata Kuliah</h2>

    <div class="card mb-4">
        <div class="card-body">
            <form id="searchForm">
                <div class="form-row align-items-end">
                    <div class="form-group col-md-6">
                        <input type="text" class="form-control" id="searchValue" name="searchValue" 
                               placeholder="Masukkan kata kunci pencarian">
                    </div>
                    <div class="form-group col-md-4">
                        <select class="form-control" id="searchBy" name="searchBy">
                            <option value="idmatkul">Kode Mata Kuliah</option>
                            <option value="namamatkul">Nama Mata Kuliah</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i><span class="ml-1">Cari</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="searchResults" class="d-none">


<!-- Delete Confirmation Modal -->
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
                Apakah Anda yakin ingin menghapus mata kuliah ini?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Hapus</button>
            </div>
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
                Data mata kuliah berhasil dihapus!
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: 'search_matkul.php',
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                $('#searchResults').removeClass('d-none');
                let html = '';
                let no = 1;

                response.forEach(function(matkul) {
                    html += `
                        <tr>
                            <td>${no++}</td>
                            <td>${matkul.idmatkul}</td>
                            <td>${matkul.namamatkul}</td>
                            <td>${matkul.sks}</td>
                            <td>${matkul.jns}</td>
                            <td>${matkul.smt}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="#" onclick="loadPage('edit_matkul&kode=${matkul.idmatkul}')" 
                                       class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <button type="button" class="btn btn-danger btn-sm btn-hapus" 
                                            data-kode="${matkul.idmatkul}">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                });

                $('#resultBody').html(html);
                bindDeleteButtons();
            },
            error: function() {
                alert('Terjadi kesalahan saat mencari data');
            }
        });
    });

    function bindDeleteButtons() {
        $('.btn-hapus').on('click', function() {
            let kodeToDelete = $(this).data('kode');
            $('#confirmDeleteModal').modal('show');

            $('#confirmDeleteBtn').off('click').on('click', function() {
                $.ajax({
                    url: 'hapus_matkul.php',
                    method: 'POST',
                    data: { idmatkul: kodeToDelete },
                    dataType: 'json',
                    success: function(response) {
                        $('#confirmDeleteModal').modal('hide');
                        if (response.status === 'success') {
                            $('#successModal').modal('show');
                            $('#successModal').on('hidden.bs.modal', function () {
                                $('#searchForm').submit();
                            });
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function() {
                        alert('Terjadi kesalahan saat menghapus data');
                    }
                });
            });
        });
    }
});
</script>