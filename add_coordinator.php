<?php
include 'db_connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $role     = trim($_POST['role']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $address  = trim($_POST['address']);
    $phone    = trim($_POST['phone']);
    

    if (!empty($name) && !empty($email) && !empty($role) && !empty($password) && !empty($address) && !empty($phone)) {
        $stmt = $conn->prepare("INSERT INTO Coordinator (Name, Email, Role, Password, Address, Phone) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $name, $email, $role, $password, $address, $phone);

        if ($stmt->execute()) {
            echo "<script>alert('Coordinator added successfully!'); window.location='add_coordinator.php';</script>";
        } else {
            echo "<script>alert('Error: " . $stmt->error . "');</script>";
        }
    } else {
        echo "<script>alert('Please fill in all fields.');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Coordinator</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        padding: 40px;
        background-color: #f5f7fa;
    }

    h2 {
        color: #2c3e50;
        margin-bottom: 30px;
        font-size: 2.2em;
        border-bottom: 3px solid #3498db;
        padding-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    h2 i {
        color: #3498db;
    }

    form {
        max-width: 600px;
        margin: 0 auto;
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        transition: transform 0.2s ease;
    }

    form:hover {
        transform: translateY(-3px);
    }

    .field {
        margin-bottom: 20px;
    }

    .field label {
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 6px;
        color: #34495e;
        font-size: 0.95em;
    }

    .field input, .field select, .field textarea {
        width: 100%;
        padding: 12px;
        border: 2px solid #e0e4e9;
        border-radius: 8px;
        font-size: 1em;
        transition: all 0.3s ease;
    }

    .field input:focus, .field select:focus, .field textarea:focus {
        border-color: #3498db;
        box-shadow: 0 0 0 3px rgba(52,152,219,0.1);
        outline: none;
    }

    button {
        margin-top: 30px;
        padding: 14px 30px;
        background-color: #3498db;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 1.1em;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        width: 100%;
        letter-spacing: 0.5px;
    }

    button:hover {
        background-color: #2980b9;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(52,152,219,0.3);
    }

    a {
        display: inline-block;
        margin-top: 25px;
        color: #3498db;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    a:hover {
        color: #2980b9;
        transform: translateX(-3px);
    }

    textarea {
        resize: vertical;
        min-height: 80px;
    }

    @media (max-width: 768px) {
        body {
            padding: 20px;
        }

        form {
            padding: 20px;
        }
    }
</style>
</head>
<body>

<h2><i class="fas fa-user-plus"></i> Add New Coordinator</h2>

<form method="POST">
    <div class="field">
        <label><i class="fas fa-user"></i> Name:</label>
        <input type="text" name="name" required>
    </div>

    <div class="field">
        <label><i class="fas fa-envelope"></i> Email:</label>
        <input type="email" name="email" required>
    </div>

    <div class="field">
        <label><i class="fas fa-clipboard-list"></i> Role:</label>
        <select name="role" required>
            <option value="Category Coordinator">Category Coordinator</option>
        </select>
    </div>

    <div class="field">
        <label><i class="fas fa-lock"></i> Password:</label>
        <input type="password" name="password" required>
    </div>

    <div class="field">
        <label><i class="fas fa-map-marker-alt"></i> Address:</label>
        <textarea name="address" rows="2" required></textarea>
    </div>

    <div class="field">
        <label><i class="fas fa-phone"></i> Phone:</label>
        <input type="text" name="phone" required>
    </div>

    <button type="submit"><i class="fas fa-check-circle"></i> Add Coordinator</button>
</form>

<a href="admin_dashboard.php"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>

</body>
</html>


