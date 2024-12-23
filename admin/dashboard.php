<?php
require_once '../config/config.php';
require_once '../config/db_connect.php';
session_start();

// Check if the user is logged in and has admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: " . BASE_URL . "/login.php");
    exit();
}

// Debugging: You can remove this after checking the session.
// echo '<pre>';
// print_r($_SESSION);
// echo '</pre>';

$db = new Database();
$conn = $db->connect();

// Fetch all complaints with user details
$sql = "SELECT c.*, u.username, u.full_name, u.phone 
        FROM complaints c 
        JOIN users u ON c.user_id = u.id 
        ORDER BY c.created_at DESC";
$complaints = $conn->query($sql);

// Fetch all collectors
$sql = "SELECT id, username, full_name FROM users WHERE role='collector'";
$collectors = $conn->query($sql);

// Get counts for different statuses
$sql = "SELECT status, COUNT(*) as count FROM complaints GROUP BY status";
$result = $conn->query($sql);
$status_counts = [];
while ($row = $result->fetch_assoc()) {
    $status_counts[$row['status']] = $row['count'];
}

$pending_count = $status_counts['pending'] ?? 0;
$progress_count = $status_counts['in_progress'] ?? 0;
$completed_count = $status_counts['completed'] ?? 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - Waste Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <div class="eco-logo">
                    <i class="fas fa-leaf"></i>
                    <span>EcoManage</span>
                </div>
            </a>
            <div class="navbar-nav ml-auto">
                <a class="nav-link text-white" href="manage_users.php">
                    <i class="fas fa-users"></i> Manage Users
                </a>
                <a class="nav-link text-white" href="reports.php">
                    <i class="fas fa-chart-bar"></i> Reports
                </a>
                <span class="navbar-text text-white mx-3">
                    <i class="fas fa-user"></i> Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>
                </span>
                <a class="nav-link text-white" href="../logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Complaints</h5>
                        <h2><?php echo $complaints->num_rows; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h5 class="card-title">Pending</h5>
                        <h2><?php echo $pending_count; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">In Progress</h5>
                        <h2><?php echo $progress_count; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Completed</h5>
                        <h2><?php echo $completed_count; ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-12">
                <a href="manage_users.php" class="btn btn-primary mr-2">
                    <i class="fas fa-users"></i> Manage Users
                </a>
                <a href="reports.php" class="btn btn-info">
                    <i class="fas fa-chart-bar"></i> View Reports
                </a>
            </div>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                Operation completed successfully
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                Operation failed
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php endif; ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="mb-0">Complaints Management</h3>
                            <div class="form-inline">
                                <input type="text" id="searchInput" class="form-control mr-2" placeholder="Search...">
                                <select class="form-control" id="statusFilter">
                                    <option value="">All Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="assigned">Assigned</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Title</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $complaints->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['id']; ?></td>
                                    <td>
                                        <?php echo htmlspecialchars($row['full_name']); ?><br>
                                        <small class="text-muted"><?php echo $row['phone']; ?></small>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                                    <td><?php echo htmlspecialchars($row['location']); ?></td>
                                    <td>
                                        <span class="badge badge-<?php 
                                            echo match($row['status']) {
                                                'pending' => 'warning',
                                                'assigned' => 'info',
                                                'completed' => 'success',
                                                default => 'secondary'
                                            };
                                        ?>">
                                            <?php echo ucfirst($row['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('Y-m-d H:i', strtotime($row['created_at'])); ?></td>
                                    <td>
                                        <?php if ($row['status'] === 'pending'): ?>
                                        <button class="btn btn-sm btn-primary assign-collector" 
                                                data-complaint-id="<?php echo $row['id']; ?>"
                                                data-toggle="modal" 
                                                data-target="#assignModal">
                                            <i class="fas fa-user-plus"></i> Assign
                                        </button>
                                        <?php endif; ?>
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

    <!-- Assign Collector Modal -->
    <div class="modal fade" id="assignModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Assign Collector</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form action="assign_collector.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="complaint_id" id="complaintId">
                        <div class="form-group">
                            <label>Select Collector</label>
                            <select class="form-control" name="collector_id" required>
                                <?php while ($collector = $collectors->fetch_assoc()): ?>
                                <option value="<?php echo $collector['id']; ?>">
                                    <?php echo htmlspecialchars($collector['full_name']); ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Notes</label>
                            <textarea class="form-control" name="notes" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Assign</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.assign-collector').click(function() {
                var complaintId = $(this).data('complaint-id');
                $('#complaintId').val(complaintId);
            });

            // Add form validation
            $('form').submit(function(e) {
                var collectorId = $('select[name="collector_id"]').val();
                if (!collectorId) {
                    e.preventDefault();
                    alert('Please select a collector');
                }
            });

            // Search functionality
            $("#searchInput").on("keyup", function() {
                var value = $(this).val().toLowerCase();
                $("table tbody tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });

            // Status filter
            $("#statusFilter").on("change", function() {
                var value = $(this).val().toLowerCase();
                if (value === "") {
                    $("table tbody tr").show();
                } else {
                    $("table tbody tr").filter(function() {
                        $(this).toggle($(this).find("td:eq(4)").text().toLowerCase().indexOf(value) > -1)
                    });
                }
            });
        });

        // Export function
        function exportToExcel() {
            window.location.href = 'export_complaints.php';
        }
    </script>
</body>
</html> 