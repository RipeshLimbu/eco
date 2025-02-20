<?php
require_once '../config/config.php';
require_once '../config/db_connect.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: " . BASE_URL . "/login.php");
    exit();
}

$db = new Database();
$conn = $db->connect();

// Get statistics
$stats = [
    'total_complaints' => $conn->query("SELECT COUNT(*) FROM complaints")->fetch_row()[0],
    'pending' => $conn->query("SELECT COUNT(*) FROM complaints WHERE status = 'pending'")->fetch_row()[0],
    'in_progress' => $conn->query("SELECT COUNT(*) FROM complaints WHERE status = 'in_progress'")->fetch_row()[0],
    'completed' => $conn->query("SELECT COUNT(*) FROM complaints WHERE status = 'completed'")->fetch_row()[0],
    'total_collectors' => $conn->query("SELECT COUNT(*) FROM users WHERE role = 'collector'")->fetch_row()[0],
    'total_users' => $conn->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetch_row()[0]
];

// Get monthly complaints count
$monthly_sql = "SELECT 
                    DATE_FORMAT(created_at, '%Y-%m') as month,
                    COUNT(*) as count,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed
                FROM complaints 
                GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                ORDER BY month DESC
                LIMIT 6";
$monthly_stats = $conn->query($monthly_sql);

// Get collector performance
$collector_sql = "SELECT 
                    u.full_name,
                    COUNT(a.id) as total_assignments,
                    SUM(CASE WHEN a.status = 'completed' THEN 1 ELSE 0 END) as completed_assignments,
                    AVG(CASE WHEN a.status = 'completed' 
                        THEN TIMESTAMPDIFF(HOUR, a.assigned_at, a.completed_at) 
                        ELSE NULL END) as avg_completion_time
                FROM users u
                LEFT JOIN assignments a ON u.id = a.collector_id
                WHERE u.role = 'collector'
                GROUP BY u.id
                ORDER BY completed_assignments DESC";
$collector_stats = $conn->query($collector_sql);

$page_title = "System Reports";
include '../includes/header.php';
?>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">
            <div class="eco-logo">
                <i class="fas fa-leaf"></i>
                <span>EcoManage</span>
            </div>
        </a>
        <div class="navbar-nav ml-auto">
        <a class="nav-link text-white" href="dashboard.php">
                <i class="fas fa-home"></i> Home
            </a>
            <a class="nav-link text-white" href="../logout.php">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <div class="row align-items-center mb-4">
        <div class="col">
            <h2 class="mb-0">
                <i class="fas fa-chart-line text-primary"></i> System Reports
            </h2>
        </div>
        <div class="col text-right">
            <div class="btn-group">
                <button class="btn btn-outline-primary active" data-period="monthly">Monthly</button>
                <button class="btn btn-outline-primary" data-period="weekly">Weekly</button>
                <button class="btn btn-outline-primary" data-period="daily">Daily</button>
            </div>
        </div>
    </div>

    <!-- Overall Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 p-2">
            <div class="card stats-card bg-primary text-white">
                <div class="card-body text-center">
                    <i class="fas fa-clipboard-list fa-2x mb-2"></i>
                    <h5>Total Complaints</h5>
                    <h2><?php echo $stats['total_complaints']; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 p-2">
            <div class="card stats-card bg-warning text-white">
                <div class="card-body text-center">
                    <i class="fas fa-clock fa-2x mb-2"></i>
                    <h5>Pending</h5>
                    <h2><?php echo $stats['pending']; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 p-2">
            <div class="card stats-card bg-info text-white">
                <div class="card-body text-center">
                    <i class="fas fa-spinner fa-2x mb-2"></i>
                    <h5>In Progress</h5>
                    <h2><?php echo $stats['in_progress']; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 p-2">
            <div class="card stats-card bg-success text-white">
                <div class="card-body text-center">
                    <i class="fas fa-check-circle fa-2x mb-2"></i>
                    <h5>Completed</h5>
                    <h2><?php echo $stats['completed']; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 p-2">
            <div class="card stats-card bg-secondary text-white">
                <div class="card-body text-center">
                    <i class="fas fa-truck fa-2x mb-2"></i>
                    <h5>Collectors</h5>
                    <h2><?php echo $stats['total_collectors']; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 p-2">
            <div class="card stats-card bg-dark text-white">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-2x mb-2"></i>
                    <h5>Users</h5>
                    <h2><?php echo $stats['total_users']; ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Statistics and Collector Performance -->
    <div class="row">
        <!-- Monthly Statistics -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="fas fa-calendar-alt text-primary"></i> Monthly Statistics</h4>
                        <button class="btn btn-sm btn-outline-primary" onclick="printReport('monthly')">
                            <i class="fas fa-print"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Month</th>
                                    <th>Total</th>
                                    <th>Completed</th>
                                    <th>Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $monthly_stats->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo date('F Y', strtotime($row['month'] . '-01')); ?></td>
                                    <td><?php echo $row['count']; ?></td>
                                    <td><?php echo $row['completed']; ?></td>
                                    <td>
                                        <?php 
                                        $rate = ($row['count'] > 0) ? ($row['completed'] / $row['count'] * 100) : 0;
                                        $color = $rate >= 70 ? 'success' : ($rate >= 40 ? 'warning' : 'danger');
                                        ?>
                                        <span class="badge badge-<?php echo $color; ?>">
                                            <?php echo round($rate, 1); ?>%
                                        </span>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Collector Performance -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="fas fa-trophy text-primary"></i> Collector Performance</h4>
                        <button class="btn btn-sm btn-outline-primary" onclick="printReport('collectors')">
                            <i class="fas fa-print"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Collector</th>
                                    <th>Assignments</th>
                                    <th>Completed</th>
                                    <th>Rate</th>
                                    <th>Avg Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $collector_stats->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                                    <td><?php echo $row['total_assignments']; ?></td>
                                    <td><?php echo $row['completed_assignments']; ?></td>
                                    <td>
                                        <?php 
                                        $rate = ($row['total_assignments'] > 0) ? 
                                               ($row['completed_assignments'] / $row['total_assignments'] * 100) : 0;
                                        $color = $rate >= 70 ? 'success' : ($rate >= 40 ? 'warning' : 'danger');
                                        ?>
                                        <span class="badge badge-<?php echo $color; ?>">
                                            <?php echo round($rate, 1); ?>%
                                        </span>
                                    </td>
                                    <td>
                                        <?php 
                                        $avg_time = $row['avg_completion_time'];
                                        if ($avg_time !== null) {
                                            echo round($avg_time, 1) . ' hrs';
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Period selector
    $('.btn-group .btn').click(function() {
        $('.btn-group .btn').removeClass('active');
        $(this).addClass('active');
        // Add your period filtering logic here
    });

    // Hover effect for stats cards
    $('.stats-card').hover(
        function() { $(this).addClass('shadow-lg').css('cursor', 'pointer'); },
        function() { $(this).removeClass('shadow-lg'); }
    );
});

function printReport(type) {
    window.print();
}
</script>

<?php 
$conn->close();
include '../includes/footer.php'; 
?> 