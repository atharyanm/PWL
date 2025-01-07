<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['status'] != 'admin') {
    header("Content-Type: application/json");
    echo json_encode([
        'status' => 'error', 
        'message' => 'Akses ditolak'
    ]);
    exit();
}

// Include database connection
require 'koneksi.php';

// Set header JSON
header('Content-Type: application/json');

// Tangkap data dari form
$npp = $_POST['npp'];
$nama_dosen = trim($_POST['nama_dosen']);
$homebase = $_POST['homebase'];

// Validasi input
if (empty($nama_dosen)) {
    echo json_encode([
        'status' => 'error', 
        'message' => 'Nama dosen tidak boleh kosong'
    ]);
    exit();
}

// Siapkan query update
$query = $koneksi->prepare("UPDATE dosen SET namadosen = ?, homebase = ? WHERE npp = ?");
$query->bind_param("sss", $nama_dosen, $homebase, $npp);

try {
    if ($query->execute()) {
        echo json_encode([
            'status' => 'success', 
            'message' => 'Data dosen berhasil diubah',
            'data' => [
                'npp' => $npp,
                'nama_dosen' => $nama_dosen,
                'homebase' => $homebase
            ]
        ]);
    } else {
        echo json_encode([
            'status' => 'error', 
            'message' => 'Gagal mengubah data: ' . $query->error
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error', 
        'message' => 'Terjadi kesalahan: ' . $e->getMessage()
    ]);
}

$query->close();
$koneksi->close();
exit();
?>