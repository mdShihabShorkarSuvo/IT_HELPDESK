<?php
// Start session and connect to the database

// Assuming user is logged in, and user_id is available
$user_id = $_SESSION['user_id']; // Get the user_id from session

// Database connection (replace with your actual database connection)
$pdo = new PDO('mysql:host=localhost;dbname=smart_it_helpdesk', 'root', ''); // Replace with actual DB details
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Query to get the count of tickets for all statuses, including 'Escalated'
$query = "
    SELECT
        COUNT(*) AS total_count,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending_count,
        SUM(CASE WHEN status = 'in progress' THEN 1 ELSE 0 END) AS in_progress_count,
        SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) AS resolved_count,
        SUM(CASE WHEN status = 'escalated' THEN 1 ELSE 0 END) AS escalated_count
    FROM tickets
";

$stmt = $pdo->prepare($query);
//$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
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
    <title>Admin Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>

<h1 style="color: black; font-size: 36px;">Admin Dashboard</h1>


    <div class="dashboard">
        <!-- Card to show total tickets count -->
        <div class="card total">
            <div class="icon">🎫</div>
            <h2>Total Tickets</h2>
            <p><?php echo $allTickets; ?></p>
        </div>

        <!-- Card to show pending tickets count -->
        <div class="card pending">
            <div class="icon">⏳</div>
            <h2>Pending Tickets</h2>
            <p><?php echo $pendingTickets; ?></p>
        </div>

        <!-- Card to show in-progress tickets count -->
        <div class="card in-progress">
            <div class="icon">⚙️</div>
            <h2>In Progress</h2>
            <p><?php echo $inProgressTickets; ?></p>
        </div>

        <!-- Card to show resolved tickets count -->
        <div class="card resolved">
            <div class="icon">✅</div>
            <h2>Resolved</h2>
            <p><?php echo $resolvedTickets; ?></p>
        </div>

        <!-- Card to show escalated tickets count -->
        <div class="card escalated">
            <div class="icon">⚠️</div>
            <h2>Escalated Tickets</h2>
            <p><?php echo $escalatedTickets; ?></p>
        </div>
    </div>

    <!-- Pie Chart for ticket status distribution -->
    <h3 style="color: black; font-size: 22px;">Ticket Status Distribution</h3>

    <div class="chart-container">
        <canvas id="statusChart"></canvas>
    </div>

    <script>
        // Data for the pie chart from PHP variables
        const pieData = {
            labels: ['Pending', 'In Progress', 'Resolved', 'Escalated'],
            datasets: [{
                data: [<?php echo $pendingTickets; ?>, <?php echo $inProgressTickets; ?>, <?php echo $resolvedTickets; ?>, <?php echo $escalatedTickets; ?>],
                backgroundColor: ['#d32f2f', '#fbc02d', '#388e3c', '#ffa000'],
                borderColor: ['#b71c1c', '#f57f17', '#2e7d32', '#ff6f00'],
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
                        labels: {
                            color: '#e0e0e0'
                        }
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
