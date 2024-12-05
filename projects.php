<?php
session_start(); // Start session to check user login status
include "db_connection.php"; // Include the database connection

try {
    // Fetch projects and related designer details
    $sql = "SELECT 
                projects.project_id, 
                projects.title, 
                projects.description, 
                projects.image, 
                projects.date_created, 
                projects.designer_id, 
                interior_designer.name AS designer_name
            FROM projects
            JOIN interior_designer ON projects.designer_id = interior_designer.designer_id";

    $stmt = $pdo->query($sql); // Use the $pdo object from db_connection.php
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all results as an associative array

    // Fetch average ratings for each designer
    $ratings_sql = "SELECT designer_id, AVG(rating) AS avg_rating 
                    FROM feedback 
                    GROUP BY designer_id";
    $ratings_stmt = $pdo->query($ratings_sql);
    $ratings = $ratings_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Create an associative array of ratings for quick lookup
    $designer_ratings = [];
    foreach ($ratings as $rating) {
        $designer_ratings[$rating['designer_id']] = number_format((float)$rating['avg_rating'], 1); // Format to 1 decimal place
    }
} catch (PDOException $e) {
    // Handle any errors
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Projects</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* CSS styles for the project cards */
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

        .section__container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .section__container h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333333;
        }

        .projects__list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: flex-start;
        }

        .project__card {
            flex: 0 0 calc(33.333% - 20px);
            max-width: calc(33.333% - 20px);
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        @media screen and (max-width: 992px) {
            .project__card {
                flex: 0 0 calc(50% - 20px);
                max-width: calc(50% - 20px);
            }
        }

        @media screen and (max-width: 600px) {
            .project__card {
                flex: 0 0 100%;
                max-width: 100%;
            }
        }

        .project__card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .project__content {
            padding: 20px;
            flex-grow: 1;
        }

        .project__content h3 {
            font-size: 1.5rem;
            color: #333;
            margin-bottom: 1rem;
        }

        .project__content p {
            font-size: 1rem;
            color: #555;
            margin-bottom: 1rem;
            height: 60px;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .project__content small {
            font-size: 0.9rem;
            color: #777;
        }

        .project__actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            padding: 10px 20px;
            background-color: #ffffff;
            border-top: 1px solid #ddd;
        }

        .project__actions a {
            flex: 1 1 48%;
            margin: 5px 1%;
            text-align: center;
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            font-size: 0.9rem;
        }

        .project__actions a:hover {
            background-color: #0056b3;
        }

        /* Optional: Different button colors for different actions */
        .project__actions .btn-chat {
            background-color: #28a745;
        }

        .project__actions .btn-chat:hover {
            background-color: #1e7e34;
        }

        .project__actions .btn-profile {
            background-color: #17a2b8;
        }

        .project__actions .btn-profile:hover {
            background-color: #117a8b;
        }

    </style>
</head>
<body>
    <?php include "header.php"; ?>
    <div class="section__container">
        <h2>All Projects</h2>
        <div class="projects__list">
            <?php
            if (!empty($projects)) {
                foreach ($projects as $project) {
                    // Ensure required fields exist
                    if (isset($project["project_id"], $project["designer_id"], $project["title"], $project["image"], $project["designer_name"])) {
                        // Format the creation date
                        $formatted_date = date("F j, Y", strtotime($project["date_created"]));

                        // Get average rating or default message
                        $avg_rating = isset($designer_ratings[$project['designer_id']]) ? $designer_ratings[$project['designer_id']] . "/5" : "Not Rated Yet";

                        echo "<div class='project__card'>";
                            // Project Image
                            echo "<img src='uploads/" . htmlspecialchars($project["image"]) . "' alt='" . htmlspecialchars($project["title"]) . "'>";

                            // Project Content
                            echo "<div class='project__content'>";
                                echo "<h3>" . htmlspecialchars($project["title"]) . "</h3>";
                                echo "<p><strong>Designer:</strong> " . htmlspecialchars($project["designer_name"]) . "</p>";
                                echo "<p><strong>Rating:</strong> " . htmlspecialchars($avg_rating) . "</p>";
                                echo "<p>" . htmlspecialchars($project["description"]) . "</p>";
                                echo "<small>Created on: " . htmlspecialchars($formatted_date) . "</small>";
                            echo "</div>";

                            // Project Actions
                            echo "<div class='project__actions'>";
                                // View More Details Button
                                echo "<a href='view_project.php?project_id=" . urlencode($project["project_id"]) . "' class='btn-details'>View More Details</a>";

                                // Chat with Designer or Login to Chat Button
                                if (isset($_SESSION['role']) && $_SESSION['role'] === 'client') {
                                    echo "<a href='client_chat.php?designer_id=" . urlencode($project["designer_id"]) . "' class='btn-chat'>Chat with Designer</a>";
                                } else {
                                    echo "<a href='client_login.php' class='btn-chat'>Login to Chat</a>";
                                }

                                // View Designer Profile Button
                                echo "<a href='client_designer.php?designer_id=" . urlencode($project["designer_id"]) . "' class='btn-profile'>View Designer Profile</a>";
                            echo "</div>";
                        echo "</div>";
                    } else {
                        echo "<p>Error: Missing project details.</p>";
                    }
                }
            } else {
                echo "<p>No projects found.</p>";
            }
            ?>
        </div>
    </div>
</body>
</html>
