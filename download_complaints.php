<?php
require('tcpdf/tcpdf.php');
include 'db_connect.php';

// Get filters
$status = $_POST['status'] ?? '';
$category = $_POST['category'] ?? '';
$from_date = $_POST['from_date'] ?? '';
$to_date = $_POST['to_date'] ?? '';

// Build SQL query with filters
$where_clause = "1";
$params = [];
$types = "";

if (!empty($status)) {
    $where_clause .= " AND c.Status = ?";
    $params[] = $status;
    $types .= "s";
}
if (!empty($category)) {
    $where_clause .= " AND c.CatID = ?";
    $params[] = $category;
    $types .= "i";
}
if (!empty($from_date) && !empty($to_date)) {
    $where_clause .= " AND c.DateSubmitted BETWEEN ? AND ?";
    $params[] = $from_date;
    $params[] = $to_date;
    $types .= "ss";
}

$query = "SELECT c.CompID, u.Name AS StudentName, cat.Cat_Name AS CategoryName, 
                 c.Description, c.Status, c.DateSubmitted 
          FROM Complaints c
          JOIN User u ON c.UserID = u.UserID
          JOIN Category cat ON c.CatID = cat.CatID
          WHERE $where_clause
          ORDER BY c.DateSubmitted DESC";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Create PDF
$pdf = new TCPDF();
$pdf->AddPage();
$pdf->SetFont('Helvetica', '', 12);
$pdf->Cell(0, 10, 'Complaints List', 1, 1, 'C');
$pdf->Ln(5);

while ($row = $result->fetch_assoc()) {
    $pdf->MultiCell(0, 10, "Complaint ID: {$row['CompID']}\nStudent: {$row['StudentName']}\nCategory: {$row['CategoryName']}\nDescription: {$row['Description']}\nStatus: {$row['Status']}\nDate Submitted: {$row['DateSubmitted']}\n", 1, 'L');
    $pdf->Ln(5);
}

$pdf->Output('Complaints_List.pdf', 'D');

?>



