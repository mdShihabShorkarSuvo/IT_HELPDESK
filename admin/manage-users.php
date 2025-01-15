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
    <style>
        body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 20px;
    background-color: #f8f9fa;
}

h1 {
    text-align: center;
    color: #343a40;
}

.filter-buttons {
    margin-bottom: 20px;
    text-align: left;
}

.filter-buttons select {
    padding: 10px;
    font-size: 14px;
    border: 1px solid #ccc;
    border-radius: 4px;
    background-color: #fff;
    cursor: pointer;
}

.table-container {
    margin-top: 20px;
    max-height: 400px; /* Set a fixed height for the table container */
    overflow-y: auto; /* Enable vertical scrolling */
    border: 1px solid #ddd;
    background-color: #ffffff;
}

table {
    width: 100%; /* Ensure the table spans the container's width */
    border-collapse: collapse;
    text-align: left;
    table-layout: fixed; /* Prevent horizontal scrolling due to content overflow */
}

th {
    background-color: #007bff;
    color: #fff;
    padding: 10px;
    text-align: left;
    position: sticky; /* Sticky header */
    top: 0;
    z-index: 2;
}

td {
    padding: 10px;
    border-bottom: 1px solid #ddd;
    word-wrap: break-word; /* Prevent content overflow */
}

tbody tr:nth-child(even) {
    background-color: #f2f2f2;
}

tbody tr:hover {
    background-color: #e9ecef;
}

tbody tr:active {
    background-color: #d3d3d3;
}

/* Custom scrollbar for the table container */
.table-container::-webkit-scrollbar {
    width: 12px;
}

.table-container::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.table-container::-webkit-scrollbar-thumb {
    background-color: #888;
    border-radius: 6px;
}

.table-container::-webkit-scrollbar-thumb:hover {
    background: #555;
}


    </style>
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
