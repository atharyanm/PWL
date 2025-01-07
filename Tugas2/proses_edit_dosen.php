<?php
// Mulai session
session_start();

// Cek apakah user sudah login sebagai admin
if (!isset($_SESSION['username']) || $_SESSION['status'] != 'admin') {
    $response = [
        'status' => 'error',
        'message' => 'Anda tidak memiliki akses'
    ];
    echo json_encode($response);
    exit();
}

// Include koneksi database
require 'koneksi.php';

// Siapkan response default
$response = [
    'status' => 'error',
    'message' => 'Terjadi kesalahan'
];

// Pastikan request adalah POST
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    $response['message'] = 'Metode request tidak valid';
    echo json_encode($response);
    exit();
}

// Ambil data dari form
$npp = isset($_POST['npp']) ? trim($_POST['npp']) : '';
$nama_dosen = isset($_POST['nama_dosen']) ? trim($_POST['nama_dosen']) : '';
$homebase = isset($_POST['homebase']) ? trim($_POST['homebase']) : '';
$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

// Validasi input
if (empty($npp) || empty($nama_dosen) || empty($homebase) || empty($username)) {
    $response['message'] = 'Semua field wajib diisi kecuali password';
    echo json_encode($response);
    exit();
}

// Validasi panjang username
if (strlen($username) < 4) {
    $response['message'] = 'Username minimal 4 karakter';
    echo json_encode($response);
    exit();
}

// Cek keunikan username
$cek_username = $koneksi->prepare("SELECT * FROM dosen WHERE username = ? AND npp != ?");
$cek_username->bind_param("ss", $username, $npp);
$cek_username->execute();
$result = $cek_username->get_result();

if ($result->num_rows > 0) {
    $response['message'] = 'Username sudah digunakan';
    echo json_encode($response);
    exit();
}

// Siapkan query
try {
    // Jika password diisi, berarti ingin diubah
    if (!empty($password)) {
        // Validasi panjang password
        if (strlen($password) < 6) {
            $response['message'] = 'Password minimal 6 karakter';
            echo json_encode($response);
            exit();
        }

        // Query update dengan password
        $query = "UPDATE dosen 
                  SET namadosen = ?, 
                      homebase = ?, 
                      username = ?, 
                      password = ? 
                  WHERE npp = ?";
        
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param("sssss", 
            $nama_dosen, 
            $homebase, 
            $username, 
            $password, 
            $npp
        );
    } else {
        // Query update tanpa password
        $query = "UPDATE dosen 
                  SET namadosen = ?, 
                      homebase = ?, 
                      username = ? 
                  WHERE npp = ?";
        
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param("ssss", 
            $nama_dosen, 
            $homebase, 
            $username, 
            $npp
        );
    }

    // Eksekusi query
    if ($stmt->execute()) {
        // Cek apakah ada baris yang terpengaruh
        if ($stmt->affected_rows > 0) {
            $response['status'] = 'success';
            $response['message'] = 'Data dosen berhasil diperbarui';
        } else {
            $response['message'] = 'Tidak ada perubahan data';
        }
    } else {
        $response['message'] = 'Gagal memperbarui data: ' . $stmt->error;
    }

    // Tutup statement
    $stmt->close();
} catch (Exception $e) {
    // Tangkap error yang mungkin terjadi
    $response['message'] = 'Terjadi kesalahan: ' . $e->getMessage();
}

// Tutup koneksi
$koneksi->close();

// Kembalikan response dalam format JSON
echo json_encode($response);
?>