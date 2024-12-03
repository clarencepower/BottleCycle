<?php
require_once '../vendor/tecnickcom/tcpdf/tcpdf.php';

// Get the data from the POST request
$bins_data = json_decode($_POST['bins_data'], true);

// Initialize TCPDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Bottle Cycle');
$pdf->SetTitle('Bottle Counts Report');
$pdf->SetSubject('Bottle Collection Data');

// Set custom margins for a cleaner layout
$pdf->SetMargins(10, 10, 10);  // Adjust left, top, and right margins
$pdf->SetAutoPageBreak(TRUE, 15); // Set auto page breaks, with 15mm space at the bottom
$pdf->AddPage();

// Set timezone to Philippines Standard Time (PST)
date_default_timezone_set('Asia/Manila');

// Add watermark (Logo image)
$logo = '../drawable/headerlogo.jpg';
$pdf->SetAlpha(0.3); // Set transparency for logo
$pdf->Image($logo, 30, 30, 150, 0, '', '', '', false, 300, '', false, false, 0, false, false, false);
$pdf->SetAlpha(); // Reset transparency

// Title and styling
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 15, 'Bottle Cycle - All Bin Bottle Counts Report', 0, 1, 'C');
$pdf->Ln(10);

// Add the current date
$currentDate = date('F j, Y');  // Format: Month day, Year (e.g., November 21, 2024)
$pdf->SetFont('helvetica', 'I', 12); // Italic font for the date
$pdf->Cell(0, 10, 'Date: ' . $currentDate, 0, 1, 'C'); // Center aligned date
$pdf->Ln(5); // Add some space after the date

// Add the current time (PST)
$currentTime = date('h:i A');  // Format: Hour:Minute AM/PM (e.g., 12:45 PM)
$pdf->SetFont('helvetica', 'I', 12); // Italic font for the time
$pdf->Cell(0, 10, 'Time: ' . $currentTime, 0, 1, 'C');
$pdf->Ln(10); // Add some space after the time

// Table Header
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(40, 8, 'Bin Code', 1, 0, 'C');
$pdf->Cell(30, 8, 'Small Bottles', 1, 0, 'C');
$pdf->Cell(30, 8, 'Medium Bottles', 1, 0, 'C');
$pdf->Cell(30, 8, 'Large Bottles', 1, 0, 'C');
$pdf->Cell(30, 8, 'Total Bottles', 1, 0, 'C');
$pdf->Cell(40, 8, 'Timestamp', 1, 1, 'C'); // New column for timestamp

// Table Data
$pdf->SetFont('helvetica', '', 9);
$total_bottles = 0; // Initialize total bottles counter

foreach ($bins_data as $bin) {
    $pdf->Cell(40, 8, $bin['bin_code'], 1, 0, 'C');
    $pdf->Cell(30, 8, $bin['total_small'], 1, 0, 'C');
    $pdf->Cell(30, 8, $bin['total_medium'], 1, 0, 'C');
    $pdf->Cell(30, 8, $bin['total_large'], 1, 0, 'C');
    
    // Calculate total bottles for this bin
    $total = $bin['total_small'] + $bin['total_medium'] + $bin['total_large'];
    $pdf->Cell(30, 8, $total, 1, 0, 'C');
    $total_bottles += $total; // Add to the total count
    
    // Add timestamp in 12-hour format
    $timestamp = new DateTime($bin['timestamp']);
    $formatted_timestamp = $timestamp->format('Y-m-d h:i:s A'); // 12-hour format
    $pdf->Cell(40, 8, $formatted_timestamp, 1, 1, 'C');
}

// Add a row for total bottles across all bins
$pdf->SetFont('helvetica', 'B', 10);
$pdf->Cell(130, 8, 'Total Bottles Collected:', 1, 0, 'C');
$pdf->Cell(30, 8, $total_bottles, 1, 0, 'C');
$pdf->Cell(40, 8, '', 1, 1, 'C'); // Empty cell for timestamp

// Output the PDF
$pdf->Output('bottle_counts_report.pdf', 'I');
?>
