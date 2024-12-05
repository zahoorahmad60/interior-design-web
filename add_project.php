<?php
session_start();
include "db_connection.php"; // Includes PDO connection as $pdo

// Ensure the user is logged in as a designer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'designer') {
    header("Location: designer_login.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $user_id = $_SESSION['user_id']; // Get the logged-in designer's user ID

    // Fetch the designer_id from the interior_designer table
    try {
        $sql = "SELECT designer_id FROM interior_designer WHERE user_id = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $designer = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$designer) {
            die("Error: No designer profile found for the logged-in user.");
        }

        $designer_id = $designer['designer_id']; // Get the valid designer_id
    } catch (PDOException $e) {
        die("Error fetching designer ID: " . $e->getMessage());
    }

    // Handle file upload
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $image = basename($_FILES['image']['name']);
        $target_file = $target_dir . $image;
        move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
    }

    try {
        // Insert the new project into the database
        $sql = "INSERT INTO projects (title, description, image, designer_id, date_created) 
                VALUES (:title, :description, :image, :designer_id, NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        $stmt->bindParam(':image', $image, PDO::PARAM_STR);
        $stmt->bindParam(':designer_id', $designer_id, PDO::PARAM_INT);
        $stmt->execute();

        echo "<script>alert('Project added successfully!'); window.location.href = 'projects.php';</script>";
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Add Project</title>
    <style>
        .form__container {
            max-width: 600px;
            margin: 2rem auto;
            padding: 1rem;
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: #f9f9f9;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .form__group {
            margin-bottom: 1rem;
        }

        .form__group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
        }

        .form__group input, .form__group textarea {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .form__group button {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }

        .form__group button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <?php include "header.php"; ?>
    <div class="form__container">
        <h2>Add New Project</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="form__group">
                <label for="title">Project Title:</label>
                <input type="text" id="title" name="title" required>
            </div>
            <div class="form__group">
                <label for="description">Project Description:</label>
                <textarea id="description" name="description" rows="5" required></textarea>
            </div>
            <div class="form__group">
                <label for="image">Upload Image:</label>
                <input type="file" id="image" name="image" accept="image/*" required>
            </div>
            <div class="form__group">
                <button type="submit">Add Project</button>
            </div>
        </form>
    </div>
</body>
</html>
