<?php
include 'db_connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cat_name = trim($_POST['cat_name']);
    $description = trim($_POST['description']);

    if (!empty($cat_name) && !empty($description)) {
        $stmt = $conn->prepare("INSERT INTO Category (Cat_Name, Description) VALUES (?, ?)");
        $stmt->bind_param("ss", $cat_name, $description);

        if ($stmt->execute()) {
            echo "<script>alert('Category added successfully!'); window.location='add_category.php';</script>";
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
    <title>Add New Complaint Category</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            margin: 0;
            min-height: 100vh;
            background: #f0f2f5;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 2rem;
            animation: fadeIn 0.6s ease-in-out;
        }

        h2 {
            color: #1a1a1a;
            margin-bottom: 2rem;
            font-size: 1.8rem;
            text-align: center;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #007bff;
        }

        form {
            background: white;
            padding: 2rem 2.5rem;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 500px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        form:hover {
            transform: scale(1.02);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        }

        .input-group {
            margin-bottom: 1.5rem;
        }

        label {
            font-weight: 600;
            color: #404040;
            margin-bottom: 0.5rem;
            display: block;
        }

        .input-icon-wrapper {
            display: flex;
            align-items: center;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            background-color: #fff;
            padding: 0 0.75rem;
        }

        .input-icon-wrapper i {
            color: #a0aec0;
            margin-right: 0.6rem;
            font-size: 1rem;
        }

        .input-icon-wrapper input,
        .input-icon-wrapper textarea {
            border: none;
            outline: none;
            padding: 0.75rem 0;
            font-size: 1rem;
            width: 100%;
            font-family: inherit;
            resize: none;
            background: transparent;
        }

        .input-icon-wrapper:focus-within {
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.15);
        }

        button {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(to right, #0066ff, #0051ff);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            margin-top: 1.5rem;
            transition: all 0.2s ease;
        }

        button:hover {
            background: linear-gradient(to right, #0051ff, #0040ff);
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0, 123, 255, 0.3);
        }

        a {
            display: inline-block;
            margin: 1.5rem 0;
            padding: 0.8rem 1.5rem;
            background: #f0f0f0;
            color: #404040;
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.2s ease;
            font-weight: 500;
        }

        a:hover {
            background: #e0e0e0;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 1rem;
            }

            form {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>

<h2><i class="fas fa-plus-circle"></i> Add New Complaint Category</h2>

<form method="POST">
    <div class="input-group">
        <label for="cat_name">Category Name:</label>
        <div class="input-icon-wrapper">
            <i class="fas fa-tags"></i>
            <input type="text" name="cat_name" id="cat_name" required>
        </div>
    </div>

    <div class="input-group">
        <label for="description">Description:</label>
        <div class="input-icon-wrapper">
            <i class="fas fa-pen"></i>
            <textarea name="description" id="description" rows="4" required></textarea>
        </div>
    </div>

    <button type="submit"><i class="fas fa-check-circle"></i> Add Category</button>
</form>

<a href="admin_dashboard.php"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>

</body>
</html>

