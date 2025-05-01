
<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'Admin') {
    header("Location: login.php");
    exit();
}
// PHP CODE FOR COMPLAINT MONITORING AND FEEDBACK MONITORING
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'Admin') {
  header("Location: login.php");
  exit();
}
include 'db_connect.php';
if (!$conn) {
  die("Database connection failed: " . mysqli_connect_error());
}

// Fetch filter and search values
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort_order = isset($_GET['sort']) && $_GET['sort'] == 'oldest' ? 'ASC' : 'DESC';

// Build SQL query with filters
$sql = "SELECT c.CompID, u.UserID AS User_Name, cat.Cat_Name AS CategoryName, 
             c.Description, c.Status, c.DateSubmitted 
      FROM Complaints c
      JOIN User u ON c.UserID = u.UserID
      JOIN Category cat ON c.CatID = cat.CatID";
$result1 = mysqli_query($conn, $sql);


$query = "SELECT f.FeedID, u.Name AS Student, c.Description, f.Rating, f.Comment, f.DateSubmitted 
        FROM Feedback f 
        JOIN Complaints c ON f.CompID = c.CompID 
        JOIN User u ON f.UserID = u.UserID
        ORDER BY f.DateSubmitted DESC";
$result2 = $conn->query($query);   
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard</title>
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
  <style>
    :root {
      --primary: #185a9d;
      --accent: #43cea2;
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

    .sidebar a svg {
      width: 20px;
      height: 20px;
    }

    #main {
      margin-left: 240px;
      transition: margin-left 0.3s ease;
      width: calc(100% - 240px);
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
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 18px;
  margin-bottom: 10px;
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

    .container, .Assign {
      background: white;
      padding: 20px;
      margin: 20px 0;
      border-radius: 12px;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    }

    .header {
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }

    th, td {
      padding: 10px;
      border-bottom: 1px solid #ccc;
    }

    th {
      background-color: #f4f7fb;
    }

    .filters form {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
    }

    .filters select, .filters input, .filters button {
      padding: 8px 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }

    .filters button {
      background-color: var(--primary);
      color: white;
      border: none;
      cursor: pointer;
    }

    .filters button:hover {
      background-color: var(--accent);
    }

    .pending { color: orange; font-weight: bold; }
    .in-progress { color: #2980b9; font-weight: bold; }
    .resolved { color: green; font-weight: bold; }
    .rejected { color: red; font-weight: bold; }

    @media (max-width: 768px) {
      .sidebar {
        transform: translateX(-100%);
        z-index: 1000;
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
    <h2>👤 Welcome, <?php echo $_SESSION['user_name']; ?> (Admin)</h2>
    <a href="view_coordinator.php"><i data-lucide="users"></i> View Coordinators</a>
    <a href="report_center.php"><i data-lucide="bar-chart-3"></i> Report Center</a>
    <a href="download_complaints.php"><i data-lucide="download-cloud"></i> Download Complaints</a>
    <a href="add_category.php"><i data-lucide="plus-circle"></i> Add Category</a>
    <a href="add_coordinator.php"><i data-lucide="user-plus"></i> Add Coordinator</a>
    <a href="logout.php"><i data-lucide="log-out"></i> Logout</a>
  </div>

  <main id="main">
    <header>
      <button class="toggle-btn" onclick="toggleSidebar()">☰</button>
      <h1>Admin Dashboard</h1>
    </header>

    <div class="card-container">
  <div class="card">
    <h3><i data-lucide="users"></i> View Coordinators</h3>
    <p>You can view Coordinator according to the complaint & Coordinator can manage it</p>
    <a href="view_coordinator.php">View Coordinators →</a>
  </div>

  <div class="card">
    <h3><i data-lucide="download-cloud"></i> Download Complaints</h3>
    <p>Here you can download total complaints that are generated.</p>
    <a href="download_complaints.php">Download Complaints →</a>
  </div>

  <div class="card">
    <h3><i data-lucide="folder-plus"></i> Add Category</h3>
    <p>Here you can add a new complaint category.</p>
    <a href="add_category.php">Add Category →</a>
  </div>

  <div class="card">
    <h3><i data-lucide="user-plus"></i> Add Coordinator</h3>
    <p>Here you can add a new coordinator.</p>
    <a href="add_coordinator.php">Add Coordinator →</a>
  </div>

  <div class="card">
    <h3><i data-lucide="bar-chart-3"></i> Report Center</h3>
    <p>Here you can view reports of your complaints.</p>
    <a href="report_center.php">Report Center →</a>
  </div>
</div>

    <div class="container">
      <div class="header">
        <h1>Admin Dashboard - Complaint Monitoring</h1>
        <div class="filters">
          <form method="GET">
            <select name="status">
              <option value="">All Status</option>
              <option value="Pending" <?= $status_filter == 'Pending' ? 'selected' : '' ?>>Pending</option>
              <option value="In Progress" <?= $status_filter == 'In Progress' ? 'selected' : '' ?>>In Progress</option>
              <option value="Resolved" <?= $status_filter == 'Resolved' ? 'selected' : '' ?>>Resolved</option>
              <option value="Rejected" <?= $status_filter == 'Rejected' ? 'selected' : '' ?>>Rejected</option>
            </select>

            <input type="text" name="search" placeholder="Search Student or Category" value="<?= htmlspecialchars($search) ?>">

            <select name="sort">
              <option value="newest" <?= $sort_order == 'DESC' ? 'selected' : '' ?>>Newest First</option>
              <option value="oldest" <?= $sort_order == 'ASC' ? 'selected' : '' ?>>Oldest First</option>
            </select>

            <button type="submit">Apply</button>
            <a href="admin_dashboard.php"><button type="button">Reset</button></a>
          </form>
        </div>
      </div>

      <table>
        <thead>
          <tr>
            <th>Complaint ID</th>
            <th>User Name</th>
            <th>Category</th>
            <th>Description</th>
            <th>Status</th>
            <th>Date Submitted</th>
          </tr>
        </thead>
        <tbody>
          <?php while($row = mysqli_fetch_assoc($result1)): ?>
          <tr>
            <td><?= $row['CompID'] ?></td>
            <td><?= $row['User_Name'] ?></td>
            <td><?= $row['CategoryName'] ?></td>
            <td><?= $row['Description'] ?></td>
            <td class="<?= strtolower(str_replace(' ', '-', $row['Status'])) ?>"><?= $row['Status'] ?></td>
            <td><?= $row['DateSubmitted'] ?></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>

      <h3>Student Feedback</h3>
      <table>
        <thead>
          <tr>
            <th>Student</th>
            <th>Complaint</th>
            <th>Rating</th>
            <th>Comments</th>
            <th>Date Submitted</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = mysqli_fetch_assoc($result2)): ?>
          <tr>
            <td><?= $row['Student'] ?></td>
            <td><?= $row['Description'] ?></td>
            <td><?= $row['Rating'] ?>/5</td>
            <td><?= $row['Comment'] ?></td>
            <td><?= $row['DateSubmitted'] ?></td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </main>

  <script>
    function toggleSidebar() {
      document.getElementById('sidebar').classList.toggle('hidden');
      document.getElementById('main').classList.toggle('full');
    }

    lucide.createIcons(); // Initialize icons
  </script>
</body>
</html>

