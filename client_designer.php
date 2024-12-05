<?php
session_start(); // Start session to check user login status
include "db_connection.php"; // Include the PDO connection

// =====================
// Client Authentication
// =====================

// Check if the user is logged in and has the 'client' role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    // User is not logged in as a client
    header("Location: client_login.php");
    exit();
}

// =====================
// Fetch Designer Details
// =====================

// Check if 'designer_id' is provided via GET parameters
if (!isset($_GET['designer_id']) || empty($_GET['designer_id'])) {
    die("Designer ID is missing.");
}

$designer_id = intval($_GET['designer_id']); // Sanitize input

try {
    // Fetch designer's basic information
    $sql = "SELECT u.username, u.email, d.experience
            FROM user u
            JOIN interior_designer d ON u.user_id = d.user_id
            WHERE d.designer_id = :designer_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':designer_id', $designer_id, PDO::PARAM_INT);
    $stmt->execute();
    $designer = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$designer) {
        die("Designer not found.");
    }

    // Fetch total number of projects
    $sql_projects = "SELECT COUNT(*) AS total_projects FROM projects WHERE designer_id = :designer_id";
    $stmt = $pdo->prepare($sql_projects);
    $stmt->bindParam(':designer_id', $designer_id, PDO::PARAM_INT);
    $stmt->execute();
    $projects = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_projects = $projects['total_projects'];

    // Fetch average rating
    $sql_ratings = "SELECT AVG(rating) AS average_rating FROM feedback WHERE designer_id = :designer_id";
    $stmt = $pdo->prepare($sql_ratings);
    $stmt->bindParam(':designer_id', $designer_id, PDO::PARAM_INT);
    $stmt->execute();
    $ratings = $stmt->fetch(PDO::FETCH_ASSOC);
    $average_rating = $ratings['average_rating'] ? number_format($ratings['average_rating'], 2) : "No ratings yet";

    // Fetch recent projects (optional)
    $sql_recent_projects = "SELECT title, description, date_created
                            FROM projects 
                            WHERE designer_id = :designer_id 
                            ORDER BY date_created DESC 
                            LIMIT 5";
    $stmt = $pdo->prepare($sql_recent_projects);
    $stmt->bindParam(':designer_id', $designer_id, PDO::PARAM_INT);
    $stmt->execute();
    $recent_projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Designer Profile - <?php echo htmlspecialchars($designer['username']); ?></title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Custom CSS for Designer Profile */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .header {
            background-color: #35424a;
            color: #ffffff;
            padding: 20px 0;
            text-align: center;
        }

        .profile-container {
            max-width: 800px;
            margin: 30px auto;
            background: #ffffff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        
        .profile-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .profile-header h2 {
            margin: 0;
            font-size: 2em;
            color: #333333;
        }

        .profile-header p {
            color: #777777;
            font-size: 1.1em;
        }

        .average-rating {
            font-size: 1.5em;
            color: #ff9900;
            text-align: center;
            margin-top: 20px;
        }

        .profile-actions {
            text-align: center;
            margin-top: 20px;
        }

        .profile-actions a {
            display: inline-block;
            margin: 10px 5px;
            padding: 10px 20px;
            background-color: #28a745;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            font-size: 1em;
        }

        .profile-actions a:hover {
            background-color: #218838;
        }

        .profile-details {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .detail {
            flex: 1 1 45%;
            margin-bottom: 20px;
        }

        .detail h3 {
            margin-bottom: 10px;
            color: #35424a;
        }

        .detail p {
            margin: 0;
            color: #555555;
        }

        .recent-projects {
            margin-top: 40px;
        }

        .recent-projects h3 {
            color: #35424a;
            margin-bottom: 15px;
        }

        .project {
            background: #f9f9f9;
            padding: 15px;
            margin-bottom: 10px;
            border-left: 4px solid #35424a;
        }

        .project h4 {
            margin: 0 0 5px 0;
            color: #333333;
        }

        .project p {
            margin: 0;
            color: #666666;
        }

        @media (max-width: 600px) {
            .detail {
                flex: 1 1 100%;
            }
        }
    </style>
</head>
<body>
    <?php include "header.php"; ?>

    <div class="profile-container">
        <div class="profile-header">
            <h2><?php echo htmlspecialchars($designer['username']); ?></h2>
            <p>Email: <?php echo htmlspecialchars($designer['email']); ?></p>
            <p>Experience: <?php echo nl2br(htmlspecialchars($designer['experience'])); ?></p>
            <p class="average-rating">Average Rating: <?php echo htmlspecialchars($average_rating); ?> / 5</p>
        </div>

        <!-- =====================
             Added Profile Actions
             ===================== -->
        <div class="profile-actions">
            <a href="client_chat.php?designer_id=<?php echo urlencode($designer_id); ?>" class="btn-interact">Interact with Designer</a>
            <!-- You can add more buttons here if needed -->
        </div>

        <div class="profile-details">
            <div class="detail">
                <h3>Total Projects</h3>
                <p><?php echo htmlspecialchars($total_projects); ?></p>
            </div>
            <div class="detail">
                <h3>Average Rating</h3>
                <p><?php echo htmlspecialchars($average_rating); ?> / 5</p>
            </div>
        </div>

        <div class="recent-projects">
            <h3>Recent Projects</h3>
            <?php if ($recent_projects): ?>
                <?php foreach ($recent_projects as $project): ?>
                    <div class="project">
                        <h4><?php echo htmlspecialchars($project['title']); ?></h4>
                        <p><?php echo nl2br(htmlspecialchars($project['description'])); ?></p>
                        <p><em>Completed on: <?php echo date("F j, Y", strtotime($project['date_created'])); ?></em></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No recent projects available.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
