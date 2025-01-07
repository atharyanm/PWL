<?php
// Matikan error reporting di production
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pastikan tidak ada output sebelum JSON
ob_start();

session_start();

// Set header JSON paling awal
header('Content-Type: application/json');

if (!isset($_SESSION['username']) || $_SESSION['status'] != 'admin') {
    ob_clean(); // Bersihkan buffer
    echo json_encode([
        'status' => 'error', 
        'message' => 'Akses ditolak'
    ]);
    exit();
}

// Include database connection
require 'koneksi.php';

// Cek apakah koneksi berhasil
if (!$koneksi) {
    ob_clean(); // Bersihkan buffer
    echo json_encode([
        'status' => 'error', 
        'message' => 'Koneksi database gagal: ' . mysqli_connect_error()
    ]);
    exit();
}

// Handle deletion
if (isset($_GET['delete_npp'])) {
    try {
        $delete_npp = $koneksi->real_escape_string($_GET['delete_npp']);

        // Query langsung untuk menghindari kompleksitas
        $query = "DELETE FROM dosen WHERE npp = '$delete_npp'";
        
        if ($koneksi->query($query)) {
            ob_clean(); // Bersihkan buffer
            echo json_encode([
                'status' => 'success', 
                'message' => 'Data dosen berhasil dihapus.'
            ]);
        } else {
            ob_clean(); // Bersihkan buffer
            echo json_encode([
                'status' => 'error', 
                'message' => 'Gagal menghapus data: ' . $koneksi->error
            ]);
        }
    } catch (Exception $e) {
        ob_clean(); // Bersihkan buffer
        echo json_encode([
            'status' => 'error', 
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ]);
    }
} else {
    ob_clean(); // Bersihkan buffer
    echo json_encode([
        'status' => 'error', 
        'message' => 'Parameter tidak valid'
    ]);
}

$koneksi->close();
ob_end_flush();
exit();
?>