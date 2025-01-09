<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['status'] != 'admin') {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

require 'koneksi.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['npp'])) {
    try {
        $npp = $_POST['npp'];
        
        // Use prepared statement
        $query = "DELETE FROM dosen WHERE npp = ?";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param("s", $npp);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Data dosen berhasil dihapus'
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Data dosen tidak ditemukan'
                ]);
            }
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Gagal menghapus data: ' . $stmt->error
            ]);
        }
        
        $stmt->close();
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method or missing NPP'
    ]);
}

$koneksi->close();
?>