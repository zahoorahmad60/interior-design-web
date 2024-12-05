<?php
session_start();

// Check if the user is logged in as a client
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: client_login.php");
    exit();
}

include "db_connection.php"; // Include the PDO database connection

try {
    // Get the logged-in client's user ID
    $user_id = $_SESSION['user_id'];

    // Fetch all unique designers from the `chats` table who interacted with the client
    $sql = "
        SELECT DISTINCT d.designer_id, u.username, u.email, u.location
        FROM chats c
        INNER JOIN interior_designer d ON c.designer_id = d.designer_id
        INNER JOIN user u ON d.user_id = u.user_id
        WHERE c.user_id = :user_id
        ORDER BY u.username ASC
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $designers = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all designers as an associative array
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Designer Chats</title>
    <style>
        .chat-list__container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 1rem;
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: var(--primary-color);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .chat-list__table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .chat-list__table th, .chat-list__table td {
            text-align: left;
            padding: 0.5rem;
            border-bottom: 1px solid #ddd;
        }

        .chat-list__table th {
            background-color: #f1f1f1;
        }

        .chat-list__table a.btn {
            padding: 0.5rem 1rem;
            text-decoration: none;
            color: white;
            background-color: #007bff;
            border-radius: 5px;
            transition: 0.3s;
        }

        .chat-list__table a.btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<?php include "header.php"; ?>
    <div class="chat-list__container">
        <h2>Designer Chats</h2>
        <table class="chat-list__table">
            <thead>
                <tr>
                    <th>Designer Name</th>
                    <th>Email</th>
                    <th>Location</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($designers)): ?>
                    <?php foreach ($designers as $designer): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($designer['username']); ?></td>
                            <td><?php echo htmlspecialchars($designer['email']); ?></td>
                            <td><?php echo htmlspecialchars($designer['location']); ?></td>
                            <td>
                                <a href="client_chat.php?designer_id=<?php echo htmlspecialchars($designer['designer_id']); ?>" class="btn">View Chat</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">No chats to display.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
