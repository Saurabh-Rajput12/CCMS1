<?php
include 'db_connect.php';
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Coordinators</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
    body { 
        font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; 
        margin: 40px auto;
        max-width: 1200px;
        color: #333;
        background-color: #f4f6f9;
    }
    .container {
        padding: 30px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 15px rgba(0,0,0,0.1);
    }
    .header {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 25px;
    }
    .back-link {
        background: #3498db;
        color: white;
        padding: 10px 16px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 0.95em;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .back-link:hover {
        background: #2980b9;
        transform: translateY(-1px);
        box-shadow: 0 3px 12px rgba(52,152,219,0.25);
    }
    h2 {
        color: #2c3e50;
        border-bottom: 3px solid #3498db;
        padding-bottom: 8px;
        font-size: 2em;
        margin: 0;
        flex-grow: 1;
    }
    table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        margin-top: 25px;
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 1px 10px rgba(0,0,0,0.05);
    }
    th, td {
        padding: 15px;
        text-align: left;
        border-bottom: 1px solid #ecf0f1;
    }
    th {
        background-color: #3498db;
        color: white;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.9em;
        letter-spacing: 0.8px;
    }
    tr:hover {
        background-color: #f8f9fa;
        transition: 0.3s;
    }
    tr:nth-child(even) {
        background-color: #fdfdfd;
    }
    a[href^="edit_coordinator"] {
        background: #27ae60;
        color: white;
        padding: 8px 15px;
        border-radius: 4px;
        transition: all 0.2s ease;
        font-size: 0.9em;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    a[href^="edit_coordinator"]:hover {
        background: #219a52;
        transform: translateY(-1px);
    }
    td:last-child {
        text-align: center;
    }
    tr:last-child td {
        border-bottom: none;
    }
    .badge {
        background: #dfe6e9;
        color: #2d3436;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 0.85em;
        font-weight: 500;
        display: inline-block;
    }
    @media (max-width: 768px) {
        body {
            margin: 20px;
        }
        .container {
            padding: 20px;
        }
        th, td {
            padding: 12px;
            font-size: 0.9em;
        }
        .header {
            flex-direction: column;
            align-items: flex-start;
        }
        h2 {
            font-size: 1.6em;
        }
    }
</style>
</head>
<body>

<div class="header">
    <a href="admin_dashboard.php" class="back-link">
        <i class="fas fa-arrow-left"></i> Back
    </a>
    <h2><i class="fas fa-users-cog"></i> List of Coordinators</h2>
</div>

<table>
    <thead>
        <tr>
            <th><i class="fas fa-id-badge"></i> ID</th>
            <th><i class="fas fa-user"></i> Name</th>
            <th><i class="fas fa-envelope"></i> Email</th>
            <th><i class="fas fa-user-tag"></i> Role</th>
            <th><i class="fas fa-phone"></i> Phone</th>
            <th><i class="fas fa-map-marker-alt"></i> Address</th>
            <th><i class="fas fa-building"></i> Department</th>
            <th><i class="fas fa-cogs"></i> Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $sql = "SELECT c.CoordID, c.Name, c.Email, c.Role, c.Phone, c.Address, cat.Cat_Name 
                FROM Coordinator c
                LEFT JOIN Category cat ON c.CatID = cat.CatID";

        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['CoordID']}</td>
                        <td>{$row['Name']}</td>
                        <td>{$row['Email']}</td>
                        <td>{$row['Role']}</td>
                        <td>{$row['Phone']}</td>
                        <td>{$row['Address']}</td>
                        <td><span class='badge'>{$row['Cat_Name']}</span></td>
                        <td><a href='edit_coordinator.php?id=" . $row['CoordID'] . "'><i class='fas fa-edit'></i> Edit</a></td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='8'>No coordinators found.</td></tr>";
        }
        ?>
    </tbody>
</table>

</body>
</html>

