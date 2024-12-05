<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: client_login.php");
    exit();
}

include "db_connection.php"; // Include the database connection

$user_id = $_SESSION['user_id'];
$designer_id = isset($_GET['designer_id']) ? (int)$_GET['designer_id'] : null;

if (!$designer_id) {
    die("Invalid Designer ID."); // Terminate if designer_id is missing or invalid
}

try {
    // Verify if the designer exists
    $stmt = $pdo->prepare("SELECT designer_id FROM interior_designer WHERE designer_id = :designer_id");
    $stmt->bindParam(':designer_id', $designer_id, PDO::PARAM_INT);
    $stmt->execute();
    $designer = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$designer) {
        die("Designer not found.");
    }

    // If form is submitted, process the order
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["amount"]) && isset($_POST["duration"])) {
        $amount = $_POST["amount"];
        $duration = $_POST["duration"];

        // Insert the new order with a status of "pending"
        $sql = "INSERT INTO orders (user_id, designer_id, amount, duration, status, notification_sent) 
                VALUES (:user_id, :designer_id, :amount, :duration, 'pending', TRUE)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':designer_id', $designer_id, PDO::PARAM_INT);
        $stmt->bindParam(':amount', $amount, PDO::PARAM_STR);
        $stmt->bindParam(':duration', $duration, PDO::PARAM_INT);
        $stmt->execute();

        // Notify the designer (you can implement an email or dashboard notification system here)
        echo "<script>alert('Order placed successfully! The designer will be notified. Once they accept, you can proceed to payment.'); window.location.href = 'client_orders.php';</script>";
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
    <title>Place Order</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php include "header.php"; ?>
    <section class="section__container">
        <h2>Place Your Order</h2>
        <form method="POST" action="place_order.php?designer_id=<?php echo $designer_id; ?>" class="form__container">
            <div class="form__group">
                <label for="amount">Amount:</label>
                <input type="number" id="amount" name="amount" required>
            </div>
            <div class="form__group">
                <label for="duration">Duration (in days):</label>
                <input type="number" id="duration" name="duration" required>
            </div>
            <div class="form__group">
                <input type="submit" value="Place Order">
            </div>
        </form>
    </section>
</body>
</html>
