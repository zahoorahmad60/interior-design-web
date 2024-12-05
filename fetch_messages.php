<?php
// fetch_messages.php

include "db_connection.php";
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    die("Unauthorized access.");
}

$sender_user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Get receiver_id and designer_id from GET parameters
$receiver_id = isset($_GET['receiver_id']) ? (int)$_GET['receiver_id'] : null;
$designer_id_input = isset($_GET['designer_id']) ? (int)$_GET['designer_id'] : null;

// Initialize variables
$client_id = null;
$designer_id = null;
$client_name = '';
$designer_name = '';

// Determine chat participants based on role
if ($role === 'client') {
    if (!$designer_id_input) {
        die("Invalid Designer ID.");
    }
    $client_id = $sender_user_id;
    $designer_id = $designer_id_input;
    
    // Fetch designer's name
    try {
        $stmt = $pdo->prepare("SELECT name FROM interior_designer WHERE designer_id = :designer_id");
        $stmt->bindParam(':designer_id', $designer_id, PDO::PARAM_INT);
        $stmt->execute();
        $designer = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$designer) {
            die("Designer not found.");
        }
        $designer_name = htmlspecialchars($designer['name']);
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
} elseif ($role === 'designer') {
    if (!$receiver_id) {
        die("Invalid Client ID.");
    }
    $designer_user_id = $sender_user_id;
    
    // Fetch designer_id
    try {
        $stmt = $pdo->prepare("SELECT designer_id FROM interior_designer WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $designer_user_id, PDO::PARAM_INT);
        $stmt->execute();
        $designer = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$designer) {
            die("Designer profile not found.");
        }
        $designer_id = $designer['designer_id'];
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
    
    $client_id = $receiver_id;
    
    // Fetch client's name
    try {
        $stmt = $pdo->prepare("SELECT username FROM user WHERE user_id = :client_id AND role = 'client'");
        $stmt->bindParam(':client_id', $client_id, PDO::PARAM_INT);
        $stmt->execute();
        $client = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$client) {
            die("Client not found.");
        }
        $client_name = htmlspecialchars($client['username']);
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
} else {
    die("Invalid user role.");
}

try {
    // Fetch messages between user and designer
    $sql = "
        SELECT user_id, designer_id, message, timestamp, sender_role
        FROM chats
        WHERE user_id = :user_id AND designer_id = :designer_id
        ORDER BY timestamp ASC
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $client_id, PDO::PARAM_INT);
    $stmt->bindParam(':designer_id', $designer_id, PDO::PARAM_INT);
    $stmt->execute();
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($messages)) {
        echo "<div>No messages found. Start the conversation!</div>";
    } else {
        foreach ($messages as $msg) {
            if ($msg['sender_role'] === 'client') {
                $sender_display_name = $client_name;
            } elseif ($msg['sender_role'] === 'designer') {
                $sender_display_name = $designer_name;
            } else {
                $sender_display_name = "Unknown";
            }

            echo "<div style='margin-bottom: 10px; padding: 10px; border: 1px solid #ddd; border-radius: 5px;'>";
            echo "<strong>{$sender_display_name}:</strong> " . nl2br(htmlspecialchars($msg['message'])) . "<br>";
            echo "<small style='color: gray;'>" . htmlspecialchars($msg['timestamp']) . "</small>";
            echo "</div>";
        }
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
