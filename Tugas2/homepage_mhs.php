<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['status'] != 'mhs') {
    header("Location: index.php");
    exit();
}

require 'koneksi.php';

// Mengambil informasi mahasiswa dari database berdasarkan username
$username = $_SESSION['username'];
$query = "SELECT * FROM mahasiswa WHERE username = ?";
$stmt = $koneksi->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

// Cek jika data ditemukan
if ($result->num_rows > 0) {
    $mahasiswa = $result->fetch_assoc();
    $nama_mahasiswa = $mahasiswa['nama'];
    $foto_profil = $mahasiswa['foto_profil'] ?: 'default.jpg'; // gunakan default jika tidak ada foto
} else {
    // Set default jika tidak ada data mahasiswa
    $nama_mahasiswa = 'Nama Mahasiswa';
    $foto_profil = 'default.jpg';
}

$stmt->close();
$koneksi->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Mahasiswa</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/dashboard_mahasiswa.css">
</head>
<body>

<div class="header-bar">
    <h3>Dashboard Mahasiswa</h3>
    <div class="header-actions">
        <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<div class="container mt-4 dashboard-content">
    <div class="welcome-section">
        <img src="uploads/<?php echo htmlspecialchars($foto_profil); ?>" alt="Foto Profil" class="profile-photo">
        <h1>Selamat datang, <?php echo htmlspecialchars($nama_mahasiswa); ?>!</h1>
    </div>

    <div class="card-section">
        <div class="card">
            <h5>Notifikasi</h5>
            <p>Anda memiliki beberapa notifikasi baru.</p>
        </div>
        <div class="card">
            <h5>Aktivitas Terkini</h5>
            <p>Daftar tugas dan deadline Anda.</p>
        </div>
        <div class="card">
            <h5>Profil</h5>
            <p>Update informasi profil Anda.</p>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
