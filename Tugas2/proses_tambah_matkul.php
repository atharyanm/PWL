<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['status'] != 'admin') {
    header("Location: index.php");
    exit();
}

header('Content-Type: application/json');
require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $idmatkul = $_POST['idmatkul'];
    $namamatkul = $_POST['namamatkul'];
    $sks = $_POST['sks'];
    $jns = $_POST['jns'];
    $smt = $_POST['smt'];

    // Check for duplicate
    $checkQuery = "SELECT idmatkul FROM matkul WHERE idmatkul = ?";
    $stmt = $koneksi->prepare($checkQuery);
    $stmt->bind_param("s", $idmatkul);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Kode mata kuliah sudah ada!'
        ]);
        exit();
    }

    // Insert new record
    $query = "INSERT INTO matkul (idmatkul, namamatkul, sks, jns, smt) 
              VALUES (?, ?, ?, ?, ?)";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("sssss", $idmatkul, $namamatkul, $sks, $jns, $smt);

    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Data mata kuliah berhasil ditambahkan'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal menambahkan data: ' . $koneksi->error
        ]);
    }

    $stmt->close();
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method'
    ]);
}

$koneksi->close();
?>