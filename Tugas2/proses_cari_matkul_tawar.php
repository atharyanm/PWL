<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['status'] != 'admin') {
    header("Location: index.php");
    exit();
}

require 'koneksi.php';

$searchTerm = $koneksi->real_escape_string($_POST['searchTerm']);
$searchBy = $koneksi->real_escape_string($_POST['searchBy']);

// Bangun query dinamis berdasarkan pilihan pencarian
switch($searchBy) {
    case 'namamatkul':
        $query = "SELECT mt.*, m.namamatkul, d.namadosen 
                  FROM matakuliah_tawar mt
                  INNER JOIN matkul m ON mt.idmatkul = m.idmatkul
                  INNER JOIN dosen d ON mt.npp = d.npp
                  WHERE m.namamatkul LIKE '%$searchTerm%'";
        break;
    case 'namadosen':
        $query = "SELECT mt.*, m.namamatkul, d.namadosen 
                  FROM matakuliah_tawar mt
                  INNER JOIN matkul m ON mt.idmatkul = m.idmatkul
                  INNER JOIN dosen d ON mt.npp = d.npp
                  WHERE d.namadosen LIKE '%$searchTerm%'";
        break;
    case 'kelompok':
        $query = "SELECT mt.*, m.namamatkul, d.namadosen 
                  FROM matakuliah_tawar mt
                  INNER JOIN matkul m ON mt.idmatkul = m.idmatkul
                  INNER JOIN dosen d ON mt.npp = d.npp
                  WHERE mt.kelompok LIKE '%$searchTerm%'";
        break;
    case 'hari':
        $query = "SELECT mt.*, m.namamatkul, d.namadosen 
                  FROM matakuliah_tawar mt
                  INNER JOIN matkul m ON mt.idmatkul = m.idmatkul
                  INNER JOIN dosen d ON mt.npp = d.npp
                  WHERE mt.hari LIKE '%$searchTerm%'";
        break;
    default:
        $query = "SELECT mt.*, m.namamatkul, d.namadosen 
                  FROM matakuliah_tawar mt
                  INNER JOIN matkul m ON mt.idmatkul = m.idmatkul
                  INNER JOIN dosen d ON mt.npp = d.npp";
}

$result = $koneksi->query($query);
$total_data = $result->num_rows;

if ($total_data > 0) {
    // Tampilkan total data ditemukan
    echo '<div class="alert alert-info">Total data ditemukan: ' . $total_data . '</div>';

    echo '<div class="table-responsive">';
    echo '<table class="table table-bordered table-striped">';
    echo '<thead class="thead-dark">';
    echo '<tr>
            <th>No.</th>
            <th>Mata Kuliah</th>
            <th>Dosen</th>
            <th>Kelompok</th>
            <th>Hari</th>
            <th>Jam</th>
            <th>Ruang</th>
            <th>Aksi</th>
          </tr>';
    echo '</thead><tbody>';
    
    $no = 1;
    while ($row = $result->fetch_assoc()) {
        echo '<tr>';
        echo '<td>'.$no++.'</td>';
        echo '<td>'.htmlspecialchars($row['namamatkul']).'</td>';
        echo '<td>'.htmlspecialchars($row['namadosen']).'</td>';
        echo '<td>'.htmlspecialchars($row['kelompok']).'</td>';
        echo '<td>'.htmlspecialchars($row['hari']).'</td>';
        echo '<td>'.htmlspecialchars($row['jam']).'</td>';
        echo '<td>'.htmlspecialchars($row['ruang']).'</td>';
        echo '<td>
                <div class="btn-group" role="group">
                    <a href="#" 
                       onclick="loadPage(\'edit_matkul_tawar&id='.$row['id_tawar'].'\')" 
                       class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <button type="button" 
                            class="btn btn-danger btn-sm btn-hapus" 
                            data-id="'.$row['id_tawar'].'">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </div>
              </td>';
        echo '</tr>';
    }
    
    echo '</tbody></table>';
    echo '</div>';
} else {
    echo '<div class="alert alert-info">Tidak ada data yang ditemukan.</div>';
}

$koneksi->close();