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
$searchBy = isset($_POST['searchBy']) ? $koneksi->real_escape_string($_POST['searchBy']) : 'npp';

// Validasi input
if (empty($searchTerm)) {
    echo '<div class="alert alert-warning">Masukkan kata kunci pencarian.</div>';
    exit;
}

// Tentukan query berdasarkan pilihan pencarian
$query = '';
switch ($searchBy) {
    case 'npp':
        $query = "SELECT * FROM dosen WHERE npp LIKE '%$searchTerm%'";
        break;
    case 'nama':
        $query = "SELECT * FROM dosen WHERE namadosen LIKE '%$searchTerm%'";
        break;
    case 'homebase':
        $query = "SELECT * FROM dosen WHERE homebase LIKE '%$searchTerm%'";
        break;
    default:
        $query = "SELECT * FROM dosen WHERE npp LIKE '%$searchTerm%'";
}

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
            <th>NPP</th>
            <th>Nama Dosen</th>
            <th>Homebase</th>
            <th>Username</th>
            <th>Password</th>
            <th>Aksi</th>
          </tr>';
    echo '</thead>';
    echo '<tbody>';

    $no = 1;
    while ($row = $result->fetch_assoc()) {
        echo '<tr id="row-' . $row['npp'] . '">';
        echo '<td>' . $no++ . '</td>';
        echo '<td>' . htmlspecialchars($row['npp']) . '</td>';
        echo '<td>' . htmlspecialchars($row['namadosen']) . '</td>';
        echo '<td>' . htmlspecialchars($row['homebase']) . '</td>';
        echo '<td>' . htmlspecialchars($row['username']) . '</td>';
        echo '<td>' . htmlspecialchars($row['password']) . '</td>';
        echo '<td>
                <div class="btn-group" role="group">
                    <a href="#" 
                       onclick="loadPage(\'edit_dosen&npp=' . $row['npp'] . '\')" 
                       class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <button class="btn btn-danger btn-sm btn-hapus" 
                            data-npp="' . $row['npp'] . '">
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
            Ditemukan ' . $result->num_rows . ' data dosen
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