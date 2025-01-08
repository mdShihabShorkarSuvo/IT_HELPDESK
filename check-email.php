<?php
require_once 'db.php';

if (isset($_GET['email'])) {
    $email = $conn->real_escape_string($_GET['email']);

    // Query to check if email exists
    $query = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($query);

    // Return JSON response
    if ($result->num_rows > 0) {
        echo json_encode(['isUnique' => false]); // Email already exists
    } else {
        echo json_encode(['isUnique' => true]); // Email is unique
    }
}
?>
