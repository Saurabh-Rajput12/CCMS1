<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $found = false;

    // 1. Check User (Student)
    $sql = "SELECT UserID, Name, Email, Password FROM User WHERE Email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['Password'])) {
            $_SESSION['user_id'] = $row['UserID'];
            $_SESSION['user_name'] = $row['Name'];
            $_SESSION['user_email'] = $row['Email'];
            $_SESSION['user_role'] = 'Student';
            header("Location: student_dashboard.php");
            exit;
        }
        $found = true;
    }

    // 2. Check Coordinator
    if (!$found) {
        $sql = "SELECT CoordID, Name, Email, Password FROM Coordinator WHERE Email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result2 = $stmt->get_result();

        if ($result2 && $result2->num_rows === 1) {
            $row = $result2->fetch_assoc();
            if (password_verify($password, $row['Password'])) {
                $_SESSION['user_id'] = $row['CoordID'];
                $_SESSION['user_name'] = $row['Name'];
                $_SESSION['user_email'] = $row['Email'];
                $_SESSION['user_role'] = 'Coordinator';
                header("Location: coordinator_dashboard.php");
                exit;
            }
            $found = true;
        }
    }

    // 3. Check Admin
    // 3. Check Admin
if (!$found) {
  $sql = "SELECT AdminID, Name, Email, Password FROM Admin WHERE Email = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result3 = $stmt->get_result();

  if ($result3 && $result3->num_rows === 1) {
      $row = $result3->fetch_assoc();

      if (password_verify($password, $row['Password'])) {
          session_regenerate_id(true); // ✅ optional, for security
          $_SESSION['user_id'] = $row['AdminID'];         // general user ID
          $_SESSION['user_name'] = $row['Name'];
          $_SESSION['user_email'] = $row['Email'];
          $_SESSION['user_role'] = 'Admin';
          $_SESSION['admin_id'] = $row['AdminID'];        // ✅ crucial for admin report generation

          header("Location: admin_dashboard.php");
          exit;
      }
      $found = true;
  }
}

    echo "<script>alert('Invalid email or password.');</script>";
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login - College Complaint Management</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --primary: #185a9d;
      --accent: #43cea2;
      --bg: #f2f7ff;
      --text-dark: #333;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Segoe UI', sans-serif;
      background: url('https://img.freepik.com/free-vector/complaint-management-concept-illustration_114360-8866.jpg') no-repeat center center/cover;
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      backdrop-filter: blur(5px);
    }

    .login-box {
      background: rgba(255, 255, 255, 0.95);
      padding: 40px 30px;
      border-radius: 15px;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
      width: 100%;
      max-width: 400px;
      animation: slideIn 0.6s ease-in-out;
    }

    @keyframes slideIn {
      from {
        transform: translateY(30px);
        opacity: 0;
      }
      to {
        transform: translateY(0);
        opacity: 1;
      }
    }

    h2 {
      text-align: center;
      color: var(--primary);
      margin-bottom: 30px;
    }

    .input-group {
      position: relative;
      margin: 15px 0;
    }

    .input-group i.fa-envelope,
    .input-group i.fa-lock {
      position: absolute;
      left: 12px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--primary);
    }

    .input-group input {
      width: 100%;
      padding: 12px 40px 12px 40px; /* padding left and right for icons */
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 15px;
    }

    .input-group .toggle-password {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: var(--primary);
    }

    button[type="submit"] {
      width: 100%;
      padding: 14px;
      background: linear-gradient(to right, var(--accent), var(--primary));
      border: none;
      border-radius: 8px;
      font-size: 16px;
      color: white;
      cursor: pointer;
      margin-top: 20px;
      transition: all 0.3s ease;
    }

    button[type="submit"]:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    }

    .back-link {
      display: block;
      text-align: center;
      margin-top: 15px;
      font-size: 14px;
      color: var(--primary);
      text-decoration: none;
    }

    .back-link:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

  <div class="login-box">
    <h2><i class="fas fa-sign-in-alt"></i> Login</h2>

    <form action="" method="POST">
      <div class="input-group">
        <i class="fas fa-envelope"></i>
        <input type="email" name="email" placeholder="Enter Email" required>
      </div>

      <div class="input-group">
        <i class="fas fa-lock"></i>
        <input type="password" id="password" name="password" placeholder="Enter Password" required>
        <i class="fas fa-eye toggle-password" onclick="togglePassword()"></i>
      </div>

      <button type="submit"><i class="fas fa-arrow-right-to-bracket"></i> Login</button>
    </form>

    <a href="register.php" class="back-link">← Back to Register Page</a>
  </div>

  <script>
    function togglePassword() {
      const passwordInput = document.getElementById('password');
      const toggleIcon = document.querySelector('.toggle-password');

      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
      } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
      }
    }
  </script>

</body>
</html>
