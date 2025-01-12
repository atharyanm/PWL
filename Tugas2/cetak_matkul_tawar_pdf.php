<?php
require_once('pdf/tcpdf.php');

class MYPDF extends TCPDF {
    public function Header() {
        $left_logo = 'kop/logoKiri.jpg';
        if (file_exists($left_logo)) {
            $this->Image($left_logo, 10, 10, 25, '', 'JPG', '', 'T', false, 300, '', false, false, 0);
        }

        $right_logo = 'kop/logoKanan.jpg';
        if (file_exists($right_logo)) {
            $this->Image($right_logo, $this->getPageWidth() - 35, 10, 25, '', 'JPG', '', 'T', false, 300, '', false, false, 0);
        }

        $this->SetFont('times', 'B', 14);
        $this->SetY(15);
        $this->Cell(0, 10, 'UNIVERSITAS DIAN NUSWANTORO', 0, 1, 'C');

        $this->SetFont('times', '', 12);
        $this->Cell(0, 5, 'FAKULTAS ILMU KOMPUTER', 0, 1, 'C');
        $this->Cell(0, 5, 'Jl. Nakula I No. 5-11, Pendrikan Kidul, Semarang', 0, 1, 'C');
        $this->Cell(0, 5, 'Telp: (024) 3517261, Email: Udinus@dinus.ac.id', 0, 1, 'C');

        $this->SetLineWidth(0.5);
        $this->Line(10, 42, $this->getPageWidth() - 10, 42);
    }

    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('times', 'I', 8);
        $this->Cell(0, 10, 'Halaman ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Admin');
$pdf->SetTitle('Laporan Jadwal Mata Kuliah Tawar');
$pdf->SetSubject('Laporan Jadwal Mata Kuliah');
$pdf->SetKeywords('Mata Kuliah, Jadwal, Laporan, Data');

$pdf->setHeaderFont(Array('times', '', 12));
$pdf->setFooterFont(Array('times', '', 8));

$pdf->SetMargins(10, 50, 10);
$pdf->SetHeaderMargin(5);
$pdf->SetFooterMargin(15);

$pdf->SetAutoPageBreak(TRUE, 20);
$pdf->AddPage('P'); // Changed to Portrait

$pdf->SetFont('times', 'B', 12);
$pdf->Cell(0, 10, 'LAPORAN JADWAL MATA KULIAH TAWAR', 0, 1, 'C');
$pdf->Ln(5);

require 'koneksi.php';

$query = "SELECT 
            mt.id_tawar, 
            m.namamatkul, 
            d.namadosen, 
            mt.kelompok, 
            mt.hari, 
            mt.jam, 
            mt.ruang,
            m.sks
          FROM matakuliah_tawar mt
          INNER JOIN matkul m ON mt.idmatkul = m.idmatkul
          INNER JOIN dosen d ON mt.npp = d.npp
          ORDER BY mt.hari, mt.jam";

$result = $koneksi->query($query);
if (!$result) {
    die("Query failed: " . $koneksi->error);
}

$pdf->SetFont('times', '', 12);
$pdf->SetFillColor(230, 230, 230);

$pageWidth = $pdf->getPageWidth() - 20;
$colNo = $pageWidth * 0.05;        // Decreased
$colMatkul = $pageWidth * 0.22;    // Increased
$colDosen = $pageWidth * 0.22;     // Increased
$colKelompok = $pageWidth * 0.12;  // Decreased
$colHari = $pageWidth * 0.08;      // Adjusted
$colJam = $pageWidth * 0.12;        // Kept same
$colRuang = $pageWidth * 0.08;     // Decreased
$colSKS = $pageWidth * 0.05;       // Decreased

$startX = ($pdf->getPageWidth() - ($colNo + $colMatkul + $colDosen + $colKelompok + $colHari + $colJam + $colRuang + $colSKS)) / 2;
$pdf->SetX($startX);

$pdf->SetFont('times', 'B', 12);
$pdf->Cell($colNo, 7, 'No', 1, 0, 'C', 1);
$pdf->Cell($colMatkul, 7, 'Mata Kuliah', 1, 0, 'C', 1);
$pdf->Cell($colDosen, 7, 'Dosen', 1, 0, 'C', 1);
$pdf->Cell($colKelompok, 7, 'Kelompok', 1, 0, 'C', 1);
$pdf->Cell($colHari, 7, 'Hari', 1, 0, 'C', 1);
$pdf->Cell($colJam, 7, 'Jam', 1, 0, 'C', 1);
$pdf->Cell($colRuang, 7, 'Ruang', 1, 0, 'C', 1);
$pdf->Cell($colSKS, 7, 'SKS', 1, 1, 'C', 1);

$pdf->SetFont('times', '', 12);

$no = 1;

while ($row = $result->fetch_assoc()) {
    $pdf->SetX($startX);
    $pdf->Cell($colNo, 6, $no++, 1, 0, 'C');
    $pdf->Cell($colMatkul, 6, $row['namamatkul'], 1, 0, 'L');
    $pdf->Cell($colDosen, 6, $row['namadosen'], 1, 0, 'L');
    $pdf->Cell($colKelompok, 6, $row['kelompok'], 1, 0, 'C');
    $pdf->Cell($colHari, 6, $row['hari'], 1, 0, 'C');
    $pdf->Cell($colJam, 6, $row['jam'], 1, 0, 'C');
    $pdf->Cell($colRuang, 6, $row['ruang'], 1, 0, 'C');
    $pdf->Cell($colSKS, 6, $row['sks'], 1, 1, 'C');

    if ($pdf->GetY() > $pdf->getPageHeight() - 30) {
        $pdf->AddPage('P');
        $pdf->SetFont('times', 'B', 12);
        $pdf->SetX($startX);
        $pdf->Cell($colNo, 7, 'No', 1, 0, 'C', 1);
        $pdf->Cell($colMatkul, 7, 'Mata Kuliah', 1, 0, 'C', 1);
        $pdf->Cell($colDosen, 7, 'Dosen', 1, 0, 'C', 1);
        $pdf->Cell($colKelompok, 7, 'Kelompok', 1, 0, 'C', 1);
        $pdf->Cell($colHari, 7, 'Hari', 1, 0, 'C', 1);
        $pdf->Cell($colJam, 7, 'Jam', 1, 0, 'C', 1);
        $pdf->Cell($colRuang, 7, 'Ruang', 1, 0, 'C', 1);
        $pdf->Cell($colSKS, 7, 'SKS', 1, 1, 'C', 1);
        $pdf->SetFont('times', '', 12);
    }
}

$pdf->Output('laporan_jadwal_matkul_tawar.pdf', 'I');
$koneksi->close();
?>