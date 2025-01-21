<?php
include 'db.php'; // Ensure the database connection file is included

// Start session for session flash messages
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collecting form data with sanitization
    $name = trim($_POST['name']);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['pass'];
    $cpassword = $_POST['cpass'];
    $phone = filter_var(trim($_POST['phone']), FILTER_SANITIZE_STRING);
    $birth_date = $_POST['birth_date'];
    $address = trim($_POST['address']);
    $gender = $_POST['gender'];
    $role = $_POST['role'];

    // Validate role
    $valid_roles = ['user', 'it_staff', 'admin'];
    if (!in_array($role, $valid_roles)) {
        $_SESSION['error'] = "Invalid role selected.";
        header("Location: signup.php");
        exit();
    }

    // Validate Email Format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format.";
        header("Location: signup.php");
        exit();
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT 1 FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['error'] = "Email already registered. Please use a different email.";
        header("Location: signup.php");
        exit();
    }

    // Check if password and confirm password match
    if ($password !== $cpassword) {
        $_SESSION['error'] = "Passwords do not match.";
        header("Location: signup.php");
        exit();
    }

    // Hash Password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare SQL query to insert user data
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone_number, date_of_birth, address, gender, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    if (!$stmt) {
        $_SESSION['error'] = "Database preparation failed. Please try again.";
        header("Location: signup.php");
        exit();
    }

    // Bind parameters and execute the query
    $stmt->bind_param("ssssssss", $name, $email, $hashed_password, $phone, $birth_date, $address, $gender, $role);

    // Execute the query and check if insertion is successful
    if ($stmt->execute()) {
        // Registration successful, redirect to login page
        $_SESSION['success'] = "Registration successful! Please log in.";
        header("Location: login.php");
        exit();
    } else {
        $_SESSION['error'] = "Error: " . $stmt->error;
        header("Location: signup.php");
        exit();
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>
