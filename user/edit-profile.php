<?php
session_start();

if ($_SESSION['role'] !== 'user') {
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
$stmt->bind_result($name, $email, $profile_picture, $phone_number, $address, $gender);
$stmt->fetch();
$stmt->close();

if (!$name) {
    die("User not found.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_picture'])) {
    $user_id = $_SESSION['user_id'];
    $target_dir = "../images/";
    $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["profile_picture"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["profile_picture"]["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
            // Crop the image
            $src = imagecreatefromstring(file_get_contents($target_file));
            $width = imagesx($src);
            $height = imagesy($src);
            $new_width = 200;
            $new_height = 200;
            $tmp = imagecreatetruecolor($new_width, $new_height);
            imagecopyresampled($tmp, $src, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
            imagejpeg($tmp, $target_file, 100);
            imagedestroy($src);
            imagedestroy($tmp);

            $sql = "UPDATE users SET profile_picture = ? WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $target_file, $user_id);
            if ($stmt->execute()) {
                echo "The file ". htmlspecialchars(basename($_FILES["profile_picture"]["name"])). " has been uploaded.";
                header("Location: user_Page.php");
                exit();
            } else {
                echo "Sorry, there was an error updating your profile picture.";
            }
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="css/profile.css">
</head>
<body>
    <div class="edit-profile-container">
        <div class="profile-card">
            <div class="profile-info">
                <?php if ($profile_picture): ?>
                    <img src="<?php echo htmlspecialchars($profile_picture); ?>" alt="Profile Picture" class="profile-picture">
                <?php endif; ?>
                <h2><?php echo htmlspecialchars($name); ?></h2>
                <p><?php echo htmlspecialchars($email); ?></p>
                <p>About</p>
                <p>Hi, I'm <?php echo htmlspecialchars($name); ?>. I enjoy creating meaningful experiences.</p>
            </div>
        </div>
        <div class="form-container">
            <h2>Edit Personal Details</h2>
            <form id="profile-form" method="POST" action="update_profile.php" enctype="multipart/form-data">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
                
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                
                <label for="phone_number">Phone Number</label>
                <input type="text" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($phone_number); ?>" required>
                
                <label for="address">Address</label>
                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($address); ?>" required>
                
                <label for="gender">Gender</label>
                <select id="gender" name="gender" required>
                    <option value="Male" <?php if ($gender === 'Male') echo 'selected'; ?>>Male</option>
                    <option value="Female" <?php if ($gender === 'Female') echo 'selected'; ?>>Female</option>
                    <option value="Other" <?php if ($gender === 'Other') echo 'selected'; ?>>Other</option>
                </select>

                <label for="profile_picture">Profile Picture</label>
                <input type="file" id="profile_picture" name="profile_picture">

                <div class="form-buttons">
                    <button type="button" class="cancel-button" onclick="window.location.href='user_page.php'">Cancel</button>
                    <button type="submit" class="update-button">Update</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
