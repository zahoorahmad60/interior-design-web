<?php
session_start(); // Start the session
include "db_connection.php"; // Include the PDO connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    try {
        // Fetch designer's details from the user table
        $sql = "SELECT user_id, password, username FROM user WHERE email = :email AND role = 'designer'";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            // Fetch results
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $user_id = $user['user_id'];
            $hashed_password = $user['password'];
            $username = $user['username'];

            // Verify password
            if (password_verify($password, $hashed_password)) {
                // Fetch designer-specific details
                $designer_query = "SELECT designer_id FROM interior_designer WHERE user_id = :user_id";
                $designer_stmt = $pdo->prepare($designer_query);
                $designer_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $designer_stmt->execute();
                $designer = $designer_stmt->fetch(PDO::FETCH_ASSOC);

                // Set session variables
                $_SESSION["user_id"] = $user_id;
                $_SESSION["role"] = "designer";
                $_SESSION["designer_id"] = $designer['designer_id'];
                $_SESSION["username"] = $username;

                // Redirect to designer dashboard
                header("Location: designer_dashboard.php");
                exit();
            } else {
                $error_message = "Invalid email or password.";
            }
        } else {
            $error_message = "No account found with this email.";
        }
    } catch (PDOException $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Designer Login</title>
</head>
<body>
    <?php include "header.php"; ?>
    <div class="form__container">
        <h2>Interior Designer Login</h2>
        <?php if (isset($error_message)): ?>
            <p style="color: red; text-align: center;"><?php echo $error_message; ?></p>
        <?php endif; ?>
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
                New user? <a href="designer_signup.php" class="form__link">Signup</a>
            </p>
        </div>
    </div>
</body>
</html>
