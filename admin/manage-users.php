<?php


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

// Fetch all users initially
$query = "SELECT user_id, name, address, gender, email, role FROM users";
$stmt = $pdo->prepare($query);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <style>
       table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px 12px;
            text-align: left;
        }
        .filter-buttons {
            margin-bottom: 20px;
        }
        .filter-buttons select, .filter-buttons button {
            padding: 8px 12px;
            margin-right: 10px;
            cursor: pointer;
        }


        /* Table Styles */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

table, th, td {
    border: 1px solid black;
}

th, td {
    padding: 8px 12px;
    text-align: left;
}

/* Make table rows clickable */
table tbody tr {
    cursor: pointer; /* Change cursor to pointer on hover */
    transition: background-color 0.3s ease; /* Smooth transition for background color */
}

/* Change background color when hovering over rows */
table tbody tr:hover {
    background-color:rgb(38, 196, 56); /* Light gray background on hover */
}

/* Optional: Highlight the row when clicked */
table tbody tr:active {
    background-color: #d3d3d3; /* Slightly darker gray when clicked */
}

    </style>
</head>
<body>
<div class="container">
    <h1>Manage Users</h1>
    <label for="role">Filter by Role:</label>
    <select name="role" id="role">
        <option value="all">All</option>
        <option value="Admin">Admin</option>
        <option value="IT Staff">IT Staff</option>
        <option value="User">User</option>
    </select>

    <table id="usersTable">
        <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Gender</th>
            <th>Role</th>
        </tr>
        </thead>
        <tbody>
        <!-- Display all users initially -->
        <?php foreach ($users as $user): ?>
            <tr onclick="window.location.href='user_update.php?id=<?php echo $user['user_id']; ?>'">

                <td class="user-id"><?php echo htmlspecialchars($user['user_id']); ?></td>
                <td><?php echo htmlspecialchars($user['name']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo htmlspecialchars($user['gender']); ?></td>
                <td><?php echo htmlspecialchars($user['role']); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>


</body>
</html>
