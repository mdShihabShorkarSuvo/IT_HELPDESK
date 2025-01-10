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
        user_creator.name AS user_name,
        user_creator.email AS user_email,
        it_staff.name AS it_staff_name,
        it_staff.email AS it_staff_email
    FROM tickets
    LEFT JOIN users AS user_creator ON tickets.user_id = user_creator.user_id
    LEFT JOIN users AS it_staff ON tickets.assigned_to = it_staff.user_id
    WHERE tickets.ticket_id = :ticket_id
";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':ticket_id', $ticket_id, PDO::PARAM_INT);
$stmt->execute();
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch available IT staff for assignment
$query_it_staff = "SELECT user_id, name FROM users WHERE role = 'it_staff'";
$stmt_it_staff = $pdo->prepare($query_it_staff);
$stmt_it_staff->execute();
$it_staff_list = $stmt_it_staff->fetchAll(PDO::FETCH_ASSOC);

// Handle IT staff assignment
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $assigned_to = $_POST['assigned_to'];

    // Update the ticket's assigned IT staff
    $update_query = "UPDATE tickets SET assigned_to = :assigned_to WHERE ticket_id = :ticket_id";
    $stmt_update = $pdo->prepare($update_query);
    $stmt_update->bindParam(':assigned_to', $assigned_to, PDO::PARAM_INT);
    $stmt_update->bindParam(':ticket_id', $ticket_id, PDO::PARAM_INT);
    $stmt_update->execute();

    // Redirect to the same page to refresh the data
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
        /* Add your custom styling here */
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
            display: flex;
            justify-content: space-between;
            margin: 20px;
        }

        .ticket-details, .it-staff {
            width: 48%;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .ticket-details table, .it-staff select, .it-staff button {
            width: 100%;
            margin-top: 20px;
        }

        .ticket-details td, .ticket-details th {
            padding: 10px;
            border: 1px solid #ddd;
        }

        .it-staff select, .it-staff button {
            padding: 10px;
            font-size: 16px;
        }

        .it-staff button {
            margin-top: 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }

        .it-staff button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<h1>Manage Ticket #<?php echo htmlspecialchars($ticket['ticket_id']); ?></h1>

<div class="container">

    <!-- Ticket Details -->
    <div class="ticket-details">
        <h2>Ticket Information</h2>
        <table>
            <tr><th>Title</th><td><?php echo htmlspecialchars($ticket['title']); ?></td></tr>
            <tr><th>Priority</th><td><?php echo htmlspecialchars($ticket['priority']); ?></td></tr>
            <tr><th>Status</th><td><?php echo htmlspecialchars($ticket['status']); ?></td></tr>
            <tr><th>Deadline</th><td><?php echo htmlspecialchars($ticket['deadline']); ?></td></tr>
            <tr><th>Category</th><td><?php echo htmlspecialchars($ticket['category']); ?></td></tr>
            <tr><th>User Name</th><td><?php echo htmlspecialchars($ticket['user_name']); ?></td></tr>
            <tr><th>User Email</th><td><?php echo htmlspecialchars($ticket['user_email']); ?></td></tr>
        </table>
    </div>

    <!-- IT Staff Assignment -->
    <div class="it-staff">
        <h2>Assign IT Staff</h2>
        <form method="post" action="">
            <select name="assigned_to" required>
                <option value="">Select IT Staff</option>
                <?php foreach ($it_staff_list as $staff): ?>
                    <option value="<?php echo $staff['user_id']; ?>"
                        <?php echo ($ticket['assigned_to'] == $staff['user_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($staff['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Assign</button>
        </form>
    </div>

</div>

</body>
</html>
