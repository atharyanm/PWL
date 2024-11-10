ini file edit_proses_mahasiswa
<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['status'] != 'admin') {
    header("Location: index.php");
    exit();
}

require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $nim = $_POST['nim'];
    $nama = $_POST['nama'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];


    // Ambil data password lama dari database
    $result = $koneksi->query("SELECT password FROM mahasiswa WHERE id = '$id'");
    $row = $result->fetch_assoc();
    $password_lama = $row['password'];

// Cek apakah field password diisi atau tidak
if (!empty($_POST['password'])) {
    $password = $_POST['password']; // Menggunakan password baru jika field diisi
} else {
    $password = $password_lama; // Menggunakan password lama jika field kosong
}

    // Ambil nama file foto lama dari database
    $result = $koneksi->query("SELECT foto_profil FROM mahasiswa WHERE id = '$id'");
    $row = $result->fetch_assoc();
    $foto_lama = $row['foto_profil'];


    // Cek apakah checkbox untuk menghapus foto dicentang
    if (isset($_POST['delete_foto'])) {
        // Hapus foto lama dari server jika ada
        if ($foto_lama && file_exists("uploads/" . $foto_lama)) {
            unlink("uploads/" . $foto_lama); // Menghapus file foto lama
        }
        $foto_baru = null; // Set foto baru menjadi null
    } else {
        // Proses upload foto baru jika ada
        if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] == UPLOAD_ERR_OK) {
            // Tentukan direktori untuk menyimpan file
            $target_dir = "uploads/";
            $foto_baru = $_FILES['foto_profil']['name'];
            $target_file = $target_dir . basename($foto_baru);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Cek apakah file gambar adalah gambar yang sebenarnya atau file palsu
            $check = getimagesize($_FILES['foto_profil']['tmp_name']);
            if ($check === false) {
                echo "File yang diupload bukan gambar.";
                $uploadOk = 0;
            }

            // Cek ukuran file (maksimal 2MB)
            if ($_FILES['foto_profil']['size'] > 2000000) {
                echo "Maaf, ukuran file terlalu besar.";
                $uploadOk = 0;
            }

            // Cek format file
            if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
                echo "Maaf, hanya file JPG, JPEG, PNG & GIF yang diperbolehkan.";
                $uploadOk = 0;
            }

            // Cek jika $uploadOk di-set menjadi 0 oleh kesalahan
            if ($uploadOk == 0) {
                echo "Maaf, file tidak dapat diupload.";
            } else {
                // Jika semuanya baik, coba untuk mengupload file
                if (move_uploaded_file($_FILES['foto_profil']['tmp_name'], $target_file)) {
                    // Hapus foto lama dari server jika ada
                    if ($foto_lama && file_exists($target_dir . $foto_lama)) {
                        unlink($target_dir . $foto_lama); // Menghapus file foto lama
                    }
                } else {
                    echo "Maaf, terjadi kesalahan saat mengupload file.";
                }
            }
        } else {
            $foto_baru = $foto_lama; // Jika tidak ada foto baru, gunakan foto lama
        }
    }

    // Update data mahasiswa di database
    $query = "UPDATE mahasiswa SET 
                nim = '$nim', 
                nama = '$nama', 
                username = '$username', 
                password = '$password', 
                email = '$email', 
                foto_profil = '$foto_baru' 
              WHERE id = '$id'";

    if ($koneksi->query($query) === TRUE) {
        // Redirect ke homepage_admin.php setelah berhasil
        header("Location: homepage_admin.php");
        exit();
    } else {
        echo "Error updating record: " . $koneksi->error;
    }
}
?>