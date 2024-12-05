<?php
session_start();
include "db_connection.php";

// Ensure the user is logged in as a designer
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "designer") {
    header("Location: designer_login.php");
    exit();
}

// Fetch designer profile
try {
    $user_id = $_SESSION["user_id"];
    $sql = "SELECT u.email, u.username, d.experience 
            FROM user u
            JOIN interior_designer d ON u.user_id = d.user_id
            WHERE u.user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Update profile
        $username = trim($_POST["username"]);
        $email = trim($_POST["email"]);
        $experience = trim($_POST["experience"]);

        $update_user = "UPDATE user SET username = :username, email = :email WHERE user_id = :user_id";
        $stmt = $pdo->prepare($update_user);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        $update_designer = "UPDATE interior_designer SET experience = :experience WHERE user_id = :user_id";
        $stmt = $pdo->prepare($update_designer);
        $stmt->bindParam(':experience', $experience, PDO::PARAM_STR);
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
    <title>Designer Profile</title>
</head>
<body>
    <?php include "header.php"; ?>
    <div class="form__container">
        <h2>Designer Profile</h2>
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
                <label for="experience">Experience:</label>
                <textarea id="experience" name="experience" required><?php echo htmlspecialchars($profile['experience']); ?></textarea>
            </div>
            <div class="form__group">
                <input type="submit" value="Update Profile">
            </div>
        </form>
    </div>
</body>
</html>
