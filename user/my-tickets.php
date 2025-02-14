<?php

// Database connection (replace with your actual database details)
$mysqli = new mysqli('localhost', 'root', '', 'smart_it_helpdesk'); // Replace with actual DB details
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Check if the user is logged in (i.e., user_id should be in the session)
if (!isset($_SESSION['user_id'])) {
    die("You need to log in first.");
}

// Get the current user ID from session
$user_id = $_SESSION['user_id'];

// Get the current page and status filter value from the GET request
$current_page = isset($_GET['page']) ? htmlspecialchars($_GET['page']) : '';

// Initialize the status filter variable
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';

// SQL query to fetch tickets for the current user with an optional status filter
$query = "
    SELECT 
        tickets.ticket_id, 
        tickets.category, 
        tickets.title, 
        tickets.priority, 
        tickets.status, 
        tickets.deadline, 
        it_staff.name AS it_staff_name
    FROM tickets
    LEFT JOIN users AS it_staff 
        ON tickets.assigned_to = it_staff.user_id
    WHERE (tickets.user_id = ?)
    AND (? = 'all' OR tickets.status = ?)
    ORDER BY CASE 
        WHEN priority = 'High' THEN 1
        WHEN priority = 'Medium' THEN 2
        WHEN priority = 'Low' THEN 3
    END, deadline ASC
";

// Prepare the query
$stmt = $mysqli->prepare($query);

// Bind the parameters (user_id and status filter)
if ($status_filter == 'all') {
    $stmt->bind_param("iss", $user_id, $status_filter, $status_filter); // For all status, bind it twice for compatibility
} else {
    $stmt->bind_param("iss", $user_id, $status_filter, $status_filter); // For specific status
}

// Execute the query
$stmt->execute();

// Get the result
$result = $stmt->get_result();

// Fetch all the tickets
$tickets = $result->fetch_all(MYSQLI_ASSOC);

// Close the statement and connection
$stmt->close();
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Tickets</title>
    <link rel="stylesheet" href="css/my-tickets.css"> <!-- Link to external CSS file -->
</head>
<body>
    <h1>My Tickets</h1>

    <!-- Status Filter -->
    <div class="filter-buttons">
        <form method="get" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>"> <!-- Action points to the current page -->
            <input type="hidden" name="page" value="<?php echo $current_page; ?>">
            <select name="status" onchange="this.form.submit()">
                <option value="all" <?php echo ($status_filter == 'all') ? 'selected' : ''; ?>>All Status</option>
                <option value="Pending" <?php echo ($status_filter == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                <option value="In Progress" <?php echo ($status_filter == 'In Progress') ? 'selected' : ''; ?>>In Progress</option>
                <option value="Resolved" <?php echo ($status_filter == 'Resolved') ? 'selected' : ''; ?>>Resolved</option>
                <option value="Escalated" <?php echo ($status_filter == 'Escalated') ? 'selected' : ''; ?>>Escalated</option>
            </select>
        </form>
    </div>

    <div class="table-container">
        <?php if (empty($tickets)): ?>
            <p>No tickets found.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Ticket ID</th>
                        <th>Category</th>
                        <th>Title</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Deadline</th>
                        <th>Assigned To (IT Staff)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tickets as $ticket): ?>
                        <tr onclick="window.location.href='print_ticket.php?ticket_id=<?php echo $ticket['ticket_id']; ?>'">
                            <td><?php echo htmlspecialchars($ticket['ticket_id']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['category']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['title']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['priority']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['status']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['deadline']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['it_staff_name']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
