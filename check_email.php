<?php
include 'db.php';  // Ensure you include your database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the email from the AJAX request
    $email = trim($_POST['email']);

    // Prepare SQL query to check if the email exists in the database
    $stmt = $conn->prepare("SELECT 1 FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // If a row is found, that means the email exists
    if ($stmt->num_rows > 0) {
        echo 'exists';  // Email already exists
    } else {
        echo 'available';  // Email is available
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
