<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['status'] != 'admin') {
    header("Location: index.php");
    exit();
}

header('Content-Type: application/json');
require 'koneksi.php';

$response = [
    'status' => 'error',
    'message' => 'Terjadi kesalahan'
];

try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $idmatkul = $_POST['idmatkul'];
        $namamatkul = $_POST['namamatkul'];
        $sks = $_POST['sks'];
        $jns = $_POST['jns'];
        $smt = $_POST['smt'];

        // Update query
        $query = "UPDATE matkul SET 
                 namamatkul = ?,
                 sks = ?,
                 jns = ?,
                 smt = ?
                 WHERE idmatkul = ?";

        $stmt = $koneksi->prepare($query);
        $stmt->bind_param("sssss", 
            $namamatkul,
            $sks,
            $jns,
            $smt,
            $idmatkul
        );

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $response['status'] = 'success';
                $response['message'] = 'Data mata kuliah berhasil diperbarui';
            } else {
                $response['message'] = 'Tidak ada perubahan data';
            }
        } else {
            $response['message'] = 'Gagal memperbarui data: ' . $stmt->error;
        }

        $stmt->close();
    } else {
        $response['message'] = 'Invalid request method';
    }
} catch (Exception $e) {
    $response['message'] = 'Terjadi kesalahan: ' . $e->getMessage();
}

$koneksi->close();
echo json_encode($response);
?>