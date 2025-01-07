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
    // $mahasiswa = checkLogin($koneksi, $username, $passw, 'mahasiswa');
    // $dosen = checkLogin($koneksi, $username, $passw, 'dosen');

    if ($admin) {
        $_SESSION['username'] = $username;
        $_SESSION['status'] = 'admin';
        header("Location: homepage_admin.php");
        exit();
    // } elseif ($mahasiswa) {
    //     $_SESSION['username'] = $username;
    //     $_SESSION['status'] = 'mhs';
    //     header("Location: homepage_mhs.php");
    //     exit();
    // } elseif ($dosen) {
    //     $_SESSION['username'] = $username;
    //     $_SESSION['status'] = 'dosen';
    //     header("Location: homepage_dosen.php");
    //     exit();
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
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-primary text-white text-center">
                        <h3>Login Sistem</h3>
                    </div>
                    <div class="card-body">
                        <?php 
                        if (!empty($error_message)) {
                            echo "<div class='alert alert-danger'>$error_message</div>";
                        }
                        ?>
                        <form method="post">
                            <div class="form-group">
                                <label>Username</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Password</label>
                                <input type="password" name="passw" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Login</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>