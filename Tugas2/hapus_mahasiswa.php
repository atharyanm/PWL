<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['status'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Include database connection
require 'koneksi.php';

// Cek apakah koneksi berhasil
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Handle deletion
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']); // Menggunakan intval untuk menghindari injeksi SQL

    $result = $koneksi->query("SELECT foto_profil FROM mahasiswa WHERE id = '$delete_id'");
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $foto_profil = $row['foto_profil'];

        // Hapus data mahasiswa dari database
        if ($koneksi->query("DELETE FROM mahasiswa WHERE id = '$delete_id'")) {
            // Hapus file foto jika ada
            if ($foto_profil) {
                $file_path = 'uploads/' . $foto_profil;
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
            echo json_encode(['status' => 'success', 'message' => 'Data mahasiswa berhasil dihapus.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus data mahasiswa.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Data mahasiswa tidak ditemukan.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'ID mahasiswa tidak valid.']);
}

$koneksi->close();
?>
