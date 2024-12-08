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

// Get bin_code and date filter from GET parameters
$bin_code = isset($_GET['bin_code']) ? $conn->real_escape_string($_GET['bin_code']) : null;
$dateFilter = isset($_GET['date']) ? $conn->real_escape_string($_GET['date']) : null;

// Query to fetch data from `bin_summary` table
$query = "
    SELECT DATE_FORMAT(timestamp, '%Y-%m-%d') AS date,
           total_small,
           total_medium,
           total_large,
           total_bottles
    FROM bottles_collected
    WHERE bin_code = '$bin_code'
";

// Apply date filter if provided
if ($dateFilter) {
    $query .= " AND DATE(timestamp) = '$dateFilter'";
}

$query .= " ORDER BY timestamp DESC";  // Order by timestamp

$result = $conn->query($query);

$data = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'date' => $row['date'],
            'small_bottles' => $row['total_small'],
            'medium_bottles' => $row['total_medium'],
            'large_bottles' => $row['total_large'],
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

// Add watermark (Logo image)
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
$pdf->Cell(0, 10, 'Bin Code: ' . $bin_code, 0, 1, 'L'); // Left aligned Bin Code
$pdf->Ln(5); // Add some space after the Bin Code

// Table header with padding and margins
$pdf->SetFont('helvetica', 'B', 12); // Table header font
$pdf->Cell(40, 10, 'Date', 1, 0, 'C');
$pdf->Cell(40, 10, 'Small Bottles', 1, 0, 'C');
$pdf->Cell(40, 10, 'Medium Bottles', 1, 0, 'C');
$pdf->Cell(40, 10, 'Large Bottles', 1, 0, 'C');
$pdf->Cell(40, 10, 'Total Bottles', 1, 1, 'C');
$pdf->SetFont('helvetica', '', 12); // Regular font for table rows

// Loop through data and populate table rows
foreach ($data as $row) {
    $pdf->Cell(40, 10, $row['date'], 1, 0, 'C');
    $pdf->Cell(40, 10, $row['small_bottles'], 1, 0, 'C');
    $pdf->Cell(40, 10, $row['medium_bottles'], 1, 0, 'C');
    $pdf->Cell(40, 10, $row['large_bottles'], 1, 0, 'C');
    $pdf->Cell(40, 10, $row['total_bottles'], 1, 1, 'C');
}

// Output the PDF
$pdf->Output('Bottle_Report_' . $bin_code . '.pdf', 'I'); // 'I' for inline view, 'D' for download
?>
