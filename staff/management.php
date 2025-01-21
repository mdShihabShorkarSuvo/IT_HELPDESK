<?php
session_start();

// Database connection
$pdo = new PDO('mysql:host=localhost;dbname=smart_it_helpdesk', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Get ticket_id from URL parameter
if (!isset($_GET['ticket_id'])) {
    die("Ticket ID is required.");
}

$ticket_id = $_GET['ticket_id'];

// Fetch the ticket details
$query = "
    SELECT 
        tickets.ticket_id, 
        tickets.title, 
        tickets.priority, 
        tickets.status, 
        tickets.deadline, 
        tickets.category,
        tickets.assigned_to,
        tickets.description,
        tickets.attachment,
        user_creator.name AS user_name,
        user_creator.email AS user_email,
        it_staff.name AS it_staff_name,
        it_staff.email AS it_staff_email,
        it_staff.user_id AS it_staff_id
    FROM tickets
    LEFT JOIN users AS user_creator ON tickets.user_id = user_creator.user_id
    LEFT JOIN users AS it_staff ON tickets.assigned_to = it_staff.user_id
    WHERE tickets.ticket_id = :ticket_id
";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':ticket_id', $ticket_id, PDO::PARAM_INT);
$stmt->execute();
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle IT staff assignment and updates
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get values from the form
    $status = $_POST['status'];

    // Update the ticket's status
    $update_query = "
        UPDATE tickets 
        SET status = :status
        WHERE ticket_id = :ticket_id
    ";
    $stmt_update = $pdo->prepare($update_query);
    $stmt_update->bindParam(':status', $status);
    $stmt_update->bindParam(':ticket_id', $ticket_id, PDO::PARAM_INT);
    $stmt_update->execute();

    // Redirect to refresh the data
    header("Location: management.php?ticket_id=" . $ticket_id);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Ticket</title>
    <link rel="stylesheet" href="css/management.css">
    <script>
        function updateStatusColor() {
            var statusSelect = document.querySelector('select[name="status"]');
            var selectedOption = statusSelect.options[statusSelect.selectedIndex];
            var color = '';

            switch (selectedOption.value) {
                case 'Pending':
                    color = '#ffeb3b';
                    break;
                case 'In Progress':
                    color = '#03a9f4';
                    break;
                case 'Resolved':
                    color = '#4caf50';
                    break;
                case 'Escalated':
                    color = '#f44336';
                    break;
                default:
                    color = '#fff';
            }

            statusSelect.style.backgroundColor = color;
        }

        document.addEventListener('DOMContentLoaded', function() {
            updateStatusColor();
            document.querySelector('select[name="status"]').addEventListener('change', updateStatusColor);
        });
    </script>
</head>
<body>

<h1>Manage Ticket #<?php echo htmlspecialchars($ticket['ticket_id']); ?></h1>

<!-- Back Button -->
<div style="text-align: center; margin-top: 20px;">
    <a class="back-button" href="it_staff.php?page=assigned_task">Back</a>
</div>

<div class="container">

    <!-- Ticket Details -->
    <div class="ticket-details">
        <h2>Ticket Information</h2>
        <form method="post" action="">
            <table>
                <tr><th>Category</th><td><?php echo htmlspecialchars($ticket['category']); ?></td></tr>
                <tr><th>Title</th><td><?php echo htmlspecialchars($ticket['title']); ?></td></tr>
                <tr><th>Priority</th><td><?php echo htmlspecialchars($ticket['priority']); ?></td></tr>
                <tr class="status-row"><th>Status</th>
                    <td>
                        <select name="status" required>
                            <option value="Pending" <?php echo ($ticket['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                            <option value="In Progress" <?php echo ($ticket['status'] == 'In Progress') ? 'selected' : ''; ?>>In Progress</option>
                            <option value="Resolved" <?php echo ($ticket['status'] == 'Resolved') ? 'selected' : ''; ?>>Resolved</option>
                            <option value="Escalated" <?php echo ($ticket['status'] == 'Escalated') ? 'selected' : ''; ?>>Escalated</option>
                        </select>
                    </td>
                </tr>
                <tr><th>Deadline</th><td><?php echo htmlspecialchars($ticket['deadline']); ?></td></tr>
                <tr><th>User Name</th><td><?php echo htmlspecialchars($ticket['user_name']); ?></td></tr>
                <tr><th>User Email</th><td><?php echo htmlspecialchars($ticket['user_email']); ?></td></tr>
                <tr><th>Description</th><td><?php echo nl2br(htmlspecialchars($ticket['description'])); ?></td></tr>
                <?php if (!empty($ticket['attachment'])): ?>
                    <tr><th>Attachment</th>
                        <td>
                            <?php if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $ticket['attachment'])): ?>
                                <img src="<?php echo htmlspecialchars($ticket['attachment']); ?>" alt="Attachment"/>
                            <?php else: ?>
                                <a href="<?php echo htmlspecialchars($ticket['attachment']); ?>" target="_blank">View Attachment</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </table>
            <button type="submit">Update Ticket</button>
        </form>
    </div>
</div>

</body>
</html>