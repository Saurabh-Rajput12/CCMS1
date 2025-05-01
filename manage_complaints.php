<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'Coordinator') {
    header("Location: login.php");
    exit();
}
include 'db_connect.php';

// Fetch complaints from the database
$sql = "SELECT c.CompID, u.Name AS StudentName, cat.Cat_Name AS CategoryName, 
               c.Description, c.Status, c.DateSubmitted 
        FROM Complaints c
        JOIN User u ON c.UserID = u.UserID
        JOIN Category cat ON c.CatID = cat.CatID
        ORDER BY c.DateSubmitted DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Manage Complaints</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <!-- Font Awesome CDN for icons -->
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
      margin: 30px;
      background-color: #f4f6f9;
      color: #333;
    }

    h2 {
      text-align: center;
      color: #2c3e50;
      margin-bottom: 30px;
      font-size: 32px;
    }

    table {
      width: 90%;
      margin: auto;
      border-collapse: separate;
      border-spacing: 0 10px;
    }

    th, td {
      padding: 14px;
      text-align: left;
      background-color: #fff;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
      border-bottom: 2px solid #ecf0f1;
    }

    th {
      background-color: #3498db;
      color: white;
      border-radius: 6px 6px 0 0;
    }

    tr:hover td {
      background-color: #ecf6ff;
    }

    .btn {
      padding: 8px 12px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      font-weight: 500;
    }

    .btn-update {
      background-color: #5dade2;
      color: white;
      transition: background-color 0.3s ease;
    }

    .btn-update:hover {
      background-color: #2874a6;
    }

    .btn-update i {
      margin-right: 6px;
    }

    .back-link {
      display: block;
      text-align: center;
      margin-top: 30px;
      color: #3498db;
      font-weight: bold;
      text-decoration: none;
      transition: color 0.3s ease;
    }

    .back-link:hover {
      color: #21618c;
      text-decoration: underline;
    }

    @media screen and (max-width: 768px) {
      table, tr, td, th {
        font-size: 14px;
      }

      .btn {
        padding: 6px 10px;
        font-size: 13px;
      }
    }
  </style>
</head>
<body>

  <h2><i class="fas fa-clipboard-list"></i> Manage Complaints</h2>

  <table>
    <tr>
      <th><i class="fas fa-id-badge"></i> Complaint ID</th>
      <th><i class="fas fa-user-graduate"></i> Student Name</th>
      <th><i class="fas fa-comment-dots"></i> Description</th>
      <th><i class="fas fa-info-circle"></i> Status</th>
      <th><i class="fas fa-cogs"></i> Action</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
      <td><?= $row['CompID'] ?></td>
      <td><?= $row['StudentName'] ?></td>
      <td><?= $row['Description'] ?></td>
      <td><?= $row['Status'] ?></td>
      <td>
        <a href="update_status.php?compid=<?= $row['CompID'] ?>" class="btn btn-update">
          <i class="fas fa-edit"></i> Update Status
        </a>
      </td>
    </tr>
    <?php endwhile; ?>
  </table>

  <a href="coordinator_dashboard.php" class="back-link">
    <i class="fas fa-arrow-left"></i> Back to Dashboard
  </a>

</body>
</html>
