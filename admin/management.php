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

// Fetch available IT staff for assignment
$query_it_staff = "SELECT user_id, name FROM users WHERE role = 'it_staff'";
$stmt_it_staff = $pdo->prepare($query_it_staff);
$stmt_it_staff->execute();
$it_staff_list = $stmt_it_staff->fetchAll(PDO::FETCH_ASSOC);

// Handle IT staff assignment and updates
// Handle IT staff assignment and updates
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get values from the form
    $assigned_to = $_POST['assigned_to'];
    $deadline = $_POST['deadline'];
    $priority = $_POST['priority'];
    $status = $_POST['status'];

    // If "Remove IT Staff" is clicked (assigned_to is empty), set assigned_to to NULL
    if (empty($assigned_to)) {
        $assigned_to = NULL; // Set it to NULL if no IT staff is selected
    }

    // Update the ticket's assigned IT staff, deadline, priority, and status
    $update_query = "
        UPDATE tickets 
        SET assigned_to = :assigned_to, deadline = :deadline, priority = :priority, status = :status
        WHERE ticket_id = :ticket_id
    ";
    $stmt_update = $pdo->prepare($update_query);
    $stmt_update->bindParam(':assigned_to', $assigned_to, PDO::PARAM_INT);
    $stmt_update->bindParam(':deadline', $deadline);
    $stmt_update->bindParam(':priority', $priority);
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
            display: flex;
            justify-content: space-between;
            margin: 20px;
        }

        .ticket-details {
            width: 70%;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-right: 3px solid black; /* Black vertical line between the tables */
        }

        .it-staff {
            width: 27%;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .ticket-details table, .it-staff table, .it-staff input, .it-staff button {
            width: 100%;
            margin-top: 20px;
        }

        .ticket-details td, .ticket-details th, .it-staff td, .it-staff th {
            padding: 10px;
            border: 1px solid #ddd;
        }

        .it-staff table {
            width: 100%;
            max-height: 300px;
            overflow-y: auto;
        }

        .it-staff td {
            cursor: pointer;
        }

        .it-staff td.selected {
            background-color: #4CAF50;
            color: white;
        }

        .it-staff td:hover {
            background-color: #4CAF50;
            color: white;
        }

        .it-staff input, .it-staff button {
            padding: 10px;
            font-size: 16px;
        }

        .it-staff button {
            margin-top: 20px;
            background-color: rgb(84, 76, 175);
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        .it-staff button:hover {
            background-color: #3f51b5;
        }

        input[type="date"], select {
            padding: 10px;
            font-size: 16px;
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

        /* Back Button Styling */
        .back-button {
            background-color:rgb(234, 28, 25);
            color: white;
            padding: 10px 20px;
            border: 1px solid black;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
            margin-top: 20px;
        }



        .back-button:hover {
            background-color: #ddd;
        }
       /* Adjust image size to fit within a specific container */
/* Adjust image size to fit within a specific container and add a black border */
.ticket-details img {
    max-width: 300px; /* Set maximum width */
    max-height: 200px; /* Set maximum height */
    width: auto; /* Maintain aspect ratio */
    height: auto; /* Maintain aspect ratio */
    object-fit: contain; /* Ensure the image scales correctly without distortion */
    border: 2px solid black; /* Black border around the image */
    padding: 5px; /* Optional: Adds padding between the image and the border */
    box-sizing: border-box; /* Ensures padding and border are included in the image's total width/height */
}



    </style>
</head>
<body>

<h1>Manage Ticket #<?php echo htmlspecialchars($ticket['ticket_id']); ?></h1>

<!-- Back Button -->
<div style="text-align: center; margin-top: 20px;">
    <button class="back-button" onclick="window.location.href='admin_page.php';">Back</button>
</div>

<div class="container">

    <!-- Ticket Details -->
    <div class="ticket-details">
        <h2>Ticket Information</h2>
        <form method="post" action="">
            <table>
                <tr><th>Title</th><td><?php echo htmlspecialchars($ticket['title']); ?></td></tr>
                <tr><th>Priority</th>
                    <td>
                        <select name="priority" required>
                            <option value="Low" <?php echo ($ticket['priority'] == 'Low') ? 'selected' : ''; ?>>Low</option>
                            <option value="Medium" <?php echo ($ticket['priority'] == 'Medium') ? 'selected' : ''; ?>>Medium</option>
                            <option value="High" <?php echo ($ticket['priority'] == 'High') ? 'selected' : ''; ?>>High</option>
                        </select>
                    </td>
                </tr>
                <tr><th>Status</th>
                    <td>
                        <select name="status" required>
                            <option value="Pending" <?php echo ($ticket['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                            <option value="In Progress" <?php echo ($ticket['status'] == 'In Progress') ? 'selected' : ''; ?>>In Progress</option>
                            <option value="Resolved" <?php echo ($ticket['status'] == 'Resolved') ? 'selected' : ''; ?>>Resolved</option>
                            <option value="Escalated" <?php echo ($ticket['status'] == 'Escalated') ? 'selected' : ''; ?>>Escalated</option>
                            
                        </select>
                    </td>
                </tr>
                <tr><th>Deadline</th><td><input type="date" name="deadline" value="<?php echo htmlspecialchars($ticket['deadline']); ?>" min="<?php echo date('Y-m-d'); ?>" ></td></tr>
                <tr><th>Category</th><td><?php echo htmlspecialchars($ticket['category']); ?></td></tr>
                <tr><th>User Name</th><td><?php echo htmlspecialchars($ticket['user_name']); ?></td></tr>
                <tr><th>User Email</th><td><?php echo htmlspecialchars($ticket['user_email']); ?></td></tr>
                <tr><th>Assigned IT Staff</th><td id="assigned-staff"><?php echo htmlspecialchars($ticket['it_staff_name']); ?></td></tr>
                <!-- Ticket Description -->
                <tr><th>Description</th><td><?php echo nl2br(htmlspecialchars($ticket['description'])); ?></td></tr>
                <!-- Attachment Section -->
                <?php if (!empty($ticket['attachment'])): ?>
                    <tr><th>Attachment</th>
                        <td>
                            <!-- Display the image if the attachment is an image file -->
                            <?php if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $ticket['attachment'])): ?>
                                <img src="<?php echo htmlspecialchars($ticket['attachment']); ?>" alt="Attachment" style="max-width: 100%; height: auto;"/>
                            <?php else: ?>
                                <a href="<?php echo htmlspecialchars($ticket['attachment']); ?>" target="_blank">View Attachment</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <tr><th>Remove Assignment</th><td><button type="button" id="remove-assignment">Remove IT Staff</button></td></tr>
                
                <?php endif; ?>
            </table>
            <input type="hidden" name="assigned_to" id="assigned_to" value="<?php echo $ticket['assigned_to']; ?>" required>
            <button type="submit">Update Ticket</button>
        </form>
    </div>

    <!-- IT Staff Assignment -->
    <div class="it-staff">
        <h2>Assign IT Staff</h2>
        <table>
            <thead>
                <tr><th>ID</th><th>Name</th></tr>
            </thead>
            <tbody>
                <?php foreach ($it_staff_list as $staff): ?>
                    <tr data-id="<?php echo $staff['user_id']; ?>" class="staff-item">
                        <td><?php echo $staff['user_id']; ?></td>
                        <td><?php echo htmlspecialchars($staff['name']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>

<script>
    // JavaScript to handle staff selection
    const staffRows = document.querySelectorAll('.staff-item');
    const assignedToInput = document.getElementById('assigned_to');
    const assignedStaffDisplay = document.getElementById('assigned-staff');
    const removeAssignmentButton = document.getElementById('remove-assignment');

    staffRows.forEach(row => {
        row.addEventListener('click', () => {
            // Highlight selected staff row
            staffRows.forEach(item => item.classList.remove('selected'));
            row.classList.add('selected');

            // Set assigned IT staff ID to hidden input
            const staffId = row.getAttribute('data-id');
            assignedToInput.value = staffId;

            // Update the displayed IT staff name
            assignedStaffDisplay.textContent = row.cells[1].textContent;
        });
    });

    removeAssignmentButton.addEventListener('click', () => {
    assignedToInput.value = ''; // Clear assigned IT staff ID
    assignedStaffDisplay.textContent = 'null'; // Reset displayed IT staff name

    // Reset selected row highlight
    staffRows.forEach(item => item.classList.remove('selected'));

    // You can also reset the server-side assigned staff ID in the ticket when submitting
    // Set assigned_to field to null for removal during the update
    assignedToInput.value = null; // Make sure the value is null when submitting the form
});

// Highlight the selected IT staff
document.querySelectorAll('.it-staff td').forEach(td => {
        td.addEventListener('click', function() {
            // Remove the 'selected' class from all IT staff
            document.querySelectorAll('.it-staff td').forEach(td => {
                td.classList.remove('selected');
            });

            // Add the 'selected' class to the clicked IT staff
            this.classList.add('selected');
        });
    });

</script>

</body>
</html>
