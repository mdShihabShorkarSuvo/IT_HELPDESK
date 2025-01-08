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

    // Debugging: Check role value
    echo "Role: " . htmlspecialchars($role) . "<br>";

    // Validate role
    $valid_roles = ['user', 'it_staff', 'admin'];
    if (!in_array($role, $valid_roles)) {
        die("Invalid role selected.");
    }

    // Hash Password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert Data
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone_number, date_of_birth, address, gender, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    if (!$stmt) {
        die("Preparation failed: " . $conn->error);
    }

    $stmt->bind_param("ssssssss", $name, $email, $hashed_password, $phone, $birth_date, $address, $gender, $role);

    if ($stmt->execute()) {
        // Redirect to login page after successful registration
        header("Location: login.php");
        exit();
    } else {
        echo "<p style='color:red;'>Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
    $conn->close();
}
?>
