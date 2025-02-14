<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

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

// Check if a status filter is set via GET or POST parameters
if (isset($_GET['status'])) {
    $status_filter = $_GET['status'];
} elseif (isset($_POST['status'])) {
    $status_filter = $_POST['status'];
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

// Check if the request is an AJAX request
if (isset($_POST['ajax']) && $_POST['ajax'] == 'true') {
    // Return the tickets as JSON
    echo json_encode($tickets);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assigned Tasks</title>
    <link rel="stylesheet" href="css/assigned_task.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Handle the filter change
            $('select[name="status"]').change(function() {
                var status = $(this).val();
                $.ajax({
                    type: 'POST',
                    url: 'assigned_task.php',
                    data: {
                        status: status,
                        ajax: 'true'
                    },
                    success: function(response) {
                        var tickets = JSON.parse(response);
                        var tbody = $('table tbody');
                        tbody.empty();
                        if (tickets.length === 0) {
                            tbody.append('<tr><td colspan="8">No tickets found for the selected status.</td></tr>');
                        } else {
                            $.each(tickets, function(index, ticket) {
                                var row = '<tr onclick="window.location.href=\'management.php?ticket_id=' + ticket.ticket_id + '\'">' +
                                    '<td>' + ticket.ticket_id + '</td>' +
                                    '<td>' + ticket.user_name + '</td>' +
                                    '<td>' + ticket.user_email + '</td>' +
                                    '<td>' + ticket.category + '</td>' +
                                    '<td>' + ticket.title + '</td>' +
                                    '<td>' + ticket.priority + '</td>' +
                                    '<td class="status">' + ticket.status + '</td>' +
                                    '<td>' + ticket.deadline + '</td>' +
                                    '</tr>';
                                tbody.append(row);
                            });
                        }
                    }
                });
            });
        });
    </script>
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
            <select name="status">
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