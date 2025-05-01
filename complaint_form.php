<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'Student') {
    header("Location: login.php");
    exit();
}
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id']; // Get logged-in student's ID
    $category = $_POST['category']; // Complaint category
    $description = $_POST['description']; // Complaint description
    $status = 'Pending'; // Default status when submitted

    // Retrieve the CatID for the selected category
    $category_query = "SELECT CatID FROM category WHERE Cat_Name = ?";
    $stmt = mysqli_prepare($conn, $category_query);
    mysqli_stmt_bind_param($stmt, "s", $category);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $cat_id);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if ($cat_id) {
        // Insert the complaint into the complaints table
        $sql = "INSERT INTO complaints (UserID, CatID, Description, Status, DateSubmitted) 
                VALUES (?, ?, ?, ?, NOW())";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iiss", $user_id, $cat_id, $description, $status);

        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Complaint submitted successfully!'); window.location.href='student_dashboard.php';</script>";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "<script>alert('Invalid category selected.');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Submit Complaint</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
  <style>
    /* Global styles */
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background-image: url('https://www.example.com/your-background-image.jpg');
      background-size: cover;
      background-position: center;
      color: #fff;
    }

    h2 {
      color: #2c3e50;
      margin-bottom: 30px;
    }

    /* Centered container for form */
    .form-container {
      width: 100%;
      max-width: 700px;
      margin: 0 auto;
      padding: 50px;
      background-color: rgba(255, 255, 255, 0.9); /* Semi-transparent background */
      border-radius: 10px;
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
    }

    /* Form styling */
    form {
      display: flex;
      flex-direction: column;
    }

    label {
      font-size: 16px;
      font-weight: bold;
      margin-bottom: 8px;
      color: #34495e;
    }

    select, textarea {
      width: 100%;
      padding: 12px;
      margin-bottom: 20px;
      border: 1px solid #ccc;
      border-radius: 6px;
      background-color: #fff;
      font-size: 14px;
      color: #34495e;
    }

    textarea {
      resize: vertical;
    }

    .btn {
      padding: 12px 20px;
      background-color: #3498db;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 16px;
      transition: background 0.3s ease;
    }

    .btn:hover {
      background-color: #2980b9;
    }

    .back-btn {
      margin-top: 20px;
      padding: 12px 20px;
      background-color: #e74c3c;
      color: white;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 16px;
      transition: background 0.3s ease;
    }

    .back-btn:hover {
      background-color: #c0392b;
    }

    /* Back link styling */
    .back-link {
      display: inline-block;
      margin-top: 20px;
      text-decoration: none;
      color: #2980b9;
      font-weight: bold;
    }

    .back-link:hover {
      text-decoration: underline;
    }

    /* Icons */
    .icon {
      margin-right: 10px;
    }

    /* Responsive Design */
    @media (max-width: 600px) {
      .form-container {
        padding: 30px;
      }

      h2 {
        font-size: 24px;
      }

      .btn, .back-btn {
        width: 100%;
      }
    }

  </style>
</head>
<body>

  <div class="form-container">
    <h2><i class="fas fa-pencil-alt icon"></i> Submit a New Complaint</h2>

    <form action="complaint_form.php" method="post">
      <input type="hidden" name="student_id" value="<?= $studentId ?>">

      <label for="category"><i class="fas fa-cogs icon"></i> Category</label>
      <select name="category" id="category" required>
        <option value="">-- Select Category --</option>
        <option value="Academic">Academic</option>
        <option value="Administration">Administration</option>
        <option value="Facilities">Facilities</option>
        <option value="Hostel">Hostel</option>
        <option value="Library">Library</option>
        <option value="Transport">Transport</option>
      </select>

      <label for="description"><i class="fas fa-comment-dots icon"></i> Complaint Description</label>
      <textarea name="description" id="description" rows="5" required></textarea>

      <button type="submit" class="btn"><i class="fas fa-paper-plane icon"></i> Submit Complaint</button>
    </form>

    <button class="back-btn" onclick="goBack()"><i class="fas fa-arrow-left icon"></i> Back</button>
  </div>

  <script>
    function goBack() {
      window.history.back();
    }
  </script>

</body>
</html>
