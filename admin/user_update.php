<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "User not logged in!";
    exit;
}

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

// Fetch user details
$query = "SELECT user_id, name, email, gender, role, profile_picture FROM users WHERE user_id = :user_id";
$stmt = $pdo->prepare($query);
$stmt->execute(['user_id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "User not found!";
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update User</title>
    <style>
        form {
            max-width: 700px;
            margin: 50px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background: #f9f9f9;
        }
        label {
            display: block;
            margin-bottom: 8px;
        }
        input, select {
            width: 100%;
            padding: 8px;
            margin-bottom: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        button {
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .profile-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 10px;
        }

        .cancel-button:hover {
            background-color: #e53935;}

            .cancel-button {
            background-color:rgb(66, 63, 63);}


            .button-container {
            display: flex;
            justify-content: space-between;
            gap: 10px; /* Adds space between the buttons */
        }


    </style>
</head>
<body>

<form method="POST" action="user_process.php?id=<?php echo urlencode($user['user_id']); ?>" enctype="multipart/form-data">

        <h2>Update User</h2>

   
        <!-- Display the current profile picture -->
        <?php if ($user['profile_picture']): ?>
            <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" class="profile-image">
        <?php endif; ?>

        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

        <label for="gender">Gender:</label>
        <select id="gender" name="gender" required>
            <option value="male" <?php echo ($user['gender'] === 'male') ? 'selected' : ''; ?>>Male</option>
            <option value="female" <?php echo ($user['gender'] === 'female') ? 'selected' : ''; ?>>Female</option>
            <option value="other" <?php echo ($user['gender'] === 'other') ? 'selected' : ''; ?>>Other</option>
        </select>

        <label for="role">Role:</label>
        <select id="role" name="role" required>
            <option value="admin" <?php echo ($user['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
            <option value="it_staff" <?php echo ($user['role'] === 'it_staff') ? 'selected' : ''; ?>>IT Staff</option>
            <option value="user" <?php echo ($user['role'] === 'user') ? 'selected' : ''; ?>>User</option>
        </select>

        <label for="password">Password (leave blank to keep current):</label>
        <input type="password" id="password" name="password">

        <label for="profile_picture">Profile Picture (Optional):</label>
        <input type="file" id="profile_picture" name="profile_picture" accept="image/*">

        <div class="button-container">
            <button type="submit">Update</button>
            <a href="admin_page.php?page=manage-users"><button type="button" class="cancel-button">Cancel</button></a>
        </div>
    </form>
</body>
</html>
