<?php
// client_chat.php

session_start();
include "db_connection.php";

// Ensure the user is logged in as a client
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: client_login.php");
    exit();
}

// Validate the designer ID
$designer_id = isset($_GET['designer_id']) ? (int)$_GET['designer_id'] : null;

if (!$designer_id) {
    die("Invalid Designer ID.");
}

try {
    // Check if the designer exists and is a designer
    $sql = "SELECT designer_id, name FROM interior_designer WHERE designer_id = :designer_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':designer_id', $designer_id, PDO::PARAM_INT);
    $stmt->execute();
    $designer = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$designer) {
        die("Designer not found.");
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

// Get the logged-in client ID
$client_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Chat</title>
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
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }

        #chat-form button:hover {
            background-color: #218838;
        }

        .btn-container {
            text-align: center;
            margin-top: 20px;
        }

        .btn-container a {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 5px;
            font-size: 1rem;
            transition: 0.3s;
        }

        .btn-container a:hover {
            background-color: #0056b3;
        }

        .btn-accept {
            background-color: #28a745;
        }

        .btn-accept:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <?php include "header.php"; ?>
    <h2>Chat with Designer: <?php echo htmlspecialchars($designer['name']); ?></h2>
    <div class="chat__container">
        <div id="chat-box" class="chat__messages"></div>
        <form id="chat-form" method="POST">
            <textarea id="message" name="message" placeholder="Type your message..." required></textarea>
            <input type="hidden" name="receiver_id" value="<?php echo htmlspecialchars($designer_id); ?>">
            <button type="submit">Send</button>
        </form>
        <div class="btn-container">
            <a href="place_order.php?designer_id=<?php echo htmlspecialchars($designer_id); ?>" class="btn">Place an Order</a>
            <a href="feedback.php?designer_id=<?php echo htmlspecialchars($designer_id); ?>" class="btn btn-accept">Accept Order</a>
        </div>
    </div>

    <script>
        function fetchMessages() {
            $.ajax({
                url: 'fetch_messages.php',
                method: 'GET',
                data: { 
                    receiver_id: <?php echo $designer_id; ?>, 
                    designer_id: <?php echo $designer_id; ?> 
                },
                success: function(response) {
                    $('#chat-box').html(response);
                    // Scroll to bottom
                    $('#chat-box').scrollTop($('#chat-box')[0].scrollHeight);
                }
            });
        }

        $(document).ready(function () {
            fetchMessages();
            setInterval(fetchMessages, 3000);

            $('#chat-form').on('submit', function (event) {
                event.preventDefault();
                $.ajax({
                    url: 'post_message.php',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function (response) {
                        $('#message').val('');
                        fetchMessages();
                    }
                });
            });
        });
    </script>
</body>
</html>
