<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: client_login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];
$designer_id = isset($_SESSION['designer_id']) ? $_SESSION['designer_id'] : null; // For designers
?>

<!DOCTYPE html>
<html lang="en">
<head>
<?php include "header.php"; ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Login Successful</title>
    <style>
        .options__container {
            max-width: 600px;
            margin: 50px auto;
            text-align: center;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: var(--primary-color);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .options__container h2 {
            font-family: var(--header-font);
            font-size: 2rem;
            margin-bottom: 20px;
            color: var(--text-dark);
        }

        .options__container p {
            font-size: 1rem;
            margin-bottom: 20px;
            color: var(--text-light);
        }

        .options__buttons {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .options__buttons .button {
            display: inline-block;
            padding: 10px 20px;
            font-size: 1rem;
            color: white;
            background-color: #007bff;
            text-decoration: none;
            border-radius: 5px;
            transition: 0.3s;
        }

        .options__buttons .button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="options__container">
        <?php if ($role === 'client'): ?>
            <h2>Welcome, Client</h2>
            <p>You have successfully logged in. Please choose an option:</p>
            <div class="options__buttons">
                <a href="projects.php" class="button">Go to Projects Page</a>
                <a href="chat_list.php" class="button">View Chats</a>
            </div>
        <?php elseif ($role === 'designer'): ?>
            <h2>Welcome, Designer</h2>
            <p>You have successfully logged in. Please choose an option:</p>
            <div class="options__buttons">
                <a href="add_project.php" class="button">Add New Project</a>
                <a href="see_chats.php" class="button">View Chats with Clients</a>
            </div>
        <?php else: ?>
            <h2>Unauthorized Access</h2>
            <p>You are not authorized to access this page. Please log in again.</p>
        <?php endif; ?>
    </div>
</body>
</html>
