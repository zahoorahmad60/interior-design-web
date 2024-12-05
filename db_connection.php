<?php
// Database connection settings
$host = 'localhost'; // Database host
$dbname = 'interior_design2'; // Database name
$username = 'root'; // Database username
$password = '123456'; // Database password

try {
    // Create a new PDO instance
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Set PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    // Handle connection errors
    echo "Database connection failed: " . $e->getMessage();
    exit();
}
?>
