<?php

$user_id = $_SESSION['user_id']; // Get the user_id from session

// Database connection (replace with your actual database connection)
$pdo = new PDO('mysql:host=localhost;dbname=smart_it_helpdesk', 'root', ''); // Replace with actual DB details
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// This SQL query counts the total tickets and their statuses , including 'Escalated'
$query = "
    SELECT
        COUNT(*) AS total_count,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending_count,
        SUM(CASE WHEN status = 'in progress' THEN 1 ELSE 0 END) AS in_progress_count,
        SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) AS resolved_count,
        SUM(CASE WHEN status = 'escalated' THEN 1 ELSE 0 END) AS escalated_count
    FROM tickets
    WHERE assigned_to = :user_id
";

$stmt = $pdo->prepare($query);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();

// Fetch the result
$ticketCounts = $stmt->fetch(PDO::FETCH_ASSOC);

$allTickets = $ticketCounts['total_count'];
$pendingTickets = $ticketCounts['pending_count'];
$inProgressTickets = $ticketCounts['in_progress_count'];
$resolvedTickets = $ticketCounts['resolved_count'];
$escalatedTickets = $ticketCounts['escalated_count']; // New 'Escalated' status
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>

    <h1 style="text-align: center; margin-top: 40px;">User Dashboard</h1>

    <div class="dashboard">
        <!-- Card to show total tickets count -->
        <div class="card" style="background: linear-gradient(135deg, #4e5cba, #6e7bff);">
            <div class="icon">🎫</div>
            <h3>Total Task</h3>
            <p><?php echo $allTickets; ?></p>
        </div>

        <!-- Card to show pending tickets count -->
        <div class="card" style="background: linear-gradient(135deg, #ff7c7c, #ff3d3d);">
            <div class="icon">⏳</div>
            <h3>Pending Task</h3>
            <p><?php echo $pendingTickets; ?></p>
        </div>

        <!-- Card to show in progress tickets count -->
        <div class="card" style="background: linear-gradient(135deg, #ffea4a, #ffb800);">
            <div class="icon">⚙️</div>
            <h3>In Progress</h3>
            <p><?php echo $inProgressTickets; ?></p>
        </div>

        <!-- Card to show resolved tickets count -->
        <div class="card" style="background: linear-gradient(135deg, #4caf50, #388e3c);">
            <div class="icon">✅</div>
            <h3>Resolved</h3>
            <p><?php echo $resolvedTickets; ?></p>
        </div>

        <!-- Card to show escalated tickets count -->
        <div class="card" style="background: linear-gradient(135deg, #ff7043, #e64a19);">
            <div class="icon">⚠️</div>
            <h3>Escalated Task</h3>
            <p><?php echo $escalatedTickets; ?></p>
        </div>
    </div>

    <!-- Pie Chart for ticket status distribution -->
    <h3 style="text-align: center;">Task Status Distribution</h3>
    <div style="width: 80%; margin: 0 auto;">
        <canvas id="statusChart"></canvas>
    </div>

    <script>
        // Data for the pie chart from PHP variables
        const pieData = {
            labels: ['Pending', 'In Progress', 'Resolved', 'Escalated'],
            datasets: [{
                data: [<?php echo $pendingTickets; ?>, <?php echo $inProgressTickets; ?>, <?php echo $resolvedTickets; ?>, <?php echo $escalatedTickets; ?>],
                backgroundColor: ['#FF0000', '#FFFF00', '#008000', '#FF8000'],
                borderColor: ['#FF0000', '#FFFF00', '#008000', '#FF8000'],
                borderWidth: 1
            }]
        };

        // Pie chart configuration
        const config = {
            type: 'pie',
            data: pieData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                let dataset = tooltipItem.dataset;
                                let value = dataset.data[tooltipItem.dataIndex];
                                return `${tooltipItem.label}: ${value} Tickets`;
                            }
                        }
                    }
                }
            }
        };

        // Render the pie chart
        const statusChart = new Chart(
            document.getElementById('statusChart'),
            config
        );
    </script>

</body>
</html>

