<?php
session_start(); // Start the session to access user_id

// Check if user is logged in (session should have user_id)
if (!isset($_SESSION['user_id'])) {
    die('User not logged in!');
}

$user_id = $_SESSION['user_id']; // Get the user_id from session

// Database connection (replace with your actual database connection)
$pdo = new PDO('mysql:host=localhost;dbname=smart_it_helpdesk', 'root', ''); // Replace with actual DB details
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Default status filter is 'all'
$status_filter = 'all';

// Check if a status filter is set
if (isset($_GET['status'])) {
    $status_filter = $_GET['status'];
}

// SQL query to fetch tickets related to the logged-in user, optionally filtered by status
$query = "
    SELECT ticket_id, assigned_to, category, title, priority, status, deadline
    FROM tickets
    WHERE user_id = :user_id
";

// If a status filter is set, add a condition for the status
if ($status_filter && $status_filter != 'all') {
    $query .= " AND status = :status";
}

// Optional: Sorting tickets by priority and deadline
$query .= " ORDER BY priority DESC, deadline DESC";

// Prepare and execute the query
$stmt = $pdo->prepare($query);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

// Bind the status filter if it's set and not 'all'
if ($status_filter && $status_filter != 'all') {
    $stmt->bindParam(':status', $status_filter, PDO::PARAM_STR);
}

$stmt->execute();

// Fetch the tickets
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Tickets</title>
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
        .filter-buttons button {
            padding: 8px 12px;
            margin-right: 10px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h1>My Tickets</h1>

    <!-- Filter Dropdown -->
    <div class="filter-buttons">
        <form method="get" action="">
            <select name="status" onchange="this.form.submit()">
                <option value="all" <?php echo ($status_filter == 'all') ? 'selected' : ''; ?>>All Status</option>
                <option value="Pending" <?php echo ($status_filter == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                <option value="In Progress" <?php echo ($status_filter == 'In Progress') ? 'selected' : ''; ?>>In Progress</option>
                <option value="Resolved" <?php echo ($status_filter == 'Resolved') ? 'selected' : ''; ?>>Resolved</option>
            </select>
        </form>
    </div>

    <?php if (empty($tickets)): ?>
        <p>No tickets found for the selected status.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Ticket ID</th>
                    <th>Assigned To</th>
                    <th>Category</th>
                    <th>Title</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Deadline</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tickets as $ticket): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($ticket['ticket_id']); ?></td>
                        <td><?php echo htmlspecialchars($ticket['assigned_to']); ?></td>
                        <td><?php echo htmlspecialchars($ticket['category']); ?></td>
                        <td><?php echo htmlspecialchars($ticket['title']); ?></td>
                        <td><?php echo htmlspecialchars($ticket['priority']); ?></td>
                        <td><?php echo htmlspecialchars($ticket['status']); ?></td>
                        <td><?php echo htmlspecialchars($ticket['deadline']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
