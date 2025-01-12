<?php
session_start();
require 'koneksi.php';

header('Content-Type: application/json');

// Cek apakah user adalah admin
if (!isset($_SESSION['username']) || $_SESSION['status'] != 'admin') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Akses ditolak'
    ]);
    exit();
}

// Ambil data dari form
$id_tawar = isset($_POST['id_tawar']) ? intval($_POST['id_tawar']) : 0;
$idmatkul = $_POST['idmatkul'] ?? '';
$npp = $_POST['npp'] ?? '';
$hari = $_POST['hari'] ?? '';
$jam = $_POST['jam'] ?? '';

// Gabungkan kelompok
$kodeKelompokBase = $_POST['kodeKelompokBase'] ?? '';
$noKelompok = $_POST['noKelompok'] ?? '';
$kelompok = $kodeKelompokBase . '.' . $noKelompok;

// Gabungkan ruang
$gedung = $_POST['gedung'] ?? '';
$lantai = $_POST['lantai'] ?? '';
$ruang_no = $_POST['ruang'] ?? '';
$ruang = $gedung . '.' . $lantai . '.' . $ruang_no;

// Validasi input
$errors = [];

if (empty($id_tawar)) $errors[] = "ID Tawar tidak valid";
if (empty($idmatkul)) $errors[] = "Mata Kuliah harus dipilih";
if (empty($npp)) $errors[] = "Dosen harus dipilih";
if (empty($hari)) $errors[] = "Hari harus dipilih";
if (empty($jam)) $errors[] = "Jam kuliah harus dipilih";
if (empty($kelompok)) $errors[] = "Kelompok tidak boleh kosong";
if (empty($ruang)) $errors[] = "Ruang kuliah harus dipilih";

// Jika ada error
if (!empty($errors)) {
    echo json_encode([
        'status' => 'error',
        'message' => implode(', ', $errors)
    ]);
    exit();
}

// Cek konflik jadwal (opsional: sesuaikan dengan kebutuhan)
$konflik_query = "SELECT * FROM matakuliah_tawar 
                  WHERE id_tawar != ? 
                  AND npp = ? 
                  AND hari = ? 
                  AND jam = ?";
$konflik_stmt = $koneksi->prepare($konflik_query);
$konflik_stmt->bind_param("isss", $id_tawar, $npp, $hari, $jam);
$konflik_stmt->execute();
$konflik_result = $konflik_stmt->get_result();

if ($konflik_result->num_rows > 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Jadwal bentrok dengan jadwal dosen yang sudah ada'
    ]);
    exit();
}

// Query update
$update_query = "UPDATE matakuliah_tawar 
                 SET idmatkul = ?, 
                     npp = ?, 
                     kelompok = ?, 
                     hari = ?, 
                     jam = ?, 
                     ruang = ?
                 WHERE id_tawar = ?";

try {
    // Persiapkan statement
    $stmt = $koneksi->prepare($update_query);
    
    // Bind parameter
    $stmt->bind_param("ssssssi", 
        $idmatkul, 
        $npp, 
        $kelompok, 
        $hari, 
        $jam, 
        $ruang, 
        $id_tawar
    );
    
    // Eksekusi query
    $result = $stmt->execute();
    
    // Cek keberhasilan update
    if ($result) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Data berhasil diupdate'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal mengupdate data: ' . $koneksi->error
        ]);
    }
    
    // Tutup statement
    $stmt->close();
} catch (Exception $e) {
    // Tangani error
    echo json_encode([
        'status' => 'error',
        'message' => 'Terjadi kesalahan: ' . $e->getMessage()
    ]);
}

// Tutup koneksi
$koneksi->close();