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
        $this->SetFont('times', 'B', 14);

        // Judul Instansi
        $this->SetY(15); // Atur posisi vertikal header
        $this->Cell(0, 10, 'UNIVERSITAS DIAN NUSWANTORO', 0, 1, 'C');

        // Sub-judul
        $this->SetFont('times', '', 12);
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
        $this->SetFont('times', 'I', 8);
        // Nomor halaman
        $this->Cell(0, 10, 'Halaman ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

// Buat dokumen PDF baru
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set informasi dokumen
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nama Anda');
$pdf->SetTitle('Laporan Data Dosen');
$pdf->SetSubject('Laporan Dosen');
$pdf->SetKeywords('Dosen, Laporan, Data');

// Set font header dan footer
$pdf->setHeaderFont(Array('times', '', 12));
$pdf->setFooterFont(Array('times', '', 8));

// Set margin
$pdf->SetMargins(10, 50, 10);
$pdf->SetHeaderMargin(5);
$pdf->SetFooterMargin(15);

// Set page break
$pdf->SetAutoPageBreak(TRUE, 20);

// Tambahkan halaman
$pdf->AddPage('P'); // Portrait

// Judul Laporan
$pdf->SetFont('times', 'B', 12);
$pdf->Cell(0, 10, 'LAPORAN DATA DOSEN', 0, 1, 'C');
$pdf->Ln(5);

// Sambungkan database
require 'koneksi.php';

// Query data
$query = "SELECT npp, namadosen, homebase FROM dosen";
$result = $koneksi->query($query);

// Set font untuk tabel
$pdf->SetFont('times', '', 12);

// Warna header tabel
$pdf->SetFillColor(230, 230, 230);

// Hitung lebar halaman
$pageWidth = $pdf->getPageWidth() - 20; // Kurangi margin
$colNo = $pageWidth * 0.1;
$colNPP = $pageWidth * 0.3;
$colNama = $pageWidth * 0.3;
$colHomebase = $pageWidth * 0.3;

// Hitung posisi awal untuk menempatkan tabel di tengah
$startX = ($pdf->getPageWidth() - ($colNo + $colNPP + $colNama + $colHomebase)) / 2;
$pdf->SetX($startX);

// Header tabel
$pdf->SetFont('times', 'B', 12);
$pdf->Cell($colNo, 7, 'No', 1, 0, 'C', 1);
$pdf->Cell($colNPP, 7, 'NPP', 1, 0, 'C', 1);
$pdf->Cell($colNama, 7, 'Nama Dosen', 1, 0, 'C', 1);
$pdf->Cell($colHomebase, 7, 'Homebase', 1, 1, 'C', 1);

// Kembalikan font ke normal
$pdf->SetFont('times', '', 12);

// Data
$no = 1;
while ($row = $result->fetch_assoc()) {
    $pdf->SetX($startX);
    $pdf->Cell($colNo, 6, $no++, 1, 0, 'C');
    $pdf->Cell($colNPP, 6, $row['npp'], 1, 0, 'L');
    $pdf->Cell($colNama, 6, $row['namadosen'], 1, 0, 'L');
    $pdf->Cell($colHomebase, 6, $row['homebase'], 1, 1, 'L');

    // Tambahkan page break jika diperlukan
    if ($pdf->GetY() > $pdf->getPageHeight() - 30) {
        $pdf->AddPage('P');
        
        // Hitung ulang posisi awal
        $startX = ($pdf->getPageWidth() - ($colNo + $colNPP + $colNama + $colHomebase)) / 2;
        
        // Cetak ulang header tabel
        $pdf->SetFont('times', 'B', 12);
        $pdf->SetX($startX);
        $pdf->Cell($colNo, 7, 'No', 1, 0, 'C', 1);
        $pdf->Cell($colNPP, 7, 'NPP', 1, 0, 'C', 1);
        $pdf->Cell($colNama, 7, 'Nama Dosen', 1, 0, 'C', 1);
        $pdf->Cell($colHomebase, 7, 'Homebase', 1, 1, 'C', 1);
        $pdf->SetFont('times', '', 12);
    }
}

// Keluarkan PDF
$pdf->Output('laporan_dosen.pdf', 'I');

// Tutup koneksi
$koneksi->close();
?>