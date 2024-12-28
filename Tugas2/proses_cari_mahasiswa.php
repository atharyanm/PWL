<?php
session_start();
require 'koneksi.php';

// Cek login dan hak akses
if (!isset($_SESSION['username']) || $_SESSION['status'] != 'admin') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Akses ditolak'
    ]);
    exit;
}

// Ambil parameter pencarian
$searchTerm = isset($_POST['searchTerm']) ? $koneksi->real_escape_string($_POST['searchTerm']) : '';
$searchBy = isset($_POST['searchBy']) ? $koneksi->real_escape_string($_POST['searchBy']) : 'nim';

// Validasi input
if (empty($searchTerm)) {
    echo '<div class="alert alert-warning">Masukkan kata kunci pencarian.</div>';
    exit;
}

// Tentukan query berdasarkan pilihan pencarian
$query = $searchBy === 'nim' 
    ? "SELECT * FROM mahasiswa WHERE nim LIKE '%$searchTerm%'" 
    : "SELECT * FROM mahasiswa WHERE nama LIKE '%$searchTerm%'";

// Eksekusi query
$result = $koneksi->query($query);

// Periksa hasil
if ($result->num_rows > 0) {
    echo '<div class="card mt-3">';
    echo '<div class="card-header bg-info text-white">
            <i class="fas fa-search"></i> Hasil Pencarian
          </div>';
    echo '<div class="card-body">';
    echo '<table class="table table-bordered table-striped table-hover">';
    echo '<thead class="thead-dark">';
    echo '<tr>
            <th>No</th>
            <th>NIM</th>
            <th>Foto Profil</th>
            <th>Nama</th>
            <th>Username</th>
            <th>Password</th>
            <th>Email</th>
            <th>Aksi</th>
          </tr>';
    echo '</thead>';
    echo '<tbody>';

    $no = 1;
    while ($row = $result->fetch_assoc()) {
        echo '<tr id="row-' . $row['id'] . '">';
        echo '<td>' . $no++ . '</td>';
        echo '<td>' . htmlspecialchars($row['nim']) . '</td>';
        echo '<td>';
        if ($row['foto_profil']) {
            echo '<img src="uploads/' . htmlspecialchars($row['foto_profil']) . '" 
                     alt="Foto Profil" 
                     class="img-thumbnail" 
                     style="max-width: 100px; max-height: 100px;">';
        } else {
            echo '<span class="badge badge-secondary">Tidak ada foto</span>';
        }
        echo '</td>';
        echo '<td>' . htmlspecialchars($row['nama']) . '</td>';
        echo '<td>' . htmlspecialchars($row['username']) . '</td>';
        echo '<td>' . htmlspecialchars($row['password']) . '</td>';
        echo '<td>' . htmlspecialchars($row['email']) . '</td>';
        echo '<td>
                <div class="btn-group" role="group">
                    <a href="#" 
                       onclick="loadPage(\'edit_mahasiswa&id=' . $row['id'] . '\')" 
                       class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <button class="btn btn-danger btn-sm btn-hapus" 
                            data-id="' . $row['id'] . '">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </div>
              </td>';
        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';
    
    // Tambahkan informasi jumlah hasil
    echo '<div class="mt-3 text-muted">
            <i class="fas fa-info-circle"></i> 
            Ditemukan ' . $result->num_rows . ' data mahasiswa
          </div>';
    
    echo '</div>';
    echo '</div>';

    // Script untuk handling aksi
    echo '<script>
        $(document).ready(function() {
            // Tooltip bootstrap
            $("[data-toggle=\'tooltip\']").tooltip();
        });
    </script>';
} else {
    echo '<div class="alert alert-info">
            <i class="fas fa-search"></i> Tidak ada data yang ditemukan.
          </div>';
}

$koneksi->close();
?>