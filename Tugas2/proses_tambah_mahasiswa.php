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
    $nim = $_POST['nim'];
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $foto_profil = null; // Inisialisasi variabel foto_profil

    // Validasi NIM unik
    $checkQuery = "SELECT * FROM mahasiswa WHERE nim = ?";
    $stmt = $koneksi->prepare($checkQuery);
    $stmt->bind_param("s", $nim);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'NIM sudah ada di database, silahkan gunakan NIM lain!']);
        exit();
    }

    // Cek apakah ada file yang di-upload
    if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] == UPLOAD_ERR_OK) {
        $foto_profil = $_FILES['foto_profil']['name'];
        $target_dir = "uploads/"; // Pastikan folder ini ada dan memiliki izin yang tepat
        $target_file = $target_dir . basename($foto_profil);

        // Cek apakah folder upload ada
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true); // Buat folder jika belum ada
        }

        // Upload file foto jika ada
        if (!move_uploaded_file($_FILES['foto_profil']['tmp_name'], $target_file)) {
            echo json_encode(['status' => 'error', 'message' => 'Error uploading file: ' . $_FILES['foto_profil']['error']]);
            exit();
        }
    }

    // Hash password sebelum disimpan untuk keamanan
    // $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert data ke database menggunakan prepared statement untuk keamanan
    $query = "INSERT INTO mahasiswa (nim, nama, username, password, email, foto_profil) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("ssssss", $nim, $nama, $username, $password, $email, $foto_profil);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Data berhasil ditambahkan!']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $stmt->error]);
    }

    exit(); // Keluar setelah memproses AJAX
}
?>