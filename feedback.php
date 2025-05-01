<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'Student') {
    header("Location: login.php");
    exit();
}

include 'db_connect.php';

$user_id = $_SESSION['user_id'];

// Fetch resolved complaints
$query = "SELECT c.CompID, c.Description 
          FROM Complaints c 
          WHERE c.UserID = ? AND c.Status = 'Resolved' 
          AND c.CompID NOT IN (SELECT CompID FROM Feedback)";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $comp_id = $_POST['comp_id'];
    $rating = $_POST['rating'];
    $comments = $_POST['comments'];

    // Insert feedback
    $query = "INSERT INTO Feedback (CompID, UserID, Rating, Comment) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iiis", $comp_id, $user_id, $rating, $comments);

    if ($stmt->execute()) {
        echo "Feedback submitted successfully!";
    } else {
        echo "Error submitting feedback.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Feedback</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        padding: 20px;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
    }

    .form-container {
        max-width: 500px;
        margin: 40px auto;
        padding: 30px;
        background: white;
        border-radius: 15px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.12);
        transition: transform 0.2s ease;
    }

    .form-container:hover {
        transform: translateY(-2px);
    }

    h2 {
        color: #cf711b;
        margin-bottom: 25px;
        font-weight: 600;
        font-size: 1.8em;
        text-align: center;
    }

    .form-group {
        margin-bottom: 25px;
        text-align: left;
    }

    label {
        display: block;
        margin-bottom: 8px;
        color: #cf711b;
        font-weight: 500;
        font-size: 0.95em;
    }

    select, input[type="number"], textarea {
        width: 100%;
        padding: 12px;
        border: 2px solid #e0e6ed;
        border-radius: 8px;
        font-size: 16px;
        transition: border-color 0.3s ease;
    }

    select:focus, input[type="number"]:focus, textarea:focus {
        border-color: #cf711b;
        outline: none;
    }

    textarea {
        resize: vertical;
        min-height: 100px;
    }

    .btn {
        padding: 12px 30px;
        background: #cf711b;
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
        width: 100%;
        margin-top: 15px;
    }

    .btn:hover {
        background: #2980b9;
        box-shadow: 0 4px 15px rgba(52,152,219,0.3);
    }

    .back-btn {
        margin-top: 20px;
        background: #cf711b;
        padding: 10px 25px;
        border-radius: 8px;
        transition: all 0.3s ease;
        display: inline-block;
        cursor: pointer;
    }

    .back-btn:hover {
        background: #7f8c8d;
    }

    .icon {
        margin-right: 8px;
    }

    @media (max-width: 576px) {
        body {
            padding: 15px;
        }
        
        .form-container {
            margin: 20px 0;
            padding: 20px;
        }
        
        h2 {
            font-size: 1.5em;
        }
    }
</style>
</head>
<body>

<div class="form-container">
    <h2><i class="fas fa-comment-alt icon"></i> Submit Feedback</h2>
    <form method="POST">
        <div class="form-group">
            <label for="comp_id"><i class="fas fa-list icon"></i> Select Resolved Complaint:</label>
            <select name="comp_id" required>
                <option value="">-- Select Complaint --</option>
                <?php while($row = $result->fetch_assoc()): ?>
                    <option value="<?= $row['CompID']; ?>">
                        <?= "Complaint #" . $row['CompID'] . ": " . htmlspecialchars($row['Description']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="rating"><i class="fas fa-star icon"></i> Rate Your Experience (1-5):</label>
            <input type="number" name="rating" min="1" max="5" required>
        </div>

        <div class="form-group">
            <label for="comments"><i class="fas fa-comment-dots icon"></i> Comments (Optional):</label>
            <textarea name="comments" rows="4"></textarea>
        </div>

        <button type="submit" class="btn"><i class="fas fa-paper-plane icon"></i> Submit Feedback</button>
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
