<?php
session_start();

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $user_id = $_GET['id'];  // Use user ID from the URL
    // Continue processing...
} else {
    echo "No user ID provided!";
}

$user_id = $_GET['id'];

// Database connection (replace with your credentials)
try {
    $pdo = new PDO('mysql:host=localhost;dbname=smart_it_helpdesk', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Get the user ID from the query parameter
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "No user ID provided!";
    exit;
}

$user_id = $_GET['id'];




// Fetch user details, including the password
$query = "SELECT user_id, name, email, gender, role, profile_picture, password FROM users WHERE user_id = :user_id";
$stmt = $pdo->prepare($query);
$stmt->execute(['user_id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "User not found!";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $gender = $_POST['gender'];
    $role = $_POST['role'];

    // Check if the password is provided and hash it if necessary; otherwise, use the current hashed password
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);  // Hash new password
    } else {
        $password = $user['password'];  // Use the existing password if no new password is provided
    }

    // Handle profile picture upload
    $profile_picture = $user['profile_picture']; // Keep the old image if no new one is uploaded
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
        $fileName = $_FILES['profile_picture']['name'];
        $fileSize = $_FILES['profile_picture']['size'];
        $fileType = $_FILES['profile_picture']['type'];

        // Check if the uploaded file is an image
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($fileType, $allowedTypes)) {
            $uploadDir = 'uploads/profile_pics/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Create a unique filename to avoid overwriting
            $newFileName = uniqid() . '_' . basename($fileName);
            $uploadPath = $uploadDir . $newFileName;

            // Move the file to the desired directory
            if (move_uploaded_file($fileTmpPath, $uploadPath)) {
                $profile_picture = $uploadPath; // Update the profile picture path
            } else {
                echo "Error uploading the file!";
                exit;
            }
        } else {
            echo "Invalid file type! Only JPG, PNG, or GIF files are allowed.";
            exit;
        }
    }

    // Update the user in the database
    $update_query = "UPDATE users SET name = :name, email = :email, gender = :gender, role = :role, password = :password, profile_picture = :profile_picture WHERE user_id = :user_id";
    $update_stmt = $pdo->prepare($update_query);
    $update_stmt->execute([
        'name' => $name,
        'email' => $email,
        'gender' => $gender,
        'role' => $role,
        'password' => $password,
        'profile_picture' => $profile_picture,
        'user_id' => $user_id
    ]);

    echo "User updated successfully!";
    header("Location: user_update.php?id=" . urlencode($user_id));
    exit;
}
