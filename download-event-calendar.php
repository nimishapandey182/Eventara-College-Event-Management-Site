<?php
ob_start();
require_once 'libs/fpdf/fpdf.php';
include "admin/config.php";

class PDF extends FPDF {
    function Header() {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'College Events Calendar', 0, 1, 'C');
        $this->Ln(3);

        $this->SetFont('Arial', 'B', 8);
        $this->SetFillColor(200, 220, 255);
        $this->Cell(25, 8, 'Start Date', 1, 0, 'C', true);
        $this->Cell(25, 8, 'End Date', 1, 0, 'C', true);
        $this->Cell(20, 8, 'Start Time', 1, 0, 'C', true);
        $this->Cell(20, 8, 'End Time', 1, 0, 'C', true);
        $this->Cell(60, 8, 'Event Name', 1, 0, 'C', true);
        $this->Cell(40, 8, 'Venue', 1, 1, 'C', true);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page '.$this->PageNo(), 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 8);

$sql = "SELECT event_name, event_venue, start_date, end_date, start_time, end_time FROM `event-list` ORDER BY start_date ASC";
$result = mysqli_query($conn, $sql);

while ($row = mysqli_fetch_assoc($result)) {
    $pdf->Cell(25, 8, date('d-M-Y', strtotime($row['start_date'])), 1);
    $pdf->Cell(25, 8, date('d-M-Y', strtotime($row['end_date'])), 1);
    $pdf->Cell(20, 8, $row['start_time'] ?? '-', 1);
    $pdf->Cell(20, 8, $row['end_time'] ?? '-', 1);
    $pdf->Cell(60, 8, mb_strimwidth($row['event_name'], 0, 35, "..."), 1);
    $pdf->Cell(40, 8, mb_strimwidth($row['event_venue'], 0, 25, "..."), 1);
    $pdf->Ln();
}

ob_end_clean(); // Clean output buffer
$pdf->Output('D', 'College_Events_Calendar.pdf');
exit;
