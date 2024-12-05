<?php
session_start();
include "db_connection.php"; // Includes PDO connection as $pdo

// Check if the user is logged in as a designer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'designer') {
    header("Location: designer_login.php");
    exit();
}

// Get the logged-in designer's user ID
$user_id = $_SESSION['user_id'];

try {
    // Fetch designer_id for the logged-in designer
    $sql = "SELECT designer_id FROM interior_designer WHERE user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $designer = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$designer) {
        die("Error: No designer profile found for the logged-in user.");
    }

    $designer_id = $designer['designer_id'];

    // Fetch pending orders for this designer
    $sql = "SELECT o.order_id, o.amount, o.duration, o.order_details, o.order_date, 
                   u.username AS client_name, u.email AS client_email 
            FROM orders o
            JOIN user u ON o.user_id = u.user_id
            WHERE o.designer_id = :designer_id AND o.status = 'pending'";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':designer_id', $designer_id, PDO::PARAM_INT);
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Handle form submission for approving or rejecting an order
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id']) && isset($_POST['action'])) {
        $order_id = (int)$_POST['order_id'];
        $action = $_POST['action'] === 'approve' ? 'accepted' : 'rejected';

        // Update the order status
        $sql = "UPDATE orders SET status = :status WHERE order_id = :order_id AND designer_id = :designer_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':status', $action, PDO::PARAM_STR);
        $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        $stmt->bindParam(':designer_id', $designer_id, PDO::PARAM_INT);
        $stmt->execute();

        $message = $action === 'accepted' ? "Order approved successfully!" : "Order rejected successfully!";
        echo "<script>alert('$message'); window.location.href = 'notifications.php';</script>";
        exit();
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Notifications</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .notifications__container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 1rem;
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: #f9f9f9;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .order__item {
            border-bottom: 1px solid #ddd;
            padding: 1rem 0;
        }

        .order__item:last-child {
            border-bottom: none;
        }

        .order__details {
            margin-bottom: 1rem;
        }

        .btn {
            padding: 8px 16px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: 0.3s;
            display: inline-block;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .btn-reject {
            background-color: #dc3545;
        }

        .btn-reject:hover {
            background-color: #a71d2a;
        }
    </style>
</head>
<body>
<?php include "header.php"; ?>
    <section class="notifications__container">
        <h2>Order Notifications</h2>
        <?php if (empty($orders)): ?>
            <p>No pending orders at the moment.</p>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <div class="order__item">
                    <div class="order__details">
                        <p><strong>Order ID:</strong> <?= htmlspecialchars($order['order_id']) ?></p>
                        <p><strong>Client:</strong> <?= htmlspecialchars($order['client_name']) ?> (<?= htmlspecialchars($order['client_email']) ?>)</p>
                        <p><strong>Amount:</strong> $<?= number_format($order['amount'], 2) ?></p>
                        <p><strong>Duration:</strong> <?= htmlspecialchars($order['duration']) ?> days</p>
                        <p><strong>Order Details:</strong> <?= htmlspecialchars($order['order_details']) ?></p>
                        <p><strong>Order Date:</strong> <?= htmlspecialchars($order['order_date']) ?></p>
                    </div>
                    <form method="POST" style="margin-top: 1rem;">
                        <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['order_id']) ?>">
                        <button type="submit" name="action" value="approve" class="btn">Approve</button>
                        <button type="submit" name="action" value="reject" class="btn btn-reject">Reject</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>
</body>
</html>
