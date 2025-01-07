<?php
session_start();
require "koneksi.php";

// Cek koneksi database
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Redirect jika sudah login
if (isset($_SESSION['username'])) {
    switch ($_SESSION['status']) {
        case 'admin':
            header("Location: homepage_admin.php");
            exit();
        case 'mhs':
            header("Location: homepage_mhs.php");
            exit();
        case 'dosen':
            header("Location: homepage_dosen.php");
            exit();
        default:
            session_destroy();
            header("Location: index.php");
            exit();
    }
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $koneksi->real_escape_string($_POST['username']);
    $passw = $_POST['passw'];

    // Fungsi untuk cek login
    function checkLogin($koneksi, $username, $passw, $table) {
        $stmt = $koneksi->prepare("SELECT * FROM $table WHERE username = ? AND password = ?");
        $stmt->bind_param("ss", $username, $passw);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return false;
    }

    // Cek login untuk setiap tipe user
    $admin = checkLogin($koneksi, $username, $passw, 'admin');
    $mahasiswa = checkLogin($koneksi, $username, $passw, 'mahasiswa');
    $dosen = checkLogin($koneksi, $username, $passw, 'dosen');

    if ($admin) {
        $_SESSION['username'] = $username;
        $_SESSION['status'] = 'admin';
        header("Location: homepage_admin.php");
        exit();
    } elseif ($mahasiswa) {
        $_SESSION['username'] = $username;
        $_SESSION['status'] = 'mhs';
        header("Location: homepage_mhs.php");
        exit();
    } elseif ($dosen) {
        $_SESSION['username'] = $username;
        $_SESSION['status'] = 'dosen';
        header("Location: homepage_dosen.php");
        exit();
    } else {
        $error_message = "Login gagal. Periksa kembali username dan password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Sistem</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/index.css">

</head>
<body>
<div class="login-wrapper">
    <div class="login-info">
        <div class="login-info-icon">
            <i class="fas fa-graduation-cap"></i>
        </div>
        <div class="login-info-text">
            <h2>Sistem Akademik</h2>
            <p>A12.2022.06869</p>
        </div>
    </div>
    <div class="login-form">
        <div class="login-header mb-4">
            <h2>Login</h2>
            <p class="text-muted">Masukkan username dan password Anda</p>
        </div>
        
        <?php 
        if (!empty($error_message)) {
            echo "<div class='alert alert-danger'>$error_message</div>";
        }
        ?>
        
        <form method="post">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="passw" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
    </div>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>