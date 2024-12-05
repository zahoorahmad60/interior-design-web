<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: client_login.php");
    exit();
}

include "db_connection.php"; // Include PDO connection

$user_id = $_SESSION['user_id'];
$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : null;

if (!$order_id) {
    die("Invalid payment details.");
}

// Fetch order and designer details from the database using order_id
try {
    $stmt = $pdo->prepare("
        SELECT o.designer_id, o.amount, o.duration, d.name AS designer_name
        FROM orders o
        JOIN interior_designer d ON o.designer_id = d.designer_id
        WHERE o.order_id = :order_id AND o.user_id = :user_id
    ");
    $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        die("Order not found or you do not have access to this order.");
    }

    $designer_id = $order['designer_id'];
    $amount = $order['amount'];
    $duration = $order['duration'];
    $designer_name = $order['designer_name'];
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}

$order_details = ''; // To hold order details for the popup

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $card_number = $_POST["card_number"];
    $expiry_date = $_POST["expiry_date"];
    $cvv = $_POST["cvv"];

    // Simulate payment processing (replace with real payment gateway integration in production)
    $payment_success = true;

    if ($payment_success) {
        try {
            // Insert payment record into the payments table
            $sql = "INSERT INTO payments (user_id, order_id, amount, date) VALUES (:user_id, :order_id, :amount, NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
            $stmt->bindParam(':amount', $amount, PDO::PARAM_STR);
            $stmt->execute();

            // Update the order status to 'completed'
            $sql = "UPDATE orders SET status = 'completed' WHERE order_id = :order_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
            $stmt->execute();

            // Prepare order details for the popup
            $order_details = json_encode([
                'designer_name' => $designer_name,
                'amount' => $amount,
                'duration' => $duration,
            ]);

        } catch (PDOException $e) {
            die("Database Error: " . $e->getMessage());
        }
    } else {
        die("Payment failed. Please try again.");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Popup styles */
        .popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 90%;
            max-width: 400px;
            background: white;
            border: 1px solid #ddd;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            text-align: center;
            padding: 20px;
            border-radius: 10px;
        }

        .popup h3 {
            margin: 0;
            margin-bottom: 15px;
        }

        .popup p {
            margin: 5px 0;
        }

        .popup .btn {
            margin-top: 15px;
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
        }

        .popup-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }
    </style>
</head>
<body>
<?php include "header.php"; ?>
    <section class="section__container">
        <h2>Payment</h2>
        <p>Designer: <?php echo htmlspecialchars($designer_name); ?></p>
        <p>Amount: $<?php echo htmlspecialchars($amount); ?></p>
        <p>Duration: <?php echo htmlspecialchars($duration); ?> days</p>
        <form method="POST" id="payment-form" class="form__container">
            <div class="form__group">
                <label for="card_number">Card Number:</label>
                <input type="text" id="card_number" name="card_number" required>
            </div>
            <div class="form__group">
                <label for="expiry_date">Expiry Date:</label>
                <input type="text" id="expiry_date" name="expiry_date" placeholder="MM/YY" required>
            </div>
            <div class="form__group">
                <label for="cvv">CVV:</label>
                <input type="text" id="cvv" name="cvv" required>
            </div>
            <div class="form__group">
                <input type="submit" value="Pay" id="pay-button">
            </div>
        </form>
    </section>

    <!-- Popup -->
    <div class="popup-overlay" id="popup-overlay"></div>
    <div class="popup" id="popup">
        <h3>Thank You!</h3>
        <p>Your payment was successful.</p>
        <p>Order Details:</p>
        <p><strong>Designer:</strong> <span id="popup-designer-name"></span></p>
        <p><strong>Amount:</strong> $<span id="popup-amount"></span></p>
        <p><strong>Duration:</strong> <span id="popup-duration"></span> days</p>
        <a href="index.php" class="btn">Go to Home</a>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const orderDetails = <?php echo $order_details ? $order_details : 'null'; ?>;

            if (orderDetails) {
                // Show the popup after successful payment
                document.getElementById("popup-designer-name").innerText = orderDetails.designer_name;
                document.getElementById("popup-amount").innerText = orderDetails.amount;
                document.getElementById("popup-duration").innerText = orderDetails.duration;

                document.getElementById("popup-overlay").style.display = "block";
                document.getElementById("popup").style.display = "block";
            }
        });
    </script>
</body>
</html>
