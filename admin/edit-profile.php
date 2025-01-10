<?php
session_start();

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../login.php"); // Redirect to login page if not a user
    exit();
}

// Include database connection
include('../db.php');

$user_email = $_SESSION['email'];

// Fetch user information from the database
$query = "SELECT name, email, profile_picture, phone_number, address, gender FROM users WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Admin not found.");
}

$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="profile.css">
</head>
<body>
    <div class="edit-profile-container">
        <div class="profile-card">
            <div class="profile-info">
                <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" class="profile-picture">
                <h2><?php echo htmlspecialchars($user['name']); ?></h2>
                <p><?php echo htmlspecialchars($user['email']); ?></p>
                <p>About</p>
                <p>Hi, I'm <?php echo htmlspecialchars($user['name']); ?>. I enjoy creating meaningful experiences.</p>
            </div>
        </div>
        <div class="form-container">
            <h2>Edit Personal Details</h2>
            <form id="profile-form" method="POST" action="update_profile.php" enctype="multipart/form-data">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                
                <label for="phone_number">Phone Number</label>
                <input type="text" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number']); ?>" required>
                
                <label for="address">Address</label>
                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user['address']); ?>" required>
                
                <label for="gender">Gender</label>
                <select id="gender" name="gender" required>
                    <option value="Male" <?php if ($user['gender'] === 'Male') echo 'selected'; ?>>Male</option>
                    <option value="Female" <?php if ($user['gender'] === 'Female') echo 'selected'; ?>>Female</option>
                    <option value="Other" <?php if ($user['gender'] === 'Other') echo 'selected'; ?>>Other</option>
                </select>

                <label for="profile_picture">Profile Picture</label>
                <input type="file" id="profile_picture" name="profile_picture">

                <div class="form-buttons">
                    <button type="button" class="cancel-button" onclick="window.location.href='admin_page.php'">Cancel</button>
                    <button type="submit" class="update-button">Update</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
