<?php
// designer_chat.php

session_start();
include "db_connection.php";

// Ensure the user is logged in as a designer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'designer') {
    header("Location: designer_login.php");
    exit();
}

// Get designer's user_id from session
$designer_user_id = $_SESSION['user_id'];

// Fetch designer_id and name from interior_designer table
try {
    $stmt = $pdo->prepare("SELECT designer_id, name FROM interior_designer WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $designer_user_id, PDO::PARAM_INT);
    $stmt->execute();
    $designer = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$designer) {
        die("Designer profile not found.");
    }

    $designer_id = $designer['designer_id'];
    $designer_name = htmlspecialchars($designer['name']);
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}

// Validate the client ID (receiver)
$receiver_id = isset($_GET['receiver_id']) ? (int)$_GET['receiver_id'] : null;

// Initialize $client variable
$client = null;

// If receiver_id is provided, fetch client data
if ($receiver_id) {
    try {
        $sql = "SELECT user_id, username FROM user WHERE user_id = :receiver_id AND role = 'client'";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':receiver_id', $receiver_id, PDO::PARAM_INT);
        $stmt->execute();
        $client = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$client) {
            // Client not found, show error
            die("Client not found.");
        }
    } catch (PDOException $e) {
        die("Database Error: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Designer Chat</title>
    <link rel="stylesheet" href="styles.css">
    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* Chat Container Styles */
        .chat__container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 1rem;
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: #f9f9f9;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Chat Messages Styles */
        .chat__messages {
            max-height: 400px;
            overflow-y: auto;
            padding: 10px;
            border: 1px solid #ccc;
            margin-bottom: 1rem;
            border-radius: 5px;
            background-color: #fff;
        }

        /* Chat Form Styles */
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

        /* Loading Spinner Styles */
        .spinner {
            border: 4px solid #f3f3f3; /* Light grey */
            border-top: 4px solid #3498db; /* Blue */
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Error Message Styles */
        .error-message {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }

        /* Success Message Styles */
        .success-message {
            color: green;
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <?php include "header.php"; ?>
    <h2>
        <?php if ($client): ?>
            Chat with Client: <?php echo htmlspecialchars($client['username']); ?>
        <?php else: ?>
            Select a Client to Chat
        <?php endif; ?>
    </h2>

    <div class="chat__container">
        <?php if (!$client): ?>
            <!-- Form to Enter Client ID -->
            <form method="GET" action="designer_chat.php">
                <label for="receiver_id">Enter Client ID:</label>
                <input type="number" id="receiver_id" name="receiver_id" required>
                <button type="submit">Start Chat</button>
            </form>
        <?php else: ?>
            <!-- Chat Interface -->
            <div id="chat-box" class="chat__messages">
                <!-- Messages will be loaded here via AJAX -->
            </div>
            <div id="status-message" class="error-message"></div>
            <form id="chat-form" method="POST">
                <textarea id="message" name="message" placeholder="Type your message..." required></textarea>
                <input type="hidden" name="receiver_id" value="<?php echo htmlspecialchars($receiver_id); ?>">
                <input type="hidden" name="designer_id" value="<?php echo htmlspecialchars($designer_id); ?>">
                <button type="submit">Send</button>
            </form>
            <div id="loading" style="display: none;">
                <div class="spinner"></div>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($client): ?>
    <script>
        $(document).ready(function () {
            // Function to Fetch Messages
            function fetchMessages() {
                $('#loading').show();
                $('#status-message').text('');
                $.ajax({
                    url: 'fetch_messages.php',
                    method: 'GET',
                    data: { 
                        receiver_id: <?php echo $receiver_id; ?>, 
                        designer_id: <?php echo $designer_id; ?> 
                    },
                    success: function(response) {
                        $('#chat-box').html(response);
                        // Auto-Scroll to Bottom
                        $('#chat-box').scrollTop($('#chat-box')[0].scrollHeight);
                        $('#loading').hide();
                    },
                    error: function(xhr, status, error) {
                        $('#status-message').text('Error fetching messages.');
                        $('#loading').hide();
                    }
                });
            }

            // Initial Fetch
            fetchMessages();

            // Polling: Fetch Messages Every 3 Seconds
            setInterval(fetchMessages, 3000);

            // Handle Chat Form Submission
            $('#chat-form').on('submit', function (event) {
                event.preventDefault();
                var message = $('#message').val().trim();
                if (message === '') {
                    $('#status-message').text('Message cannot be empty.');
                    return;
                }

                $('#loading').show();
                $('#status-message').text('');

                $.ajax({
                    url: 'post_message.php',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function (response) {
                        // Optionally, display a success message
                        // $('#status-message').text(response).removeClass('error-message').addClass('success-message');
                        $('#message').val('');
                        fetchMessages(); // Immediately fetch messages after sending
                        $('#loading').hide();
                    },
                    error: function(xhr, status, error) {
                        $('#status-message').text('Error sending message.');
                        $('#loading').hide();
                    }
                });
            });
        });
    </script>
    <?php endif; ?>
</body>
</html>
