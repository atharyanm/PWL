<?php
require 'koneksi.php';

$searchTerm = isset($_POST['searchTerm']) ? $_POST['searchTerm'] : '';
$searchBy = isset($_POST['searchBy']) ? $_POST['searchBy'] : 'nim';

if (!empty($searchTerm)) {
    // Query pencarian berdasarkan NIM atau Nama
    if ($searchBy === 'nim') {
        $query = "SELECT * FROM mahasiswa WHERE nim LIKE '%$searchTerm%'";
    } else {
        $query = "SELECT * FROM mahasiswa WHERE nama LIKE '%$searchTerm%'";
    }

    $result = $koneksi->query($query);

    if ($result && $result->num_rows > 0) {
        echo '<table class="table table-bordered table-striped">';
        echo '<thead class="thead-dark">
                <tr>
                    <th>No.</th>
                    <th>NIM</th>
                    <th>Foto Profil</th>
                    <th>Nama</th>
                    <th>Username</th>
                </tr>
              </thead>';
        echo '<tbody>';

        $no = 1;
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $no++ . '</td>';
            echo '<td>' . htmlspecialchars($row['nim']) . '</td>';
            echo '<td>';
            if ($row['foto_profil']) {
                echo '<img src="uploads/' . htmlspecialchars($row['foto_profil']) . '" alt="Foto Profil" width="50" height="50">';
            } else {
                echo 'No Image';
            }
            echo '</td>';
            echo '<td>' . htmlspecialchars($row['nama']) . '</td>';
            echo '<td>' . htmlspecialchars($row['username']) . '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
    } else {
        echo '<p class="text-danger">Data tidak ditemukan.</p>';
    }
} else {
    echo '<p class="text-danger">Masukkan kata kunci pencarian.</p>';
}

$koneksi->close();
?>
