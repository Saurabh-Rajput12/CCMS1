<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'Student') {
    header("Location: login.php");
    exit();
}
include 'db_connect.php';

$user_id = $_SESSION['user_id']; // Get the logged-in student's ID

// Fetch complaints submitted by the student
$sql = "SELECT c.CompID, cat.Cat_Name, c.Description, c.Status, c.DateSubmitted 
        FROM complaints c
        JOIN category cat ON c.CatID = cat.CatID
        WHERE c.UserID = '$user_id' 
        ORDER BY c.DateSubmitted DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Complaint Status</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
  <style>
    /* Global Styles */
    body {
      font-family: 'Roboto', sans-serif;
      margin: 0;
      padding: 0;
      color: #2c3e50;
      text-align: center;
      padding-top: 50px;
      background-color: #f8f9fa; /* Light background for better contrast */
    }

    h2 {
      font-size: 36px;
      color: #2c3e50;
      margin-bottom: 30px;
      text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.2);
    }

    /* Table Styles */
    table {
      width: 80%;
      margin: 40px auto;
      border-collapse: collapse;
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
      background-color: #ffffff;
      border-radius: 12px;
      overflow: hidden;
    }

    th, td {
      border: 1px solid #ddd;
      padding: 14px;
      text-align: left;
      font-size: 16px;
    }

    th {
      background-color: #3498db;
      color: white;
      font-size: 18px;
      letter-spacing: 1px;
    }

    td {
      background-color: #fafafa;
    }

    tr:nth-child(even) td {
      background-color: #f2f2f2;
    }

    tr:hover {
      background-color: #eaf2f8;
      transform: scale(1.02);
      transition: all 0.3s ease-in-out;
    }

    tr:hover td {
      background-color: #e1ecf4;
    }

    /* Back Link */
    .back-link {
      display: inline-block;
      margin-top: 30px;
      text-decoration: none;
      color: #3498db;
      font-weight: bold;
      font-size: 18px;
      transition: 0.3s ease;
    }

    .back-link:hover {
      text-decoration: underline;
      color: #2c3e50;
    }

    /* Icon Styles */
    .icon {
      margin-right: 8px;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      table {
        width: 95%;
      }

      h2 {
        font-size: 28px;
      }

      .back-link {
        font-size: 16px;
      }
    }

  </style>
</head>
<body>

  <h2><i class="fas fa-clipboard-list icon"></i> Your Complaint Status</h2>

  <table>
    <tr>
      <th><i class="fas fa-id-badge icon"></i> Complaint ID</th>
      <th><i class="fas fa-comment-dots icon"></i> Description</th>
      <th><i class="fas fa-tasks icon"></i> Status</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= $row['CompID'] ?></td>
        <td><?= $row['Description'] ?></td>
        <td><?= $row['Status'] ?></td>
      </tr>
    <?php endwhile; ?>
  </table>

  <a href="student_dashboard.php" class="back-link"><i class="fas fa-arrow-left icon"></i> Back to Dashboard</a>

</body>
</html>


