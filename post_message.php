<?php
// post_message.php

include "db_connection.php";
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    die("Unauthorized access.");
}

$sender_user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Get message and receiver_id from POST data
$message = isset($_POST['message']) ? trim($_POST['message']) : null;
$receiver_id = isset($_POST['receiver_id']) ? (int)$_POST['receiver_id'] : null;

// For clients, 'receiver_id' is designer_id
// For designers, 'receiver_id' is client_id

if (!$message) {
    die("Message cannot be empty.");
}

try {
    if ($role === 'client') {
        if (!$receiver_id) {
            die("Invalid Designer ID.");
        }
        // For clients, user_id = client ID, designer_id = designer ID
        $user_id = $sender_user_id;
        $designer_id = $receiver_id;
        $sender_role = 'client';
    } elseif ($role === 'designer') {
        if (!$receiver_id) {
            die("Invalid Client ID.");
        }
        // For designers, user_id = client ID, designer_id = designer ID
        $client_id = $receiver_id;
        // Fetch designer_id from interior_designer table
        $stmt = $pdo->prepare("SELECT designer_id FROM interior_designer WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $sender_user_id, PDO::PARAM_INT);
        $stmt->execute();
        $designer = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$designer) {
            die("Designer profile not found.");
        }
        $designer_id = $designer['designer_id'];
        $user_id = $client_id;
        $sender_role = 'designer';
    } else {
        die("Invalid user role.");
    }

    // Insert the message into chats table with sender_role
    $sql = "INSERT INTO chats (user_id, designer_id, message, sender_role) VALUES (:user_id, :designer_id, :message, :sender_role)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':designer_id', $designer_id, PDO::PARAM_INT);
    $stmt->bindParam(':message', $message, PDO::PARAM_STR);
    $stmt->bindParam(':sender_role', $sender_role, PDO::PARAM_STR);
    $stmt->execute();

    echo "Message sent successfully!";
} catch (PDOException $e) {
    // Log the error instead of displaying it
    // error_log("Database Error: " . $e->getMessage());
    die("Error sending message.");
}
?>
