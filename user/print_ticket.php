<?php
session_start();

// Check if user is logged in (session should have user_id)
if (!isset($_SESSION['user_id'])) {
    die('User not logged in!');
}

// Check if ticket_id is set in the GET request
if (!isset($_GET['ticket_id'])) {
    die('Ticket ID not provided!');
}

$ticket_id = $_GET['ticket_id']; // Retrieve the ticket_id from URL parameter

// Database connection (replace with your actual database connection)
$pdo = new PDO('mysql:host=localhost;dbname=smart_it_helpdesk', 'root', ''); // Replace with actual DB details
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// SQL query to fetch the ticket details
$query = "
    SELECT ticket_id, user_id, assigned_to, category, title, priority, status, description, attachment, created_at, updated_at
    FROM tickets
    WHERE ticket_id = :ticket_id
";

$stmt = $pdo->prepare($query);
$stmt->bindParam(':ticket_id', $ticket_id, PDO::PARAM_INT);
$stmt->execute();

// Fetch the ticket details
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ticket) {
    die('Ticket not found!');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Ticket</title>
    <link rel="stylesheet" href="css/print_ticket.css">
</head>
<body>
    <div class="ticket-container">
        <div class="ticket-header">
            <h1>Ticket Details</h1>
        </div>
        <table class="ticket-details">
            <tr>
                <th>Ticket ID</th>
                <td><?php echo htmlspecialchars($ticket['ticket_id']); ?></td>
            </tr>
            <tr>
                <th>User ID</th>
                <td><?php echo htmlspecialchars($ticket['user_id']); ?></td>
            </tr>
            <tr>
                <th>Assigned To</th>
                <td><?php echo htmlspecialchars($ticket['assigned_to']); ?></td>
            </tr>
            <tr>
                <th>Category</th>
                <td><?php echo htmlspecialchars($ticket['category']); ?></td>
            </tr>
            <tr>
                <th>Title</th>
                <td><?php echo htmlspecialchars($ticket['title']); ?></td>
            </tr>
            <tr>
                <th>Priority</th>
                <td><?php echo htmlspecialchars($ticket['priority']); ?></td>
            </tr>
            <tr>
                <th>Status</th>
                <td><?php echo htmlspecialchars($ticket['status']); ?></td>
            </tr>
            <tr>
                <th>Description</th>
                <td><?php echo htmlspecialchars($ticket['description']); ?></td>
            </tr>
            <tr>
                <th>Attachment</th>
                <td>
                    <?php if ($ticket['attachment']): ?>
                        <a href="<?php echo htmlspecialchars($ticket['attachment']); ?>" target="_blank" class="attachment-link">View Attachment</a>
                    <?php else: ?>
                        No attachment
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <th>Created At</th>
                <td><?php echo htmlspecialchars($ticket['created_at']); ?></td>
            </tr>
            <tr>
                <th>Updated At</th>
                <td><?php echo htmlspecialchars($ticket['updated_at']); ?></td>
            </tr>
        </table>
        <button class="print-button" onclick="window.print()">Print</button>
    </div>
</body>
</html>
