<?php
session_start();
include "db_connection.php"; // Include the PDO connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $email = $_POST["email"];
    $password = $_POST["password"];

    try {
        // SQL query to fetch user by email
        $sql = "SELECT user_id, password FROM user WHERE email = :email";
        $stmt = $pdo->prepare($sql); // Use $pdo for prepared statement
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        // Fetch the user record
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Login successful
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = 'client';

            // Redirect to client dashboard
            header("Location: client_dashboard.php");
            exit();
        } else {
            // Invalid email or password
            echo "Invalid email or password.";
        }
    } catch (PDOException $e) {
        // Handle database errors
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Client Login</title>
</head>
<body>
<?php include "header.php"; ?>
    <div class="form__container">
        <h2>Client Login</h2>
        <form method="POST" action="">
            <div class="form__group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form__group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form__group">
                <input type="submit" value="Login">
            </div>
        </form>
        <div class="form__group">
            <p>
                New user? <a href="client_signup.php" class="form__link">Signup</a>
            </p>
        </div>
    </div>
</body>
</html>
