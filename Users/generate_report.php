<?php
require_once '../vendor/tecnickcom/tcpdf/tcpdf.php';

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bottlecycle-ctu";

// Connect to the database
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get date filter if set
$dateFilter = isset($_GET['date']) ? $conn->real_escape_string($_GET['date']) : null;

// Query to fetch data from `ctu_0001` table
$query = "
    SELECT DATE_FORMAT(timestamp, '%Y-%m-%d') AS date,
           COALESCE(small_bottle_counts, 0) AS small_bottles,
           COALESCE(medium_bottle_counts, 0) AS medium_bottles,
           COALESCE(large_bottle_counts, 0) AS large_bottles,
           (COALESCE(small_bottle_counts, 0) + COALESCE(medium_bottle_counts, 0) + COALESCE(large_bottle_counts, 0)) AS total_bottles
    FROM ctu_0001
";

// Apply date filter if provided
if ($dateFilter) {
    $query .= " WHERE DATE(timestamp) = '$dateFilter'";
}

$query .= " ORDER BY timestamp DESC";  // Order by date

$result = $conn->query($query);

$data = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'date' => $row['date'],
            'small_bottles' => $row['small_bottles'],
            'medium_bottles' => $row['medium_bottles'],
            'large_bottles' => $row['large_bottles'],
            'total_bottles' => $row['total_bottles']
        ];
    }
} else {
    die("No records found.");
}

$conn->close();

// Initialize TCPDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Bottle Cycle');
$pdf->SetTitle('Bottle Counts Report');
$pdf->SetSubject('Bottle Collection Data');
$pdf->SetMargins(16, 16, 16);  // Adjust left, top, and right margins
$pdf->AddPage();

// Set timezone to Philippines Standard Time (PST)
date_default_timezone_set('Asia/Manila');

// Add watermark
$logo = '../drawable/headerlogo.jpg';
$pdf->SetAlpha(0.3); // Set transparency for logo
$pdf->Image($logo, 30, 60, 150, 0, '', '', '', false, 300, '', false, false, 0, false, false, false);
$pdf->SetAlpha(); // Reset transparency

// Title and styling
$pdf->SetFont('helvetica', 'B', 18);
$pdf->Cell(0, 15, 'Bottle Cycle - Daily Bottle Counts Report', 0, 1, 'C');
$pdf->Ln(10);

// Add the current date
$currentDate = date('F j, Y');  // Format: Month day, Year (e.g., November 21, 2024)
$pdf->SetFont('helvetica', 'I', 12); // Italic font for the date
$pdf->Cell(0, 10, 'Date: ' . $currentDate, 0, 1, 'C'); // Center aligned date
$pdf->Ln(5); // Add some space after the date

// Add the current time (PST)
$currentTime = date('h:i A');  // Format: Hour:Minute AM/PM (e.g., 12:45 PM)
$pdf->SetFont('helvetica', 'I', 12); // Italic font for the time
$pdf->Cell(0, 10, 'Time: ' . $currentTime, 0, 1, 'C'); // Center aligned time
$pdf->Ln(10); // Add some space after the time

// Add Bin Code above the table
$pdf->SetFont('helvetica', 'B', 12);  // Bold font for Bin Code
$pdf->Cell(0, 10, 'Bin Code: CTU-0001', 0, 1, 'L'); // Left aligned Bin Code
$pdf->Ln(5); // Add some space after the Bin Code

// Table header with padding and margins
$pdf->SetFont('helvetica', 'B', 11);
$pdf->SetCellPadding(4);  // Add padding inside the cells

// Set column widths (adjusted to fit within the A4 page size)
$pdf->Cell(35, 10, 'Date', 1, 0, 'C');  // Column for Date
$pdf->Cell(35, 10, 'Small Bottles', 1, 0, 'C');  // Column for Small Bottles
$pdf->Cell(35, 10, 'Medium Bottles', 1, 0, 'C'); // Column for Medium Bottles
$pdf->Cell(35, 10, 'Large Bottles', 1, 0, 'C');  // Column for Large Bottles
$pdf->Cell(35, 10, 'Total Bottles', 1, 1, 'C');  // Column for Total Bottles

// Table data
$pdf->SetFont('helvetica', '', 11);
foreach ($data as $row) {
    $pdf->Cell(35, 10, $row['date'], 1, 0, 'C');  // Date cell
    $pdf->Cell(35, 10, $row['small_bottles'], 1, 0, 'C');  // Small Bottles cell
    $pdf->Cell(35, 10, $row['medium_bottles'], 1, 0, 'C'); // Medium Bottles cell
    $pdf->Cell(35, 10, $row['large_bottles'], 1, 0, 'C');  // Large Bottles cell
    $pdf->Cell(35, 10, $row['total_bottles'], 1, 1, 'C');  // Total Bottles cell
}

// Output the PDF
$pdf->Output('Bottle_Counts_Report.pdf', 'I');
?>
