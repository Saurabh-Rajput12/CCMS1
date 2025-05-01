<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'Student') {
    header("Location: login.php");
    exit();
}
require 'db_connect.php';
$user_id = $_SESSION['user_id'];

// Fetch complaints for the logged-in student
$query = "SELECT CompID, Description, Status FROM Complaints WHERE UserID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Student Dashboard</title>

  <!-- Bootstrap Icons and Font Awesome -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    :root {
      --primary: #f39c12; /* Orange */
      --accent: #e67e22;  /* Darker Orange */
      --light-bg: #f1f6fb;
      --text-dark: #333;
      --white: #ffffff;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', sans-serif;
    }

    body {
      background-color: var(--light-bg);
      color: var(--text-dark);
    }

    .sidebar {
      width: 250px;
      min-height: 100vh;
      background: linear-gradient(to bottom, var(--primary), var(--accent));
      color: #fff;
      padding: 20px;
      position: fixed;
      top: 0;
      left: 0;
      transition: transform 0.3s ease;
      z-index: 1000;
    }

    .sidebar.hidden {
      transform: translateX(-100%);
    }

    .sidebar h2 {
      margin-bottom: 30px;
      font-size: 20px;
      font-weight: 600;
    }

    .sidebar a {
      display: flex;
      align-items: center;
      gap: 10px;
      text-decoration: none;
      color: #fff;
      padding: 10px 15px;
      margin-bottom: 10px;
      border-radius: 8px;
      transition: background 0.3s ease;
    }

    .sidebar a:hover,
    .sidebar a.active {
      background: rgba(255, 255, 255, 0.2);
    }

    #main {
      margin-left: 250px;
      transition: margin-left 0.3s ease;
      width: calc(100% - 250px);
    }

    #main.full {
      margin-left: 0;
      width: 100%;
    }

    header {
      background-color: white;
      padding: 15px 20px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
      margin-bottom: 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    header h1 {
      font-size: 24px;
      color: var(--primary);
    }

    .toggle-btn {
      font-size: 24px;
      background: none;
      border: none;
      cursor: pointer;
      color: var(--primary);
    }

    .card-container {
      display: grid;
      gap: 20px;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      animation: rotateCards 20s linear infinite;
      padding: 0 20px;
    }

    .card {
      background: var(--white);
      border-radius: 12px;
      padding: 20px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
      transition: transform 0.3s;
    }

    .card:hover {
      transform: translateY(-5px) scale(1.02);
      box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
    }

    .card h3 {
      color: var(--primary);
      margin-bottom: 10px;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .card p {
      font-size: 14px;
    }

    .card a {
      display: inline-block;
      margin-top: 10px;
      text-decoration: none;
      font-weight: 500;
      color: var(--primary);
    }

    .card a:hover {
      text-decoration: underline;
      color: var(--accent);
    }

    @keyframes rotateCards {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-10px); }
    }

    .table-container {
      margin: 20px;
      overflow-x: auto;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
      overflow: hidden;
    }

    table th,
    table td {
      padding: 12px 15px;
      text-align: left;
      border-bottom: 1px solid #f0f0f0;
    }

    table th {
      background: var(--primary);
      color: #fff;
      font-weight: 600;
    }

    table tr:hover {
      background-color: #f9f9f9;
    }

    .btn-feedback {
      background-color: var(--accent);
      color: #fff;
      padding: 6px 12px;
      text-decoration: none;
      border-radius: 6px;
      display: inline-flex;
      align-items: center;
      gap: 5px;
      font-size: 14px;
      transition: background-color 0.3s ease;
    }

    .btn-feedback:hover {
      background-color: #cf711b;
    }

    footer {
      text-align: center;
      margin: 40px 0 20px;
      color: #888;
      font-size: 14px;
    }

    @media (max-width: 768px) {
      .sidebar {
        transform: translateX(-100%);
      }

      #main {
        margin-left: 0;
        width: 100%;
      }
    }
  </style>
</head>
<body>
  <div class="sidebar" id="sidebar">
    <h2>👩‍🎓 Welcome, <?php echo $_SESSION['user_name']; ?> (Student)</h2>
    <a href="complaint_status.php"><i class="bi bi-eye"></i> View Complaints</a>
    <a href="complaint_form.php"><i class="bi bi-pencil"></i> File a Complaint</a>
    <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
  </div>

  <main id="main">
    <header>
      <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
      <h1>Student Dashboard</h1>
    </header>

    <div class="card-container">
      <div class="card">
        <h3><i class="bi bi-eye"></i> View Complaints</h3>
        <p>Review your submitted complaints and track their status.</p>
        <a href="complaint_status.php">View Complaints →</a>
      </div>
      
      <div class="card">
        <h3><i class="bi bi-pencil"></i> File a Complaint</h3>
        <p>Submit a new complaint to the college authorities for resolution.</p>
        <a href="complaint_form.php">File Complaint →</a>
      </div>
    </div>
    
    <h3 style="text-align:center; color:#2c3e50; margin-top: 40px;">Your Complaints</h3>

    <div class="table-container">
      <table>
        <tr>
          <th>Complaint</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= $row['Description'] ?></td>
          <td><?= $row['Status'] ?></td>
          <td>
            <?php if ($row['Status'] == 'Resolved'): ?>
              <a href="feedback.php?comp_id=<?= $row['CompID'] ?>" class="btn-feedback">
                <i class="fas fa-comment-dots"></i> Feedback
              </a>
            <?php else: ?>
              <span>N/A</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endwhile; ?>
      </table>
    </div>

    <footer>
      © 2025 College Complaint Management System
    </footer>
  </main>

  <script>
    function toggleSidebar() {
      document.getElementById('sidebar').classList.toggle('hidden');
      document.getElementById('main').classList.toggle('full');
    }
  </script>
</body>
</html>




