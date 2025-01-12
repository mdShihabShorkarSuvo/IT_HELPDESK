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
    <style>
        /* Custom styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            margin: 20px;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .ticket-details table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        .ticket-details th, .ticket-details td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .ticket-details th {
            background-color: #f4f4f4;
        }

        .ticket-details img {
            max-width: 100%;
            height: auto;
            border: 2px solid black;
            padding: 5px;
            box-sizing: border-box;
        }

        select[name="status"] {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #fff;
            color: #333;
            width: 100%;
            box-sizing: border-box;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url('data:image/svg+xml;charset=US-ASCII,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 4 5"><path fill="none" stroke="currentColor" stroke-width=".5" d="M2 0L0 2h4zm0 5L0 3h4z"/></svg>');
            background-repeat: no-repeat;
            background-position: right 10px center;
            background-size: 8px 10px;
        }

        select[name="status"] option[value="Pending"] {
            background-color: #ffeb3b;
        }

        select[name="status"] option[value="In Progress"] {
            background-color: #03a9f4;
        }

        select[name="status"] option[value="Resolved"] {
            background-color: #4caf50;
        }

        select[name="status"] option[value="Escalated"] {
            background-color: #f44336;
        }

        button[type="submit"] {
            background-color: #4CAF50;
            padding: 10px 20px;
            color: white;
            border: none;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            width: 100%;
            margin-top: 20px;
        }

        button[type="submit"]:hover {
            background-color: #45a049;
        }

        .back-button {
            background-color: rgb(234, 28, 25);
            color: white;
            padding: 10px 20px;
            border: 1px solid black;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
            margin-top: 20px;
            display: inline-block;
            text-align: center;
            text-decoration: none;
        }

        .back-button:hover {
            background-color: #ddd;
            color: black;
        }

        .status-row {
            background-color: #f4f4f4;
        }

        .status-row select {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: none;
            background-color: transparent;
        }
    </style>
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
    <a class="back-button" href="it_staff.php">Back</a>
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