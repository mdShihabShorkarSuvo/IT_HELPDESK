<?php
session_start();
include('../db.php');

if ($_SESSION['role'] !== 'user') {
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
    $target_dir = "../uploads/";
    $file_name = basename($_FILES['profile_picture']['name']);
    $target_file = $target_dir . $file_name;
    
    // Move uploaded file to the target directory
    if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
        $profile_picture = $target_file;
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
