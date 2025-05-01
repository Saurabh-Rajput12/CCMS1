<?php
$host = "localhost";  // XAMPP runs MySQL on localhost
$user = "root";       // Default MySQL user in XAMPP
$password = "";       // Default MySQL password is empty
$database = "College_Complaint_DB"; // Your database name

// Create connection
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    // echo "Database connected successfully"; // Uncomment for testing
}
?>
