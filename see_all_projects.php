<?php
session_start();
include "db_connection.php"; // Includes PDO connection as $pdo

// Ensure the user is logged in as a designer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'designer') {
    header("Location: designer_login.php");
    exit();
}

// Fetch all projects for the logged-in designer
$user_id = $_SESSION['user_id'];

try {
    // Fetch designer_id for the logged-in designer
    $sql = "SELECT designer_id FROM interior_designer WHERE user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $designer = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$designer) {
        die("Error: No designer profile found for the logged-in user.");
    }

    $designer_id = $designer['designer_id'];

    // Fetch all projects for this designer
    $sql = "SELECT * FROM projects WHERE designer_id = :designer_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':designer_id', $designer_id, PDO::PARAM_INT);
    $stmt->execute();
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching projects: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>All Projects</title>
    <style>
        .projects__container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 1rem;
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: #f9f9f9;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .project__item {
            border-bottom: 1px solid #ddd;
            padding: 1rem 0;
        }

        .project__item:last-child {
            border-bottom: none;
        }

        .project__title {
            font-size: 1.2rem;
            font-weight: bold;
        }

        .project__description {
            margin: 0.5rem 0;
        }

        .project__actions {
            margin-top: 1rem;
        }

        .btn {
            padding: 8px 16px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: 0.3s;
        }

        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <?php include "header.php"; ?>
    <div class="projects__container">
        <h2>All Projects</h2>
        <?php if (empty($projects)): ?>
            <p>No projects found. <a href="add_project.php" class="btn">Add a New Project</a></p>
        <?php else: ?>
            <?php foreach ($projects as $project): ?>
                <div class="project__item">
                    <p class="project__title"><?= htmlspecialchars($project['title']) ?></p>
                    <p class="project__description"><?= htmlspecialchars($project['description']) ?></p>
                    <?php if ($project['image']): ?>
                        <img src="uploads/<?= htmlspecialchars($project['image']) ?>" alt="Project Image" style="max-width: 100%; height: auto;">
                    <?php endif; ?>
                    <div class="project__actions">
                        <a href="designer_edit.php?project_id=<?= $project['project_id'] ?>" class="btn">Edit Project</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
