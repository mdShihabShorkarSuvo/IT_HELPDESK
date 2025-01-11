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
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
        }

        .container {
            max-width: 900px;
            margin: 2rem auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        h1 {
            margin-bottom: 1rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #f9f9f9;
        }

        .user-id {
            color: blue;
            text-decoration: underline;
            cursor: pointer;
        }

        select {
            padding: 8px;
            margin-bottom: 20px;
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
            <th>Address</th>
            <th>Gender</th>
            <th>Email</th>
            <th>Role</th>
        </tr>
        </thead>
        <tbody>
        <!-- Display all users initially -->
        <?php foreach ($users as $user): ?>
            <tr>
                <td class="user-id" onclick="goToDetails(<?php echo $user['user_id']; ?>)">
                    <?php echo htmlspecialchars($user['user_id']); ?>
                </td>
                <td><?php echo htmlspecialchars($user['name']); ?></td>
                <td><?php echo htmlspecialchars($user['address']); ?></td>
                <td><?php echo htmlspecialchars($user['gender']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo htmlspecialchars($user['role']); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    // Handle clicking on the ID column to navigate to user_details.php
    function goToDetails(userId) {
        // Redirect to user_details.php with the user ID as a query parameter
        window.location.href = `user_details.php?user_id=${userId}`;
    }

    // Handle role filtering
    document.getElementById('role').addEventListener('change', function () {
        const role = this.value;

        // Create an AJAX request
        fetch(`fetch_users.php?role=${role}`)
            .then(response => response.json())
            .then(users => {
                // Clear the table body
                const tbody = document.querySelector('#usersTable tbody');
                tbody.innerHTML = '';

                // Populate the table with filtered users
                users.forEach(user => {
                    const row = `
                        <tr>
                            <td class="user-id" onclick="goToDetails(${user.user_id})">${user.user_id}</td>
                            <td>${user.name}</td>
                            <td>${user.address}</td>
                            <td>${user.gender}</td>
                            <td>${user.email}</td>
                            <td>${user.role}</td>
                        </tr>
                    `;
                    tbody.innerHTML += row;
                });
            })
            .catch(error => console.error('Error:', error));
    });
</script>
</body>
</html>
