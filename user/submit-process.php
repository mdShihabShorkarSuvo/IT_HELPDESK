<?php
session_start();
include('../db.php');

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Access denied. Please log in to submit a ticket.");
}

// Initialize variables
$user_id = $_SESSION['user_id'];
$category = htmlspecialchars($_POST['category'], ENT_QUOTES, 'UTF-8');
$title = htmlspecialchars($_POST['title'], ENT_QUOTES, 'UTF-8');
$description = htmlspecialchars($_POST['description'], ENT_QUOTES, 'UTF-8');
$room_no = htmlspecialchars($_POST['roomNo'], ENT_QUOTES, 'UTF-8'); // New field for Room No
$pc_no = htmlspecialchars($_POST['pcNo'], ENT_QUOTES, 'UTF-8'); // New field for PC No
$attachment = null;

// Handle file upload
if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
    $max_size = 5 * 1024 * 1024; // 5 MB
    $upload_dir = '../uploads/';
    
    $file_type = mime_content_type($_FILES['attachment']['tmp_name']);
    $file_size = $_FILES['attachment']['size'];
    
    if (!in_array($file_type, $allowed_types)) {
        die("Invalid file type. Only JPG, PNG, and PDF files are allowed.");
    }
    
    if ($file_size > $max_size) {
        die("File size exceeds the 5MB limit.");
    }
    
    $file_name = basename($_FILES['attachment']['name']);
    $safe_name = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file_name);
    $target_file = $upload_dir . $safe_name;
    
    if (move_uploaded_file($_FILES['attachment']['tmp_name'], $target_file)) {
        chmod($target_file, 0644); // Set secure permissions
        $attachment = $target_file;
    } else {
        die("Failed to upload file.");
    }
}

// Insert ticket into database
$sql = "INSERT INTO tickets (user_id, category, title, description, room_no, pc_no, attachment) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("issssss", $user_id, $category, $title, $description, $room_no, $pc_no, $attachment);

if ($stmt->execute()) {
    header("Location: user_page.php?page=submit-ticket");
    exit();
} else {
    echo "Error: " . $stmt->error;
}

// Close connection
$stmt->close();
$conn->close();
?>
