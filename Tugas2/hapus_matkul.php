<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['status'] != 'admin') {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

require 'koneksi.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['idmatkul'])) {
    try {
        $idmatkul = $_POST['idmatkul'];
        
        $query = "DELETE FROM matkul WHERE idmatkul = ?";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param("s", $idmatkul);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Data mata kuliah berhasil dihapus'
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Data mata kuliah tidak ditemukan'
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
        'message' => 'Invalid request method or missing idmatkul'
    ]);
}

$koneksi->close();
?>