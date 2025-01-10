<?php
// Start session and connect to the database
session_start();

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
        SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) AS in_progress_count,
        SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) AS resolved_count,
        SUM(CASE WHEN status = 'escalated' THEN 1 ELSE 0 END) AS escalated_count
    FROM tickets
    WHERE user_id = :user_id
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
    <title>Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Basic dashboard styling */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
        }

        .dashboard {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            margin-top: 20px;
            padding: 20px;
        }

        .dashboard .card {
            width: 250px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            background: linear-gradient(135deg, #6e7bff, #4e5cba);
            text-align: center;
            color: #fff;
            padding: 30px;
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
        }

        .dashboard .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        .dashboard .card h3 {
            margin-bottom: 10px;
            font-size: 18px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .dashboard .card p {
            font-size: 28px;
            font-weight: bold;
            margin: 0;
        }

        .dashboard .card .icon {
            font-size: 40px;
            margin-bottom: 15px;
        }

        canvas {
            max-width: 100%;
            max-height: 400px;
            margin-top: 30px;
        }
    </style>
</head>
<body>

    <h1 style="text-align: center; margin-top: 40px;">Dashboard</h1>

    <div class="dashboard">
        <!-- Card to show total tickets count -->
        <div class="card" style="background: linear-gradient(135deg, #4e5cba, #6e7bff);">
            <div class="icon">🎫</div>
            <h3>Total Tickets</h3>
            <p><?php echo $allTickets; ?></p>
        </div>

        <!-- Card to show pending tickets count -->
        <div class="card" style="background: linear-gradient(135deg, #ff7c7c, #ff3d3d);">
            <div class="icon">⏳</div>
            <h3>Pending Tickets</h3>
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
            <h3>Escalated Tickets</h3>
            <p><?php echo $escalatedTickets; ?></p>
        </div>
    </div>

    <!-- Pie Chart for ticket status distribution -->
    <h3 style="text-align: center;">Ticket Status Distribution</h3>
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

