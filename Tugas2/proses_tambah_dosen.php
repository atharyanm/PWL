<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['status'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Include database connection
require 'koneksi.php';

// Proses penambahan data jika permintaan POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Tangkap data form
    $tahun_npp = $_POST['tahun_npp'];
    $nomor_urut = $_POST['nomor_urut'];
    $nama_dosen = trim($_POST['nama_dosen']);
    $homebase = $_POST['homebase'];

    // Susun NPP lengkap
    $npp = "0686.11." . $tahun_npp . "." . $nomor_urut;

    // Validasi input dasar
    if (empty($nama_dosen) || empty($nomor_urut)) {
        echo json_encode([
            'status' => 'error', 
            'message' => 'Semua field harus diisi!'
        ]);
        exit();
    }

    // Validasi nomor urut hanya angka
    if (!is_numeric($nomor_urut)) {
        echo json_encode([
            'status' => 'error', 
            'message' => 'Nomor urut harus berupa angka!'
        ]);
        exit();
    }

    // Cek NPP duplikat
    $cek_npp = $koneksi->prepare("SELECT * FROM dosen WHERE npp = ?");
    $cek_npp->bind_param("s", $npp);
    $cek_npp->execute();
    $result = $cek_npp->get_result();

    if ($result->num_rows > 0) {
        echo json_encode([
            'status' => 'error', 
            'message' => 'NPP sudah ada!'
        ]);
        exit();
    }

    // Query tambah dosen
    $query = $koneksi->prepare("INSERT INTO dosen (npp, namadosen, homebase) VALUES (?, ?, ?)");
    $query->bind_param("sss", $npp, $nama_dosen, $homebase);

    if ($query->execute()) {
        echo json_encode([
            'status' => 'success', 
            'message' => 'Data dosen berhasil ditambahkan!',
            'npp' => $npp,
            'nama_dosen' => $nama_dosen,
            'homebase' => $homebase
        ]);
    } else {
        echo json_encode([
            'status' => 'error', 
            'message' => 'Gagal menambahkan data dosen: ' . $query->error
        ]);
    }

    exit(); // Keluar setelah memproses
}
?>