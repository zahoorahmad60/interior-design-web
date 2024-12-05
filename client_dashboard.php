<?php
session_start();


$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Client Dashboard</title>
    <style>
        .dashboard__container {
            max-width: 800px;
            margin: 50px auto;
            text-align: center;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: var(--primary-color);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .dashboard__container h2 {
            font-family: var(--header-font);
            font-size: 2rem;
            color: var(--text-dark);
            margin-bottom: 20px;
        }

        .dashboard__container p {
            color: var(--text-light);
            margin-bottom: 20px;
        }

        .dashboard__buttons {
            display: flex;
            justify-content: space-around;
            gap: 10px;
        }

        .dashboard__buttons a {
            display: inline-block;
            padding: 10px 20px;
            text-decoration: none;
            color: white;
            background-color: #007bff;
            border-radius: 5px;
            font-size: 1rem;
            transition: background-color 0.3s;
        }

        .dashboard__buttons a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<?php include "header.php"; ?>
    <div class="dashboard__container">
        <h2>Welcome to Your Dashboard, Client</h2>
        <p>Choose an option to get started:</p>
        <div class="dashboard__buttons">
            <a href="projects.php">View Projects</a>
            <a href="client_see_chats.php">View Chats with Designers</a>
            <a href="client_profile.php">View/Edit Profile</a>
            <a href="client_orders.php">See order Status</a>

        </div>
    </div>
</body>
</html>
