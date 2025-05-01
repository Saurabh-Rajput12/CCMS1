<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'Coordinator') {
    header("Location: login.php");
    exit();
}

include 'db_connect.php';

// Check if a complaint ID is provided
if (!isset($_GET['compid']) || empty($_GET['compid'])) {
    die("Complaint ID is missing!");
}

$compid = $_GET['compid'];

// Fetch complaint details
$sql = "SELECT c.CompID, u.Name AS StudentName, cat.Cat_Name AS CategoryName, 
               c.Description, c.Status, c.DateSubmitted 
        FROM Complaints c
        JOIN User u ON c.UserID = u.UserID
        JOIN Category cat ON c.CatID = cat.CatID
        WHERE c.CompID = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $compid);
$stmt->execute();
$result = $stmt->get_result();
$complaint = $result->fetch_assoc();

if (!$complaint) {
    die("Complaint not found!");
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_status = $_POST['status'];

    $update_sql = "UPDATE Complaints SET Status = ? WHERE CompID = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("si", $new_status, $compid);

    if ($update_stmt->execute()) {
        echo "<script>alert('Status updated successfully!'); window.location.href='manage_complaints.php';</script>";
    } else {
        echo "<script>alert('Error updating status!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Update Complaint Status</title>

  <!-- Font Awesome for icons -->
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    integrity="sha512-..."
    crossorigin="anonymous"
    referrerpolicy="no-referrer"
  />

  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f5f8fa;
      padding: 50px 20px;
      display: flex;
      justify-content: center;
      align-items: flex-start;
      min-height: 100vh;
    }

    .container {
      background-color: #fff;
      padding: 30px 35px;
      border-radius: 12px;
      box-shadow: 0 4px 18px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 600px;
    }

    h2 {
      text-align: center;
      color: #2c3e50;
      margin-bottom: 25px;
      font-size: 28px;
    }

    p {
      font-size: 16px;
      margin: 10px 0;
      color: #34495e;
    }

    p strong {
      color: #2c3e50;
    }

    label {
      display: block;
      font-weight: bold;
      margin-top: 20px;
      margin-bottom: 8px;
    }

    select {
      width: 100%;
      padding: 10px;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 16px;
    }

    button[type="submit"] {
      margin-top: 25px;
      width: 100%;
      padding: 12px;
      background-color: #27ae60;
      color: white;
      font-size: 16px;
      font-weight: bold;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    button[type="submit"]:hover {
      background-color: #1e8449;
    }

    .back-btn {
      display: inline-block;
      margin-top: 20px;
      background: none;
      border: none;
      color: #3498db;
      font-weight: bold;
      cursor: pointer;
      font-size: 15px;
      transition: color 0.3s ease;
    }

    .back-btn i {
      margin-right: 6px;
    }

    .back-btn:hover {
      color: #21618c;
      text-decoration: underline;
    }

    .info-icon {
      margin-right: 8px;
      color: #2980b9;
    }
  </style>
</head>
<body>

<div class="container">
  <h2><i class="fas fa-edit"></i> Update Complaint Status</h2>

  <p><i class="fas fa-id-badge info-icon"></i><strong>Complaint ID:</strong> <?= $complaint['CompID'] ?></p>
  <p><i class="fas fa-user info-icon"></i><strong>Student:</strong> <?= $complaint['StudentName'] ?></p>
  <p><i class="fas fa-layer-group info-icon"></i><strong>Category:</strong> <?= $complaint['CategoryName'] ?></p>
  <p><i class="fas fa-align-left info-icon"></i><strong>Description:</strong> <?= $complaint['Description'] ?></p>
  <p><i class="fas fa-info-circle info-icon"></i><strong>Current Status:</strong> <?= $complaint['Status'] ?></p>

  <form method="post">
    <label for="status"><i class="fas fa-sync-alt"></i> Change Status:</label>
    <select name="status" id="status">
      <option value="Pending" <?= $complaint['Status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
      <option value="In Progress" <?= $complaint['Status'] == 'In Progress' ? 'selected' : '' ?>>In Progress</option>
      <option value="Resolved" <?= $complaint['Status'] == 'Resolved' ? 'selected' : '' ?>>Resolved</option>
      <option value="Rejected" <?= $complaint['Status'] == 'Rejected' ? 'selected' : '' ?>>Rejected</option>
    </select>

    <button type="submit"><i class="fas fa-save"></i> Update Status</button>
  </form>

  <button class="back-btn" onclick="goBack()"><i class="fas fa-arrow-left"></i> Back</button>

  <script>
    function goBack() {
      window.history.back();
    }
  </script>

</div>

</body>
</html>
