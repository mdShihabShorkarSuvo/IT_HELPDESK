<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['pass'];
    $cpassword = $_POST['cpass'];
    $phone = $_POST['phone'];
    $birth_date = $_POST['birth_date'];
    $address = trim($_POST['address']);
    $gender = $_POST['gender'];
    $role = $_POST['role'];
    
    // Hash Password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert Data
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone_number, date_of_birth, address, gender, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $name, $email, $hashed_password, $phone, $birth_date, $address, $gender, $role);

    if ($stmt->execute()) {
        // Redirect to login page after successful registration
        header("Location: login.php");
        exit(); // Stop the execution of the rest of the code
    } else {
        echo "<p style='color:red;'>Error: " . $stmt->error . "</p>";
    }
    
    $stmt->close();
    $conn->close();
}
?>
