<?php
session_start();
include "db_connection.php"; // Includes PDO connection as $pdo

// Validate and sanitize the project ID
$project_id = isset($_GET['project_id']) ? (int)$_GET['project_id'] : 0;

if ($project_id === 0) {
    die("Invalid project ID.");
}

try {
    // Fetch project details using PDO
    $sql = "
        SELECT 
            projects.title, 
            projects.description, 
            projects.image, 
            projects.date_created, 
            projects.designer_id, 
            interior_designer.name AS DesignerName, 
            interior_designer.experience, 
            user.email AS DesignerEmail
        FROM 
            projects
        JOIN 
            interior_designer ON projects.designer_id = interior_designer.designer_id
        JOIN 
            user ON interior_designer.user_id = user.user_id
        WHERE 
            projects.project_id = :project_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':project_id', $project_id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() === 1) {
        $project = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        die("Project not found.");
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
    <link rel="stylesheet" href="styles.css">
    <title>Project Details</title>
    <style>
        .project__container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 1rem;
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: #f9f9f9;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .project__image img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
            margin-bottom: 1rem;
        }

        .project__details h1 {
            font-family: Arial, sans-serif;
            font-size: 2rem;
            color: #333;
            margin-bottom: 1rem;
        }

        .project__details p {
            color: #555;
            line-height: 1.6;
            margin-bottom: 1rem;
        }

        .project__details strong {
            color: #222;
        }

        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: 0.3s;
            margin-top: 1rem;
        }

        .button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <?php include "header.php"; ?>
    <div class="project__container">
        <div class="project__image">
            <img src="uploads/<?php echo htmlspecialchars($project['image']); ?>" alt="<?php echo htmlspecialchars($project['title']); ?>">
        </div>
        <div class="project__details">
            <h1><?php echo htmlspecialchars($project['title']); ?></h1>
            <p><strong>Designer:</strong> <?php echo htmlspecialchars($project['DesignerName']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($project['DesignerEmail']); ?></p>
            <p><strong>Experience:</strong> <?php echo htmlspecialchars($project['experience']); ?></p>
            <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($project['description'])); ?></p>
            <p><strong>Created on:</strong> <?php echo htmlspecialchars($project['date_created']); ?></p>
            <a href="client_chat.php?designer_id=<?php echo htmlspecialchars($project['designer_id']); ?>" class="button">Chat with Designer</a>
            <a href="projects.php" class="button">Back to Projects</a>
        </div>
    </div>
</body>
</html>
