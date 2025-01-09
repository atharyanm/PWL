<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['status'] != 'admin') {
    header("Location: index.php");
    exit();
}

require 'koneksi.php';

$searchTerm = isset($_POST['searchTerm']) ? $_POST['searchTerm'] : '';
$searchBy = isset($_POST['searchBy']) ? $_POST['searchBy'] : 'idmatkul';

$query = "SELECT * FROM matkul WHERE $searchBy LIKE ? ORDER BY idmatkul";
$stmt = $koneksi->prepare($query);
$searchPattern = "%$searchTerm%";
$stmt->bind_param("s", $searchPattern);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo '<div class="card">';
    echo '<div class="card-body">';
    echo '<div class="table-responsive">';
    echo '<table class="table table-bordered table-striped">';
    echo '<thead class="thead-dark">
            <tr>
                <th>No.</th>
                <th>Kode MK</th>
                <th>Nama Mata Kuliah</th>
                <th>SKS</th>
                <th>Jenis</th>
                <th>Semester</th>
                <th>Aksi</th>
            </tr>
          </thead>';
    echo '<tbody id="resultBody">';

    $no = 1;
    while ($row = $result->fetch_assoc()) {
        echo '<tr id="row-' . $row['idmatkul'] . '">';
        echo '<td>' . $no++ . '</td>';
        echo '<td>' . htmlspecialchars($row['idmatkul']) . '</td>';
        echo '<td>' . htmlspecialchars($row['namamatkul']) . '</td>';
        echo '<td>' . htmlspecialchars($row['sks']) . '</td>';
        echo '<td>' . htmlspecialchars($row['jns']) . '</td>';
        echo '<td>' . htmlspecialchars($row['smt']) . '</td>';
        echo '<td>
                <div class="btn-group" role="group">
                    <a href="#" onclick="loadPage(\'edit_matkul&kode=' . $row['idmatkul'] . '\')" 
                       class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <button type="button" 
                            class="btn btn-danger btn-sm btn-hapus" 
                            data-kode="' . $row['idmatkul'] . '">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </div>
              </td>';
        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';
    echo '<div class="mt-3 text-muted">
            <i class="fas fa-info-circle"></i> 
            Ditemukan ' . $result->num_rows . ' data mata kuliah
          </div>';
    echo '</div>';
    echo '</div>';
} else {
    echo '<div class="alert alert-info">
            <i class="fas fa-search"></i> Tidak ada data yang ditemukan.
          </div>';
}

$stmt->close();
$koneksi->close();
?>