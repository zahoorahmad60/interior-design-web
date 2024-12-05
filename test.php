<?php
// test.php

// Function to simulate POST requests
function test_login($email, $password)
{
    // Start cURL
    $ch = curl_init();

    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, "http://localhost/Interior_design/client_login.php"); // Replace with your actual login.php path
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'email' => $email,
        'password' => $password
    ]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    // Execute cURL and fetch response
    $response = curl_exec($ch);

    // Check for cURL errors
    if (curl_errno($ch)) {
        echo "cURL Error: " . curl_error($ch) . "\n";
    }

    // Close cURL
    curl_close($ch);

    // Output the response
    return $response;
}

// Test cases
echo "Testing with valid credentials:\n";
$response = test_login("zass@gmail.com", "Zah@1234"); // Replace with actual valid credentials
echo $response;

?>
