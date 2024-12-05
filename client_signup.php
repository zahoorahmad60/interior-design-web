<?php
include "db_connection.php"; // Ensure $pdo is defined in db_connection.php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $email = trim($_POST["email"]);
    $location = trim($_POST["location"]);
    $password = password_hash(trim($_POST["password"]), PASSWORD_DEFAULT); // Hash the password
    $username = trim($_POST["username"]);
    $preferences = trim($_POST["preferences"]);

    try {
        // Check if the email already exists
        $sql = "SELECT email FROM user WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            die("<p style='color: red;'>Error: This email is already registered. Please try logging in.</p>");
        }

        // Insert the new user into the database
        $sql = "INSERT INTO user (email, location, password, username, preferences, role) 
                VALUES (:email, :location, :password, :username, :preferences, 'client')";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':location', $location, PDO::PARAM_STR);
        $stmt->bindParam(':password', $password, PDO::PARAM_STR);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':preferences', $preferences, PDO::PARAM_STR);

        if ($stmt->execute()) {
            echo "Signup successful!";
            // Redirect to login page
            header("Location: client_login.php");
            exit();
        } else {
            echo "<p style='color: red;'>Error: Failed to create account. Please try again.</p>";
        }
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Client Signup</title>
</head>
<body>
<?php include "header.php"; ?>
    <div class="form__container">
        <h2>Client Signup</h2>
        <form method="POST" action="">
            <div class="form__group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form__group">
                <label for="location">Location:</label>
                <input type="text" id="location" name="location" required>
            </div>
            <div class="form__group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form__group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form__group">
                <label for="preferences">Preferences:</label>
                <textarea id="preferences" name="preferences"></textarea>
            </div>
            <div class="form__group">
                <input type="submit" value="Signup">
            </div>
        </form>
        <div class="form__group">
            <p>
                Already have an account? <a href="client_login.php" class="form__link">Login</a>
            </p>
        </div>
    </div>
</body>
</html>
