<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['status'] != 'mhs') {
    header("Location: index.php");
    exit();
}

require 'koneksi.php';

$username = $_SESSION['username'];
$query = $koneksi->prepare("SELECT * FROM mahasiswa WHERE username = ?");
$query->bind_param("s", $username);
$query->execute();
$result = $query->get_result();
$mahasiswa = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f2f5;
        }
        .profile-container {
            max-width: 500px;
            margin: 50px auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .profile-header {
            background-color: #007bff;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .profile-body {
            padding: 25px;
        }
        .profile-img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        .profile-detail {
            display: flex;
            margin-bottom: 15px;
            align-items: center;
        }
        .profile-detail i {
            color: #007bff;
            margin-right: 15px;
            width: 25px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <div class="profile-header">
            <?php 
            $foto = !empty($mahasiswa['foto_profil']) ? $mahasiswa['foto_profil'] : 'default_profile.png';
            ?>
            <img src="uploads/<?php echo htmlspecialchars($foto); ?>" 
                 class="profile-img mb-3" 
                 alt="Foto Profil">
            <h4><?php echo htmlspecialchars($mahasiswa['nama']); ?></h4>
            <p class="mb-0"><?php echo htmlspecialchars($mahasiswa['nim']); ?></p>
        </div>
        
        <div class="profile-body">
            <div class="profile-detail">
                <i class="fas fa-user"></i>
                <div>
                    <small class="text-muted">Username</small>
                    <p class="mb-0"><?php echo htmlspecialchars($mahasiswa['username']); ?></p>
                </div>
            </div>
            
            <div class="profile-detail">
                <i class="fas fa-envelope"></i>
                <div>
                    <small class="text-muted">Email</small>
                    <p class="mb-0"><?php echo htmlspecialchars($mahasiswa['email']); ?></p>
                </div>
            </div>
            
            <hr>
            
            <div class="text-center">
                <a href="logout.php" class="btn btn-outline-danger">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>