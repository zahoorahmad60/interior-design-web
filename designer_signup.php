<?php
// Start session
session_start();

// Include database connection
include "db_connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $email = trim($_POST['email']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT); // Hash the password
    $username = trim($_POST['username']);
    $experience = trim($_POST['experience']);

    try {
        // Check if the email already exists
        $email_check_query = "SELECT email FROM user WHERE email = :email";
        $stmt = $pdo->prepare($email_check_query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo "<p style='color: red;'>Error: This email is already registered. Please try logging in.</p>";
            exit();
        }

        // Begin transaction
        $pdo->beginTransaction();

        // Insert into the user table
        $user_query = "INSERT INTO user (email, password, username, role) VALUES (:email, :password, :username, 'designer')";
        $stmt = $pdo->prepare($user_query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':password', $password, PDO::PARAM_STR);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $user_id = $pdo->lastInsertId(); // Get the ID of the newly created user

        // Insert into the interior_designer table
        $designer_query = "INSERT INTO interior_designer (user_id, name, experience) VALUES (:user_id, :name, :experience)";
        $stmt = $pdo->prepare($designer_query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $username, PDO::PARAM_STR);
        $stmt->bindParam(':experience', $experience, PDO::PARAM_STR);
        $stmt->execute();

        // Commit transaction
        $pdo->commit();

        // Redirect or display a success message
        echo "<script>
            alert('Signup successful. Redirecting to login page...');
            window.location.href = 'designer_login.php';
        </script>";
    } catch (Exception $e) {
        // Rollback transaction on error
        $pdo->rollBack();
        echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Designer Signup</title>
</head>
<body>
    <?php include "header.php"; ?>

    <div class="form__container">
        <h2>Interior Designer Signup</h2>
        <form method="POST" action="">
            <div class="form__group">
                <label for="username">Name:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form__group">
                <label for="experience">Experience:</label>
                <textarea id="experience" name="experience" required></textarea>
            </div>
            <div class="form__group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form__group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form__group">
                <input type="submit" value="Signup">
            </div>
            <div class="form__group">
                <p>Already have an account? <a href="designer_login.php" class="form__link">Login</a></p>
            </div>
        </form>
    </div>
</body>
</html>
