<?php
session_start();
include "db_connection.php";

// Ensure the user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: client_login.php");
    exit();
}

// Validate and sanitize the project ID
$project_id = isset($_GET['project_id']) ? (int)$_GET['project_id'] : null;

if (!$project_id) {
    die("<p style='color: red;'>Error: Project ID is required.</p>");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat for Project</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .chat__container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 1rem;
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: #f9f9f9;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .chat__messages {
            max-height: 400px;
            overflow-y: auto;
            padding: 10px;
            border: 1px solid #ccc;
            margin-bottom: 1rem;
            border-radius: 5px;
            background-color: #fff;
        }

        #chat-form textarea {
            width: 100%;
            height: 60px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            font-size: 1rem;
        }

        #chat-form button {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }

        #chat-form button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <?php include "header.php"; ?>
    <h2>Chat for Project ID: <?php echo htmlspecialchars($project_id); ?></h2>
    <div class="chat__container">
        <div id="chat-box" class="chat__messages"></div>
        <form id="chat-form" method="POST">
            <textarea id="message" name="message" placeholder="Type your message..." required></textarea>
            <input type="hidden" name="project_id" value="<?php echo htmlspecialchars($project_id); ?>">
            <button type="submit">Send</button>
        </form>
    </div>

    <script>
        function fetchMessages() {
            $.ajax({
                url: 'fetch_messages.php',
                method: 'GET',
                data: { project_id: <?php echo $project_id; ?> },
                success: function(response) {
                    $('#chat-box').html(response);
                },
                error: function() {
                    console.error("Failed to fetch messages.");
                }
            });
        }

        $(document).ready(function () {
            fetchMessages();
            setInterval(fetchMessages, 3000); // Fetch new messages every 3 seconds

            $('#chat-form').on('submit', function (event) {
                event.preventDefault();
                $.ajax({
                    url: 'post_message.php',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function () {
                        $('#message').val(''); // Clear the textarea after submission
                        fetchMessages();
                        console.log("donne");
                    },
                    error: function() {
                        console.error("Failed to send the message.");
                    }
                });
            });
        });
    </script>
</body>
</html>
