<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'Coordinator') {
    header("Location: login.php");
    exit();
}

// Start session and check if user is logged in as Coordinator  
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'Coordinator') {
  header("Location: login.php");
  exit();
}

$conn = new mysqli("localhost", "root", "", "College_Complaint_DB");

$coordID = $_SESSION['user_id'];

// Get the coordinator's category
$stmt = $conn->prepare("SELECT CatID FROM Coordinator WHERE CoordID = ?");
$stmt->bind_param("i", $coordID);
$stmt->execute();
$stmt->bind_result($catID);
$stmt->fetch();
$stmt->close();

// Get complaints of this category that are In Progress or Pending
$complaints = $conn->prepare("SELECT c.CompID, u.Name AS StudentName, c.Description, c.Status FROM Complaints c JOIN User u ON c.UserID = u.UserID WHERE c.CatID = ?");
$complaints->bind_param("i", $catID);
$complaints->execute();
$result = $complaints->get_result();
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Coordinator Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    :root {
      --primary: #2ecc71;
      --accent: #27ae60;
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

    .your-ac {
      margin-top: 50px;
    }

    .your-ac h2 {
      margin-bottom: 20px;
      color: var(--primary);
      font-size: 22px;
    }

    .complaint-box {
      background: #fff;
      border-left: 5px solid var(--primary);
      padding: 15px 20px;
      margin-bottom: 15px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    footer {
      text-align: center;
      padding: 20px;
      color: #777;
      font-size: 14px;
      background: #f8f8f8;
      margin-top: 30px;
    }


    @keyframes rotateCards {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-10px); }
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
    <h2>🧑‍💼 Welcome, <?php echo $_SESSION['user_name']; ?> (Coordinator)</h2>
    <a href="manage_complaints.php"><i class="bi bi-tags"></i> Manage Complaints</a>
    <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
  </div>

  <main id="main">
    <header>
      <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
      <h1>Coordinator Dashboard</h1>
    </header>

    

      <div class="card">
        <h3><i class="bi bi-tags"></i> Manage Complaints</h3>
        <p> View and handle student complaints. You can update the status and review assigned tasks.</p>
        <a href="manage_complaints.php">Manage Categories →</a>
      </div>
    </div>

    <!-- Assigned Complaints Section -->
    <div class="your-ac">
      <h2><i class="fas fa-file-alt"></i> Your Assigned Complaints</h2>
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="complaint-box">
          <p><strong>Complaint ID:</strong> <?= $row['CompID'] ?></p>
          <p><strong>From Student:</strong> <?= $row['StudentName'] ?></p>
          <p><strong>Status:</strong> <?= $row['Status'] ?></p>
          <p><strong>Description:</strong> <?= $row['Description'] ?></p>
        </div>
      <?php endwhile; ?>
    </div>

    <footer>
      &copy; <?php echo date("Y"); ?> Coordinator Dashboard. All rights reserved.
    </footer>
  </div>

  </main>

  <script>
    function toggleSidebar() {
      document.getElementById('sidebar').classList.toggle('hidden');
      document.getElementById('main').classList.toggle('full');
    }
  </script>
</body>
</html>
