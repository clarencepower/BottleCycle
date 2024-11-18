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

$dateFilter = isset($_GET['date']) ? $conn->real_escape_string($_GET['date']) : null;

$query = "
    SELECT all_dates.date,
           COALESCE(small_counts.small_bottles, 0) AS small_bottles,
           COALESCE(medium_counts.medium_bottles, 0) AS medium_bottles,
           COALESCE(large_counts.large_bottles, 0) AS large_bottles,
           (COALESCE(small_counts.small_bottles, 0) + COALESCE(medium_counts.medium_bottles, 0) + COALESCE(large_counts.large_bottles, 0)) AS total_bottles
    FROM (
        SELECT DISTINCT DATE_FORMAT(timestamp, '%Y-%m-%d') AS date FROM small_bottle_counts
        UNION
        SELECT DISTINCT DATE_FORMAT(timestamp, '%Y-%m-%d') AS date FROM medium_bottle_counts
        UNION
        SELECT DISTINCT DATE_FORMAT(timestamp, '%Y-%m-%d') AS date FROM large_bottle_counts
    ) AS all_dates
    LEFT JOIN (
        SELECT DATE_FORMAT(timestamp, '%Y-%m-%d') AS date,
               count AS small_bottles
        FROM small_bottle_counts AS t1
        WHERE id = (SELECT MAX(id) FROM small_bottle_counts WHERE DATE(timestamp) = DATE(t1.timestamp))
    ) AS small_counts ON all_dates.date = small_counts.date
    LEFT JOIN (
        SELECT DATE_FORMAT(timestamp, '%Y-%m-%d') AS date,
               count AS medium_bottles
        FROM medium_bottle_counts AS t2
        WHERE id = (SELECT MAX(id) FROM medium_bottle_counts WHERE DATE(timestamp) = DATE(t2.timestamp))
    ) AS medium_counts ON all_dates.date = medium_counts.date
    LEFT JOIN (
        SELECT DATE_FORMAT(timestamp, '%Y-%m-%d') AS date,
               count AS large_bottles
        FROM large_bottle_counts AS t3
        WHERE id = (SELECT MAX(id) FROM large_bottle_counts WHERE DATE(timestamp) = DATE(t3.timestamp))
    ) AS large_counts ON all_dates.date = large_counts.date
";

if ($dateFilter) {
    $query .= " WHERE all_dates.date = '$dateFilter'";
}
$query .= " ORDER BY all_dates.date DESC";

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
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Bottle Cycle');
$pdf->SetTitle('Bottle Counts Report');
$pdf->SetSubject('Bottle Collection Data');
$pdf->SetMargins(10, 10, 10);
$pdf->AddPage();

// Add watermark
$logo = '../drawable/headerlogo.jpg';
$pdf->SetAlpha(0.3); // Set transparency
$pdf->Image($logo, 30, 60, 150, 0, '', '', '', false, 300, '', false, false, 0, false, false, false);
$pdf->SetAlpha(); // Reset transparency

// Title and styling
$pdf->SetFont('helvetica', 'B', 18);
$pdf->Cell(0, 15, 'Bottle Cycle - Daily Bottle Counts Report', 0, 1, 'C');
$pdf->Ln(20);

// Table header styling
$pdf->SetFont('helvetica', 'B', 12);
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(35, 10, 'Date', 1, 0, 'C', 1);
$pdf->Cell(35, 10, 'Small Bottles', 1, 0, 'C', 1);
$pdf->Cell(35, 10, 'Medium Bottles', 1, 0, 'C', 1);
$pdf->Cell(35, 10, 'Large Bottles', 1, 0, 'C', 1);
$pdf->Cell(35, 10, 'Total Bottles', 1, 1, 'C', 1);

// Table data with alternating row colors
$pdf->SetFont('helvetica', '', 12);
$fill = false;
foreach ($data as $row) {
    $pdf->SetFillColor($fill ? 245 : 255, $fill ? 245 : 255, $fill ? 245 : 255);
    $pdf->Cell(35, 10, $row['date'], 1, 0, 'C', $fill);
    $pdf->Cell(35, 10, $row['small_bottles'], 1, 0, 'C', $fill);
    $pdf->Cell(35, 10, $row['medium_bottles'], 1, 0, 'C', $fill);
    $pdf->Cell(35, 10, $row['large_bottles'], 1, 0, 'C', $fill);
    $pdf->Cell(35, 10, $row['total_bottles'], 1, 1, 'C', $fill);
    $fill = !$fill; // Toggle fill color
}

// Footer with page number
$pdf->SetY(-15);
$pdf->SetFont('helvetica', 'I', 8);
$pdf->Cell(0, 10, 'Page ' . $pdf->getAliasNumPage() . '/' . $pdf->getAliasNbPages(), 0, 0, 'C');

// Output PDF as a download
$pdf->Output('Bottle_Counts_Report.pdf', 'D');
?>
