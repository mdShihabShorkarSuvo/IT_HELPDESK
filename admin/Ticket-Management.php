<?php
// Database connection (replace with your actual database details)
$pdo = new PDO('mysql:host=localhost;dbname=smart_it_helpdesk', 'root', ''); // Replace with actual DB details
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Default status filter is 'all'
$status_filter = 'all';

// Check if a status filter is set via GET parameters
if (isset($_GET['status'])) {
    $status_filter = $_GET['status'];
}

// SQL query to fetch tickets along with user and IT staff details
$query = "
    SELECT 
        tickets.ticket_id, 
        tickets.assigned_to, 
        tickets.category, 
        tickets.title, 
        tickets.priority, 
        tickets.status, 
        tickets.deadline, 
        user_creator.name AS user_name, 
        user_creator.email AS user_email, 
        it_staff.name AS it_staff_name, 
        it_staff.email AS it_staff_email
    FROM tickets
    LEFT JOIN users AS user_creator 
        ON tickets.user_id = user_creator.user_id 
    LEFT JOIN users AS it_staff 
        ON tickets.assigned_to = it_staff.user_id 
        AND it_staff.role = 'it_staff'
";

// Add a condition for status if it's set and not 'all'
if ($status_filter && $status_filter != 'all') {
    $query .= " WHERE tickets.status = :status";
}

// Sort tickets by priority and deadline
$query .= " ORDER BY 
    CASE 
        WHEN tickets.priority = 'High' THEN 1
        WHEN tickets.priority = 'Medium' THEN 2
        WHEN tickets.priority = 'Low' THEN 3
    END, tickets.deadline ASC";

// Prepare the query
$stmt = $pdo->prepare($query);

// Bind the status filter if it's set and not 'all'
if ($status_filter && $status_filter != 'all') {
    $stmt->bindParam(':status', $status_filter, PDO::PARAM_STR);
}

// Execute the query
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
    <link rel="stylesheet" href="css/ticket-management.css">
</head>
<body>
    <h1>All Tickets</h1>

    <!-- Filter Dropdown -->
    <div class="filter-buttons">
        <?php
        $current_page = isset($_GET['page']) ? htmlspecialchars($_GET['page']) : '';
        ?>
        <form method="get" action="admin_page.php">
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
            <p>No tickets found for the selected status.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Ticket ID</th>
                        <th>User Name</th>
                        <th>User Email</th>
                        <th>Category</th>
                        <th>Title</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Deadline</th>
                        <th>Assigned To</th>
                        <th>IT Staff ID</th>
                        <th>IT Staff Email</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tickets as $ticket): ?>
                        <tr onclick="window.location.href='management.php?ticket_id=<?php echo $ticket['ticket_id']; ?>'">
                            <td><?php echo htmlspecialchars($ticket['ticket_id']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['user_name']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['user_email']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['category']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['title']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['priority']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['status']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['deadline']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['it_staff_name']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['assigned_to']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['it_staff_email']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>
</html>
