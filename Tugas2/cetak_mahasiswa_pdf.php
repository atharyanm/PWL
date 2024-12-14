<?php
// Include TCPDF library
require_once('pdf/tcpdf.php');

// Extend TCPDF class untuk custom Header dan Footer
class MYPDF extends TCPDF {
    // Page header
    public function Header() {
        // Logo kiri
        $left_logo = 'kop/logoKiri.jpg';
        if (file_exists($left_logo)) {
            $this->Image($left_logo, 10, 10, 25, '', 'JPG', '', 'T', false, 300, '', false, false, 0);
        }

        // Logo kanan
        $right_logo = 'kop/logoKanan.jpg';
        if (file_exists($right_logo)) {
            $this->Image($right_logo, $this->getPageWidth() - 35, 10, 25, '', 'JPG', '', 'T', false, 300, '', false, false, 0);
        }

        // Set font untuk header instansi
        $this->SetFont('helvetica', 'B', 14);

        // Judul Instansi
        $this->SetY(15); // Atur posisi vertikal header
        $this->Cell(0, 10, 'UNIVERSITAS DIAN NUSWANTORO', 0, 1, 'C');

        // Sub-judul
        $this->SetFont('helvetica', '', 10);
        $this->Cell(0, 5, 'FAKULTAS ILMU KOMPUTER', 0, 1, 'C');
        $this->Cell(0, 5, 'Jl. Nakula I No. 5-11, Pendrikan Kidul, Semarang', 0, 1, 'C');
        $this->Cell(0, 5, 'Telp: (024) 3517261, Email: Udinus@dinus.ac.id', 0, 1, 'C');

        // Garis pembatas header
        $this->SetLineWidth(0.5);
        $this->Line(10, 42, $this->getPageWidth() - 10, 42);
    }

    // Page footer
    public function Footer() {
        // Posisi 15mm dari bawah
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', 'I', 8);
        // Nomor halaman
        $this->Cell(0, 10, 'Halaman ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

// Buat dokumen PDF baru
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set informasi dokumen
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nama Anda');
$pdf->SetTitle('Laporan Data Mahasiswa');
$pdf->SetSubject('Laporan Mahasiswa');
$pdf->SetKeywords('Mahasiswa, Laporan, Data');

// Set font header dan footer
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// Set margin
$pdf->SetMargins(10, 50, 10); // Sesuaikan margin atas
$pdf->SetHeaderMargin(5);
$pdf->SetFooterMargin(15);

// Set page break
$pdf->SetAutoPageBreak(TRUE, 20);

// Tambahkan halaman
$pdf->AddPage();

// Judul Laporan
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, 'LAPORAN DATA MAHASISWA', 0, 1, 'C');
$pdf->Ln(5); // Tambahkan jarak sebelum tabel

// Sambungkan database
require 'koneksi.php';

// Query data
$query = "SELECT * FROM mahasiswa";
$result = $koneksi->query($query);

// Set font untuk tabel
$pdf->SetFont('helvetica', '', 10);

// Warna header tabel
$pdf->SetFillColor(230, 230, 230);

// Header tabel
$pdf->Cell(10, 7, 'No', 1, 0, 'C', 1);
$pdf->Cell(35, 7, 'NIM', 1, 0, 'C', 1);
$pdf->Cell(45, 7, 'Nama', 1, 0, 'C', 1);
$pdf->Cell(45, 7, 'Username', 1, 0, 'C', 1);
$pdf->Cell(45, 7, 'Email', 1, 1, 'C', 1);

// Data
$no = 1;
while ($row = $result->fetch_assoc()) {
    $pdf->Cell(10, 6, $no++, 1, 0, 'C');
    $pdf->Cell(35, 6, $row['nim'], 1, 0, 'L');
    $pdf->Cell(45, 6, $row['nama'], 1, 0, 'L');
    $pdf->Cell(45, 6, $row['username'], 1, 0, 'L');
    $pdf->Cell(45, 6, $row['email'], 1, 1, 'L');
}

// Keluarkan PDF
$pdf->Output('laporan_mahasiswa.pdf', 'I');
