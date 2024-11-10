<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['status'] != 'dosen') {
    header("Location: index.php"); 
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage Dosen</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Selamat datang, <?php echo $_SESSION['username']; ?>!</h1>
        <p>Anda login sebagai Dosen</p>
        <a href="logout.php" class="btn btn-danger">Logout</a> <!-- Tombol Logout -->
    </div>
</body>
</html>
