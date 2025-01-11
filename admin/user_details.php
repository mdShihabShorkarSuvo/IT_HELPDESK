<?php
// Start session and check login
session_start();
if (!isset($_SESSION['user_id'])) {
    die("Access denied.");
}

// Database connection
try {
    $pdo = new PDO('mysql:host=localhost;dbname=smart_it_helpdesk', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Fetch user details if user_id is provided
$user = null;
if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
    $query = "SELECT * FROM users WHERE user_id = :user_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("User not found.");
    }
}

// Handle form submission for updating user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'update') {
        $user_id = $_POST['user_id'];
        $name = $_POST['name'];
        $address = $_POST['address'];
        $gender = $_POST['gender'];
        $email = $_POST['email'];
        $role = $_POST['role'];

        $update_query = "UPDATE users SET name = :name, address = :address, gender = :gender, email = :email, role = :role WHERE user_id = :user_id";
        $update_stmt = $pdo->prepare($update_query);
        $update_stmt->execute([
            ':name' => $name,
            ':address' => $address,
            ':gender' => $gender,
            ':email' => $email,
            ':role' => $role,
            ':user_id' => $user_id
        ]);
        echo "<script>alert('User updated successfully.'); window.location.href='manage_users.php';</script>";
    } elseif ($action === 'delete') {
        $user_id = $_POST['user_id'];
        $delete_query = "DELETE FROM users WHERE user_id = :user_id";
        $delete_stmt = $pdo->prepare($delete_query);
        $delete_stmt->execute([':user_id' => $user_id]);
        echo "<script>alert('User deleted successfully.'); window.location.href='manage_users.php';</script>";
    } elseif ($action === 'add') {
        $name = $_POST['name'];
        $address = $_POST['address'];
        $gender = $_POST['gender'];
        $email = $_POST['email'];
        $role = $_POST['role'];

        $add_query = "INSERT INTO users (name, address, gender, email, role) VALUES (:name, :address, :gender, :email, :role)";
        $add_stmt = $pdo->prepare($add_query);
        $add_stmt->execute([
            ':name' => $name,
            ':address' => $address,
            ':gender' => $gender,
            ':email' => $email,
            ':role' => $role
        ]);
        echo "<script>alert('New user added successfully.'); window.location.href='manage_users.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
        }

        .container {
            max-width: 600px;
            margin: 2rem auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
        }

        label {
            display: block;
            margin: 10px 0 5px;
        }

        input, select, button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            background-color: #007BFF;
            color: white;
            border: none;
        }

        button:hover {
            background-color: #0056b3;
        }

        .delete-button {
            background-color: #FF0000;
        }

        .delete-button:hover {
            background-color: #cc0000;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>User Details</h1>

    <?php if ($user): ?>
        <form method="post" action="">
            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['user_id']); ?>">
            <input type="hidden" name="action" value="update">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>

            <label for="address">Address:</label>
            <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user['address']); ?>" required>

            <label for="gender">Gender:</label>
            <select id="gender" name="gender" required>
                <option value="Male" <?php echo ($user['gender'] === 'Male') ? 'selected' : ''; ?>>Male</option>
                <option value="Female" <?php echo ($user['gender'] === 'Female') ? 'selected' : ''; ?>>Female</option>
            </select>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <label for="role">Role:</label>
            <select id="role" name="role" required>
                <option value="Admin" <?php echo ($user['role'] === 'Admin') ? 'selected' : ''; ?>>Admin</option>
                <option value="IT Staff" <?php echo ($user['role'] === 'IT Staff') ? 'selected' : ''; ?>>IT Staff</option>
                <option value="User" <?php echo ($user['role'] === 'User') ? 'selected' : ''; ?>>User</option>
            </select>

            <button type="submit">Update User</button>
        </form>

        <form method="post" action="">
            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['user_id']); ?>">
            <input type="hidden" name="action" value="delete">
            <button type="submit" class="delete-button">Delete User</button>
        </form>
    <?php endif; ?>

    <h2>Add New User</h2>
    <form method="post" action="">
        <input type="hidden" name="action" value="add">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>

        <label for="address">Address:</label>
        <input type="text" id="address" name="address" required>

        <label for="gender">Gender:</label>
        <select id="gender" name="gender" required>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
        </select>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="role">Role:</label>
        <select id="role" name="role" required>
            <option value="Admin">Admin</option>
            <option value="IT Staff">IT Staff</option>
            <option value="User">User</option>
        </select>

        <button type="submit">Add User</button>
    </form>
</div>
</body>
</html>
