<?php
session_start();
include('../db.php');

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$user_email = $_SESSION['email'];

// Retrieve form data
$name = $_POST['name'];
$email = $_POST['email'];
$phone_number = $_POST['phone_number'];
$address = $_POST['address'];
$gender = $_POST['gender'];

// Handle file upload for profile picture
$profile_picture = null;
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png'];
    $max_size = 5 * 1024 * 1024; // 5 MB
    $upload_dir = '../uploads/';
    
    // Ensure the upload directory exists
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $file_type = mime_content_type($_FILES['profile_picture']['tmp_name']);
    $file_size = $_FILES['profile_picture']['size'];
    
    if (!in_array($file_type, $allowed_types)) {
        die("Invalid file type. Only JPG and PNG files are allowed.");
    }
    
    if ($file_size > $max_size) {
        die("File size exceeds the 5MB limit.");
    }
    
    $file_name = basename($_FILES['profile_picture']['name']);
    $safe_name = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file_name);
    $target_file = $upload_dir . $safe_name;
    
    if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
        chmod($target_file, 0644); // Set secure permissions
        $profile_picture = $target_file; // Store the file path in the database
    } else {
        die("Failed to upload file.");
    }
}

// Update user information in the database
$query = "UPDATE users SET name = ?, email = ?, phone_number = ?, address = ?, gender = ?";
if ($profile_picture) {
    $query .= ", profile_picture = ?";
}
$query .= " WHERE email = ?";

$stmt = $conn->prepare($query);

if ($profile_picture) {
    $stmt->bind_param("sssssss", $name, $email, $phone_number, $address, $gender, $profile_picture, $user_email);
} else {
    $stmt->bind_param("ssssss", $name, $email, $phone_number, $address, $gender, $user_email);
}

if ($stmt->execute()) {
    // Update session email if the email was changed
    $_SESSION['email'] = $email;
    header("Location: edit-profile.php"); // Redirect back to the profile page
    exit();
} else {
    echo "Error updating profile: " . $conn->error;
}
?>
