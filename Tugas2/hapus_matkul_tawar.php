<?php
session_start();
require 'koneksi.php';

// Set header untuk respons JSON
header('Content-Type: application/json');

// Cek otentikasi
if (!isset($_SESSION['username']) || $_SESSION['status'] != 'admin') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Akses ditolak'
    ]);
    exit();
}

// Validasi input
$id_tawar = isset($_POST['id_tawar']) ? intval($_POST['id_tawar']) : 0;

if ($id_tawar <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'ID tidak valid'
    ]);
    exit();
}

try {
    // Mulai transaksi
    $koneksi->begin_transaction();

    // Cek apakah data exists
    $cek_query = "SELECT * FROM matakuliah_tawar WHERE id_tawar = ?";
    $cek_stmt = $koneksi->prepare($cek_query);
    $cek_stmt->bind_param("i", $id_tawar);
    $cek_stmt->execute();
    $result = $cek_stmt->get_result();

    if ($result->num_rows == 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Data tidak ditemukan'
        ]);
        exit();
    }

    // Query hapus
    $hapus_query = "DELETE FROM matakuliah_tawar WHERE id_tawar = ?";
    $hapus_stmt = $koneksi->prepare($hapus_query);
    $hapus_stmt->bind_param("i", $id_tawar);
    $hapus_result = $hapus_stmt->execute();

    if ($hapus_result) {
        // Commit transaksi
        $koneksi->commit();
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Data berhasil dihapus'
        ]);
    } else {
        // Rollback transaksi
        $koneksi->rollback();
        
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal menghapus data'
        ]);
    }

    // Tutup statement
    $cek_stmt->close();
    $hapus_stmt->close();

} catch (Exception $e) {
    // Rollback transaksi jika terjadi error
    $koneksi->rollback();
    
    echo json_encode([
        'status' => 'error',
        'message' => 'Terjadi kesalahan: ' . $e->getMessage()
    ]);
}

// Tutup koneksi
$koneksi->close();