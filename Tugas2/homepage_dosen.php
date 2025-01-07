<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['status'] != 'dosen') {
    header("Location: index.php");
    exit();
}

require 'koneksi.php';

$username = $_SESSION['username'];
$query = $koneksi->prepare("SELECT * FROM dosen WHERE username = ?");
$query->bind_param("s", $username);
$query->execute();
$result = $query->get_result();
$dosen = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage Dosen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
        }
        .card {
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            border: none;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0 text-muted">
                                <i class="fas fa-user-tie text-secondary me-2"></i>
                                Profil Dosen
                            </h4>
                            <a href="logout.php" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="border-start border-primary border-3 p-2">
                                    <small class="text-muted">NPP</small>
                                    <h6 class="mb-0"><?php echo htmlspecialchars($dosen['npp']); ?></h6>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="border-start border-success border-3 p-2">
                                    <small class="text-muted">Nama Dosen</small>
                                    <h6 class="mb-0"><?php echo htmlspecialchars($dosen['namadosen']); ?></h6>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="border-start border-info border-3 p-2">
                                    <small class="text-muted">Homebase</small>
                                    <h6 class="mb-0"><?php echo htmlspecialchars($dosen['homebase']); ?></h6>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted">Username</small>
                                <h6><?php echo htmlspecialchars($dosen['username']); ?></h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>