<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // secure
    $role = $_POST['role']; // should be Student, Coordinator, or Admin

    if ($role === 'Student') {
        $sql = "INSERT INTO User (Name, Email, Address, PhoneNo, Password, Role) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $name, $email, $address, $phone, $password, $role);

    } elseif ($role === 'Coordinator') {
        $sql = "INSERT INTO Coordinator (Name, Email, Role, Password,address,Phone) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $fixedRole = 'Assigned Coordinator';
        $stmt->bind_param("ssssss", $name, $email, $fixedRole, $password, $address, $phone);

    } elseif ($role === 'Admin') {
        $sql = "INSERT INTO Admin (Name, Email, Role, Password,address,Phone) VALUES (?, ?, ?, ?, ?,?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $name, $email,$role, $password,$address,$phone);

    } else {
        echo "<script>alert('Invalid role selected.');</script>";
        exit;
    }

    if ($stmt->execute()) {
        echo "<script>alert('Registration successful! You can now login.');</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }
    $stmt->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Student Registration</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <!-- Font Awesome for icons -->
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
      background: url('https://images.unsplash.com/photo-1573497491208-6b1acb260507?auto=format&fit=crop&w=1400&q=80') no-repeat center center/cover;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      backdrop-filter: blur(5px);
    }

    .register-container {
      background: rgba(255, 255, 255, 0.95);
      padding: 40px 30px;
      border-radius: 15px;
      box-shadow: 0 12px 28px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 500px;
      animation: fadeIn 0.6s ease-in-out;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .register-container h2 {
      text-align: center;
      margin-bottom: 25px;
      color: var(--primary);
    }

    .input-group {
      position: relative;
      margin: 10px 0;
    }

    .input-group i {
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      color: var(--primary);
    }

    .input-group .fa-user,
    .input-group .fa-envelope,
    .input-group .fa-home,
    .input-group .fa-phone,
    .input-group .fa-lock,
    .input-group .fa-users {
      left: 15px;
    }

    .toggle-password {
      right: 15px;
      cursor: pointer;
      z-index: 1;
    }

    .input-group input,
    .input-group select {
      width: 100%;
      padding: 12px 40px 12px 40px;
      border: 1px solid #ccc;
      border-radius: 10px;
      font-size: 15px;
      margin-top: 5px;
    }

    .input-group input.password-input {
      padding-right: 45px;
    }

    form button {
      width: 100%;
      padding: 14px;
      background: linear-gradient(to right, var(--accent), var(--primary));
      border: none;
      border-radius: 10px;
      font-size: 16px;
      color: white;
      cursor: pointer;
      margin-top: 20px;
      transition: all 0.3s ease;
    }

    form button:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
    }

    .footer-text {
      text-align: center;
      margin-top: 20px;
      font-size: 14px;
      color: #777;
    }

    .footer-text a {
      color: var(--primary);
      text-decoration: none;
    }

    .footer-text a:hover {
      text-decoration: underline;
    }

    a.back-link {
      display: block;
      margin-top: 15px;
      text-align: center;
      color: var(--primary);
      text-decoration: none;
      font-size: 15px;
    }

    a.back-link:hover {
      text-decoration: underline;
    }

    #phone-error {
      font-size: 13px;
      margin-left: 10px;
      color: red;
    }
  </style>
</head>
<body>

  <div class="register-container">
    <h2><i class="fas fa-user-plus"></i> Registration Page</h2>
    <form action="" method="POST">
      
      <div class="input-group">
        <i class="fas fa-user"></i>
        <input type="text" name="name" placeholder="Full Name" required>
      </div>

      <div class="input-group">
        <i class="fas fa-envelope"></i>
        <input type="email" name="email" placeholder="Email" required>
      </div>

      <div class="input-group">
        <i class="fas fa-home"></i>
        <input type="text" name="address" placeholder="Address" required>
      </div>

      <div class="input-group">
        <i class="fas fa-phone"></i>
        <input type="text" name="phone" id="phone" maxlength="10" placeholder="Phone" required oninput="validatePhone()" />
        <span id="phone-error"></span>
      </div>

      <div class="input-group">
        <i class="fas fa-lock"></i>
        <input type="password" name="password" id="password" class="password-input" placeholder="Password" required>
        <i class="fas fa-eye toggle-password" onclick="toggleVisibility('password', this)"></i>
      </div>

      <div class="input-group">
        <i class="fas fa-lock"></i>
        <input type="password" name="confirm_password" id="confirm-password" class="password-input" placeholder="Confirm Password" required>
        <i class="fas fa-eye toggle-password" onclick="toggleVisibility('confirm-password', this)"></i>
      </div>

      <div class="input-group">
        <i class="fas fa-users"></i>
        <select name="role" required>
          <option value="">Select Role</option>
          <option value="Student">Student</option>
          <option value="Coordinator">Coordinator</option>
          <option value="Admin">Admin</option>
        </select>
      </div>

      <button type="submit"><i class="fas fa-paper-plane"></i> Register</button>
    </form>

    <div class="footer-text">
      Already registered? <a href="login.php">Login here</a>
    </div>
    
    <a href="landing_page.html" class="back-link">← Back to Landing page</a>
  </div>
  
  <script>
    function toggleVisibility(fieldId, icon) {
      const field = document.getElementById(fieldId);
      if (field.type === "password") {
        field.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
      } else {
        field.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
      }
    }

    function validatePhone() {
      const phoneInput = document.getElementById("phone");
      const errorMsg = document.getElementById("phone-error");
      
      if (phoneInput.value.length < 10) {
          errorMsg.textContent = "Phone number must be exactly 10 digits.";
      } else {
          errorMsg.textContent = "";
      }
    }
  </script>

</body>
</html>

