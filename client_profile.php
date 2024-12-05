<?php
session_start();
include "db_connection.php";

// Ensure the user is logged in as a client
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "client") {
    header("Location: client_login.php");
    exit();
}

// Fetch client profile
try {
    $user_id = $_SESSION["user_id"];
    $sql = "SELECT email, username FROM user WHERE user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Update profile
        $username = trim($_POST["username"]);
        $email = trim($_POST["email"]);

        $update_user = "UPDATE user SET username = :username, email = :email WHERE user_id = :user_id";
        $stmt = $pdo->prepare($update_user);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        echo "<script>alert('Profile updated successfully!');</script>";
        header("Refresh:0");
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
    <link rel="stylesheet" href="styles.css">
    <title>Client Profile</title>
</head>
<body>
    <?php include "header.php"; ?>
    <div class="form__container">
        <h2>Client Profile</h2>
        <form method="POST" action="">
            <div class="form__group">
                <label for="username">Name:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($profile['username']); ?>" required>
            </div>
            <div class="form__group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($profile['email']); ?>" required>
            </div>
            <div class="form__group">
                <input type="submit" value="Update Profile">
            </div>
        </form>
    </div>
</body>
</html>
