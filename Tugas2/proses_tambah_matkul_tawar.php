<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['username']) || $_SESSION['status'] != 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $koneksi->begin_transaction();

        // Validate required fields
        $required = ['idmatkul', 'npp', 'hari', 'jam', 'gedung', 'lantai', 'ruang', 'kodeKelompokBase', 'noKelompok'];
        foreach ($required as $field) {
            if (!isset($_POST[$field]) || empty($_POST[$field])) {
                throw new Exception("Field $field is required");
            }
        }

        // Construct kelompok and ruang
        $kelompok = $_POST['kodeKelompokBase'] . $_POST['noKelompok'];
        $ruang = sprintf("%s.%d.%d", 
            $_POST['gedung'],
            (int)$_POST['lantai'],
            (int)$_POST['ruang']
        );

        // Check existing kelompok
        $check_kelompok = $koneksi->prepare("SELECT id_tawar FROM matakuliah_tawar WHERE idmatkul = ? AND kelompok = ?");
        $check_kelompok->bind_param("ss", $_POST['idmatkul'], $kelompok);
        $check_kelompok->execute();
        if ($check_kelompok->get_result()->num_rows > 0) {
            throw new Exception("Kelompok " . $kelompok . " sudah ada untuk mata kuliah ini");
        }

        // Check room availability
        $check_room = $koneksi->prepare("SELECT mt.id_tawar, m.namamatkul, d.namadosen 
                                        FROM matakuliah_tawar mt
                                        JOIN matkul m ON mt.idmatkul = m.idmatkul
                                        JOIN dosen d ON mt.npp = d.npp
                                        WHERE mt.hari = ? AND mt.jam = ? AND mt.ruang = ?");
        $check_room->bind_param("sss", $_POST['hari'], $_POST['jam'], $ruang);
        $check_room->execute();
        if ($check_room->get_result()->num_rows > 0) {
            throw new Exception("Ruang " . $ruang . " sudah digunakan pada waktu tersebut");
        }

        // Check lecturer availability
        $check_lecturer = $koneksi->prepare("SELECT mt.id_tawar, m.namamatkul 
                                            FROM matakuliah_tawar mt
                                            JOIN matkul m ON mt.idmatkul = m.idmatkul
                                            WHERE mt.npp = ? AND mt.hari = ? AND mt.jam = ?");
        $check_lecturer->bind_param("sss", $_POST['npp'], $_POST['hari'], $_POST['jam']);
        $check_lecturer->execute();
        if ($check_lecturer->get_result()->num_rows > 0) {
            throw new Exception("Dosen sudah memiliki jadwal mengajar pada waktu tersebut");
        }

        // Insert new schedule
        $insert = $koneksi->prepare("INSERT INTO matakuliah_tawar (npp, idmatkul, kelompok, hari, jam, ruang) 
                                    VALUES (?, ?, ?, ?, ?, ?)");
        $insert->bind_param("ssssss", 
            $_POST['npp'], 
            $_POST['idmatkul'], 
            $kelompok,
            $_POST['hari'],
            $_POST['jam'],
            $ruang
        );

        if (!$insert->execute()) {
            throw new Exception("Gagal menyimpan data: " . $insert->error);
        }

        $koneksi->commit();
        echo json_encode([
            'status' => 'success',
            'message' => 'Jadwal berhasil ditambahkan'
        ]);

    } catch (Exception $e) {
        $koneksi->rollback();
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    } finally {
        if (isset($check_kelompok)) $check_kelompok->close();
        if (isset($check_room)) $check_room->close();
        if (isset($check_lecturer)) $check_lecturer->close();
        if (isset($insert)) $insert->close();
        $koneksi->close();
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method'
    ]);
}