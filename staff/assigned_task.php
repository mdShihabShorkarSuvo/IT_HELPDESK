<?php


// Check if the user is logged in and is an IT staff
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'it_staff') {
    die('Access denied.');
}

$user_id = $_SESSION['user_id']; // Get the IT staff's user ID from the session

// Database connection (replace with your actual database details)
$pdo = new PDO('mysql:host=localhost;dbname=smart_it_helpdesk', 'root', ''); // Replace with actual DB details
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Default status filter is 'all'
$status_filter = 'all';

// Check if a status filter is set via GET parameters
if (isset($_GET['status'])) {
    $status_filter = $_GET['status'];
}

// SQL query to fetch tickets assigned to the logged-in IT staff
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
    WHERE tickets.assigned_to = :user_id
";

// Add a condition for status if it's set and not 'all'
if ($status_filter && $status_filter != 'all') {
    $query .= " AND tickets.status = :status";
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

// Bind the user ID
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

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
    <title>Assigned Tasks</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 20px;
        background-color: #f8f9fc;
    }

    h1 {
        text-align: center;
        color: #4e73df;
    }

    .filter-buttons {
        margin-bottom: 20px;
        text-align: center;
    }

    .filter-buttons select {
        padding: 10px;
        font-size: 14px;
        border: 1px solid #ccc;
        border-radius: 4px;
        outline: none;
        cursor: pointer;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        background-color: #ffffff;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        overflow: hidden;
    }

    table thead {
        background-color: #4e73df;
        color: white;
    }

    table thead th {
        padding: 12px 15px;
        text-align: left;
        font-size: 14px;
    }

    table tbody tr {
        transition: background-color 0.3s ease;
    }

    table tbody tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    table tbody tr:hover {
        background-color: #d1e7dd;
    }

    table tbody tr td {
        padding: 12px 15px;
        font-size: 14px;
        text-align: left; /* Default left alignment */
    }

    table tbody tr td.priority-high {
        color: #e74a3b;
        font-weight: bold;
    }

    table tbody tr td.priority-medium {
        color: #f6c23e;
    }

    table tbody tr td.priority-low {
        color: #1cc88a;
    }

    .filter-buttons {
    margin-bottom: 20px;
    text-align: left;
}

</style>


</head>
<body>
    <h1>Assigned Tasks</h1>

    <!-- Filter Dropdown -->
    <div class="filter-buttons">
        <?php
        // Retain the 'page' parameter dynamically
        $current_page = isset($_GET['page']) ? htmlspecialchars($_GET['page']) : '';
        ?>
        <form method="get" action="it_staff.php">
            <input type="hidden" name="page" value="<?php echo $current_page; ?>">
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
                    <th>User Name</th>
                    <th>User Email</th>
                    <th>Category</th>
                    <th>Title</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Deadline</th>
                   
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
    <td class="status"><?php echo htmlspecialchars($ticket['status']); ?></td>
    <td><?php echo htmlspecialchars($ticket['deadline']); ?></td>
</tr>

        <?php endforeach; ?>
    </tbody>
        </table>
    <?php endif; ?>
</body>
</html>