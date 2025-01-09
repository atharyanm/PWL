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
$pdf->SetTitle('Laporan Data Mata Kuliah');
$pdf->SetSubject('Laporan Mata Kuliah');
$pdf->SetKeywords('Mata Kuliah, Laporan, Data');

$pdf->setHeaderFont(Array('times', '', 12));
$pdf->setFooterFont(Array('times', '', 8));

$pdf->SetMargins(10, 50, 10);
$pdf->SetHeaderMargin(5);
$pdf->SetFooterMargin(15);

$pdf->SetAutoPageBreak(TRUE, 20);
$pdf->AddPage('P');

$pdf->SetFont('times', 'B', 12);
$pdf->Cell(0, 10, 'LAPORAN DATA MATA KULIAH', 0, 1, 'C');
$pdf->Ln(5);

require 'koneksi.php';

$query = "SELECT idmatkul, namamatkul, sks, jns, smt FROM matkul";
$result = $koneksi->query($query);

$pdf->SetFont('times', '', 12);
$pdf->SetFillColor(230, 230, 230);

$pageWidth = $pdf->getPageWidth() - 20;
$colNo = $pageWidth * 0.1;
$colKode = $pageWidth * 0.15;
$colNama = $pageWidth * 0.35;
$colSKS = $pageWidth * 0.1;
$colJenis = $pageWidth * 0.15;
$colSemester = $pageWidth * 0.15;

$startX = ($pdf->getPageWidth() - ($colNo + $colKode + $colNama + $colSKS + $colJenis + $colSemester)) / 2;
$pdf->SetX($startX);

$pdf->SetFont('times', 'B', 12);
$pdf->Cell($colNo, 7, 'No', 1, 0, 'C', 1);
$pdf->Cell($colKode, 7, 'Kode MK', 1, 0, 'C', 1);
$pdf->Cell($colNama, 7, 'Nama Mata Kuliah', 1, 0, 'C', 1);
$pdf->Cell($colSKS, 7, 'SKS', 1, 0, 'C', 1);
$pdf->Cell($colJenis, 7, 'Jenis', 1, 0, 'C', 1);
$pdf->Cell($colSemester, 7, 'Semester', 1, 1, 'C', 1);

$pdf->SetFont('times', '', 12);

$no = 1;
while ($row = $result->fetch_assoc()) {
    $pdf->SetX($startX);
    $pdf->Cell($colNo, 6, $no++, 1, 0, 'C');
    $pdf->Cell($colKode, 6, $row['idmatkul'], 1, 0, 'L');
    $pdf->Cell($colNama, 6, $row['namamatkul'], 1, 0, 'L');
    $pdf->Cell($colSKS, 6, $row['sks'], 1, 0, 'C');
    $pdf->Cell($colJenis, 6, $row['jns'], 1, 0, 'C');
    $pdf->Cell($colSemester, 6, $row['smt'], 1, 1, 'C');

    if ($pdf->GetY() > $pdf->getPageHeight() - 30) {
        $pdf->AddPage('P');
        $pdf->SetFont('times', 'B', 12);
        $pdf->SetX($startX);
        $pdf->Cell($colNo, 7, 'No', 1, 0, 'C', 1);
        $pdf->Cell($colKode, 7, 'Kode MK', 1, 0, 'C', 1);
        $pdf->Cell($colNama, 7, 'Nama Mata Kuliah', 1, 0, 'C', 1);
        $pdf->Cell($colSKS, 7, 'SKS', 1, 0, 'C', 1);
        $pdf->Cell($colJenis, 7, 'Jenis', 1, 0, 'C', 1);
        $pdf->Cell($colSemester, 7, 'Semester', 1, 1, 'C', 1);
        $pdf->SetFont('times', '', 12);
    }
}

$pdf->Output('laporan_matkul.pdf', 'I');
$koneksi->close();
?>