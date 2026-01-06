<?php
require_once('tcpdf/tcpdf.php');

// Connect to MySQL
$conn = new mysqli("localhost", "root", "", "project_management");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $company = $_POST['company_name'];
    $project = $_POST['project_name'];
    $description = $_POST['description'];
    $budget = $_POST['budget'];
    $status = $_POST['status'];
    $tasks = $_POST['tasks'];

    // Fetch company logo from database
    $company_query = "SELECT logo FROM clients WHERE name = '$company'";
    $result = $conn->query($company_query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $logoFile = $row['logo'];  // e.g., 'uploads/logo.png'
    } else {
        $logoFile = '';  // Fallback if logo not found
    }

    // Create new PDF
    $pdf = new TCPDF();
    $pdf->AddPage();

    // Set title and styling
    $pdf->SetFont('helvetica', 'B', 20);
    $pdf->Cell(0, 15, 'Project Report', 0, 1, 'C');
    $pdf->Ln(5);

    // Add company logo (if available)
    if (!empty($logoFile) && file_exists($logoFile)) {
        $pdf->Image($logoFile, 80, 10, 50);  // Adjust x, y, width as needed
        $pdf->Ln(30);  // Add space after logo
    }

    // Project details
    $pdf->SetFont('helvetica', '', 12);
    $html = '
    <style>
        .section-title { font-weight: bold; font-size: 14pt; margin-top: 10px; }
        .label { font-weight: bold; }
    </style>
    <div>
        <p><span class="label">Company Name:</span> ' . htmlspecialchars($company) . '</p>
        <p><span class="label">Project Name:</span> ' . htmlspecialchars($project) . '</p>
        <p class="section-title">Description</p>
        <p>' . nl2br(htmlspecialchars($description)) . '</p>

        <p class="section-title">Estimated Budget</p>
        <p>RS ' . number_format($budget, 2) . '</p>

        <p class="section-title">Status</p>
        <p>' . htmlspecialchars($status) . '</p>

        <p class="section-title">Tasks and Assigned Members</p>
        <pre>' . htmlspecialchars($tasks) . '</pre>
    </div>
    ';

    $pdf->writeHTML($html, true, false, true, false, '');

    // Output PDF
    $pdf->Output('project_report.pdf', 'I');
} else {
    echo "No data received.";
}

$conn->close();
?>
