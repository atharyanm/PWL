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
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Susun NPP lengkap
    $npp = "0686.11." . $tahun_npp . "." . $nomor_urut;

    // Validasi input dasar
    if (empty($nama_dosen) || empty($nomor_urut) || empty($username) || empty($password)) {
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

    // Validasi panjang username
    if (strlen($username) < 4) {
        echo json_encode([
            'status' => 'error', 
            'message' => 'Username minimal 4 karakter!'
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

    // Cek username duplikat
    $cek_username = $koneksi->prepare("SELECT * FROM dosen WHERE username = ?");
    $cek_username->bind_param("s", $username);
    $cek_username->execute();
    $result_username = $cek_username->get_result();

    if ($result_username->num_rows > 0) {
        echo json_encode([
            'status' => 'error', 
            'message' => 'Username sudah digunakan!'
        ]);
        exit();
    }

    // Query tambah dosen
    $query = $koneksi->prepare("INSERT INTO dosen (npp, namadosen, homebase, username, password) VALUES (?, ?, ?, ?, ?)");
    $query->bind_param("sssss", $npp, $nama_dosen, $homebase, $username, $password);

    if ($query->execute()) {
        echo json_encode([
            'status' => 'success', 
            'message' => 'Data dosen berhasil ditambahkan!',
            'npp' => $npp,
            'nama_dosen' => $nama_dosen,
            'homebase' => $homebase,
            'username' => $username
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