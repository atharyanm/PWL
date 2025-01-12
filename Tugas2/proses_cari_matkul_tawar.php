<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['status'] != 'admin') {
    header("Location: index.php");
    exit();
}

require 'koneksi.php';

$searchTerm = isset($_POST['searchTerm']) ? $_POST['searchTerm'] : '';
$searchBy = isset($_POST['searchBy']) ? $_POST['searchBy'] : 'm.namamatkul';

$query = "SELECT 
            mt.id_tawar,
            m.namamatkul,
            d.namadosen,
            mt.kelompok,
            mt.hari,
            mt.jam,
            mt.ruang,
            m.sks 
          FROM matakuliah_tawar mt
          INNER JOIN matkul m ON mt.idmatkul = m.idmatkul
          INNER JOIN dosen d ON mt.npp = d.npp
          WHERE $searchBy LIKE ?
          ORDER BY mt.hari, mt.jam";

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
                <th>Mata Kuliah</th>
                <th>Dosen</th>
                <th>Kelompok</th>
                <th>Hari</th>
                <th>Jam</th>
                <th>Ruang</th>
                <th>SKS</th>
                <th>Aksi</th>
            </tr>
          </thead>';
    echo '<tbody id="resultBody">';

    $no = 1;
    while ($row = $result->fetch_assoc()) {
        echo '<tr id="row-' . $row['id_tawar'] . '">';
        echo '<td>' . $no++ . '</td>';
        echo '<td>' . htmlspecialchars($row['namamatkul']) . '</td>';
        echo '<td>' . htmlspecialchars($row['namadosen']) . '</td>';
        echo '<td>' . htmlspecialchars($row['kelompok']) . '</td>';
        echo '<td>' . htmlspecialchars($row['hari']) . '</td>';
        echo '<td>' . htmlspecialchars($row['jam']) . '</td>';
        echo '<td>' . htmlspecialchars($row['ruang']) . '</td>';
        echo '<td>' . htmlspecialchars($row['sks']) . '</td>';
        echo '<td>
                <div class="btn-group" role="group">
                    <a href="#" onclick="loadPage(\'edit_matkul_tawar&id=' . $row['id_tawar'] . '\')" 
                       class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <button type="button" 
                            class="btn btn-danger btn-sm btn-hapus" 
                            data-id="' . $row['id_tawar'] . '">
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
            Ditemukan ' . $result->num_rows . ' jadwal mata kuliah
          </div>';
    echo '</div>';
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