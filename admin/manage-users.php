<?php
// Start the session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "User not logged in!";
    exit;
}

// Database connection
try {
    $pdo = new PDO('mysql:host=localhost;dbname=smart_it_helpdesk', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Handle AJAX requests for filtering
if (isset($_GET['ajax']) && $_GET['ajax'] == 'filter') {
    $role_filter = $_GET['role'] ?? 'all';
    $query = "SELECT user_id, name, email, gender, role FROM users";

    if ($role_filter !== 'all') {
        $query .= " WHERE role = :role";
    }

    $stmt = $pdo->prepare($query);

    if ($role_filter !== 'all') {
        $stmt->bindParam(':role', $role_filter, PDO::PARAM_STR);
    }

    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($users);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="css/manage-users.css">
    <script>
        function filterUsers() {
            const role = document.getElementById('role').value;
            fetch(`manage-users.php?ajax=filter&role=${role}`)
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById('usersTableBody');
                    tbody.innerHTML = '';
                    if (data.length > 0) {
                        data.forEach(user => {
                            const row = document.createElement('tr');
                            row.setAttribute('onclick', `window.location.href='user_update.php?id=${user.user_id}'`);
                            row.innerHTML = `
                                <td>${user.user_id}</td>
                                <td>${user.name}</td>
                                <td>${user.email}</td>
                                <td>${user.gender}</td>
                                <td>${user.role}</td>
                            `;
                            tbody.appendChild(row);
                        });
                    } else {
                        tbody.innerHTML = '<tr><td colspan="5">No users found.</td></tr>';
                    }
                })
                .catch(error => console.error('Error fetching users:', error));
        }
    </script>
</head>
<body>
    <h1>Manage Users</h1>
    <div class="filter-buttons">
       
        <select name="role" id="role" onchange="filterUsers()">
            <option value="all">All</option>
            <option value="Admin">Admin</option>
            <option value="IT Staff">IT Staff</option>
            <option value="User">User</option>
        </select>
    </div>
   
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
            <tbody id="usersTableBody">
            <!-- Initial table content -->
            <?php
            $query = "SELECT user_id, name, email, gender, role FROM users";
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($users as $user): ?>
                <tr onclick="window.location.href='user_update.php?id=<?php echo $user['user_id']; ?>'">
                    <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['gender']); ?></td>
                    <td><?php echo htmlspecialchars($user['role']); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
   
</body>
</html>
