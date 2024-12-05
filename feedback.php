<?php
session_start();
include "db_connection.php"; // Include PDO connection

// Check if the user is logged in as a client
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: client_login.php");
    exit();
}

// Get logged-in user ID
$user_id = $_SESSION['user_id'];

// Get designer_id from URL
$designer_id = isset($_GET['designer_id']) ? (int)$_GET['designer_id'] : null;

// Validate designer_id
if (!$designer_id) {
    die("Invalid designer ID.");
}

try {
    // Fetch the designer details
    $stmt = $pdo->prepare("
        SELECT designer_id, name 
        FROM interior_designer 
        WHERE designer_id = :designer_id
    ");
    $stmt->bindParam(':designer_id', $designer_id, PDO::PARAM_INT);
    $stmt->execute();
    $designer = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$designer) {
        die("Designer not found.");
    }

    $designer_name = $designer['name'];

    // Check if feedback already exists for this designer by this client
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM feedback WHERE user_id = :user_id AND designer_id = :designer_id");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':designer_id', $designer_id, PDO::PARAM_INT);
    $stmt->execute();
    $feedback_exists = $stmt->fetchColumn();

    if ($feedback_exists) {
        echo "<script>alert('You have already submitted feedback for this designer.'); window.location.href = 'client_orders.php';</script>";
        exit();
    }

    // Handle feedback form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : null;
        $comments = trim($_POST['comments']);

        // Validate rating
        if ($rating < 1 || $rating > 5) {
            die("Invalid rating. Please provide a rating between 1 and 5.");
        }

        try {
            // Insert feedback into the feedback table
            $sql = "INSERT INTO feedback (user_id, designer_id, rating, comments, feedback_date) 
                    VALUES (:user_id, :designer_id, :rating, :comments, NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':designer_id', $designer_id, PDO::PARAM_INT);
            $stmt->bindParam(':rating', $rating, PDO::PARAM_INT);
            $stmt->bindParam(':comments', $comments, PDO::PARAM_STR);
            $stmt->execute();

            echo "<script>alert('Feedback submitted successfully!'); window.location.href = 'client_orders.php';</script>";
            exit();
        } catch (PDOException $e) {
            die("Database Error: " . $e->getMessage());
        }
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Feedback</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .feedback__container {
            max-width: 600px;
            margin: 2rem auto;
            padding: 1rem;
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: #f9f9f9;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .feedback__group {
            margin-bottom: 1rem;
        }

        .feedback__group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
        }

        .feedback__group input[type="number"],
        .feedback__group textarea {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .feedback__group textarea {
            resize: vertical;
        }

        .feedback__group button {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }

        .feedback__group button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<?php include "header.php"; ?>
    <div class="feedback__container">
        <h2>Submit Feedback for Designer: <?php echo htmlspecialchars($designer_name); ?></h2>
        <form method="POST" action="">
            <div class="feedback__group">
                <label for="rating">Rating (1 to 5):</label>
                <input type="number" id="rating" name="rating" min="1" max="5" required>
            </div>
            <div class="feedback__group">
                <label for="comments">Comments:</label>
                <textarea id="comments" name="comments" rows="5" placeholder="Share your experience..." required></textarea>
            </div>
            <div class="feedback__group">
                <button type="submit">Submit Feedback</button>
            </div>
        </form>
    </div>
</body>
</html>
