<?php
session_start();
include "db_connection.php"; // Includes PDO connection as $pdo

// Check if the user is logged in as a client
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'client') {
    header("Location: client_login.php");
    exit();
}

// Get the logged-in client's user ID
$user_id = $_SESSION['user_id'];

try {
    // Fetch all orders placed by the client
    $sql = "SELECT o.order_id, o.amount, o.duration, o.status, o.order_date, 
                   d.designer_id, d.name AS designer_name 
            FROM orders o
            JOIN interior_designer d ON o.designer_id = d.designer_id
            WHERE o.user_id = :user_id
            ORDER BY o.order_date DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching orders: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .orders__container {
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

        .order__status {
            font-weight: bold;
        }

        .btn {
            padding: 8px 16px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            transition: 0.3s;
            display: inline-block;
            margin-right: 10px;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .btn-disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
<?php include "header.php"; ?>
    <section class="orders__container">
        <h2>My Orders</h2>
        <?php if (empty($orders)): ?>
            <p>No orders found. <a href="designers.php" class="btn">Place a New Order</a></p>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <div class="order__item">
                    <div class="order__details">
                        <p><strong>Order ID:</strong> <?= htmlspecialchars($order['order_id']) ?></p>
                        <p><strong>Designer:</strong> <?= htmlspecialchars($order['designer_name']) ?></p>
                        <p><strong>Amount:</strong> $<?= number_format($order['amount'], 2) ?></p>
                        <p><strong>Duration:</strong> <?= htmlspecialchars($order['duration']) ?> days</p>
                        <p><strong>Date:</strong> <?= htmlspecialchars($order['order_date']) ?></p>
                        <p><strong>Status:</strong> 
                            <span class="order__status">
                                <?php
                                if ($order['status'] === 'pending') {
                                    echo "Pending (Awaiting Designer Approval)";
                                } elseif ($order['status'] === 'accepted') {
                                    echo "Accepted (Proceed to Payment)";
                                } elseif ($order['status'] === 'rejected') {
                                    echo "Rejected";
                                }
                                ?>
                            </span>
                        </p>
                    </div>
                    <?php if ($order['status'] === 'accepted'): ?>
                        <a href="payment.php?order_id=<?= $order['order_id'] ?>" class="btn">Pay Now</a>
                    <?php elseif ($order['status'] === 'rejected'): ?>
                        <p class="btn-disabled">Order Rejected</p>
                    <?php else: ?>
                        <p class="btn-disabled">Waiting for Designer Approval</p>
                    <?php endif; ?>
                    <a href="client_chat.php?designer_id=<?= $order['designer_id'] ?>" class="btn">Chat with Designer</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>
</body>
</html>
