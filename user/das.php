<?php
require_once '../config/config.php';
require_once '../config/db_connect.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: " . BASE_URL . "/login.php");
    exit();
}

$db = new Database();
$conn = $db->connect();

// Fetch notifications for the user
$notification_sql = "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 5";
$notification_stmt = $conn->prepare($notification_sql);
$notification_stmt->bind_param("i", $_SESSION['user_id']);
$notification_stmt->execute();
$notifications = $notification_stmt->get_result();

// Fetch user's complaints with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

$user_id = $_SESSION['user_id'];
$sql = "
    SELECT 
        c.*, 
        a.collector_id, 
        u.full_name AS collector_name, 
        a.status AS assignment_status
    FROM complaints c
    LEFT JOIN assignments a ON c.id = a.complaint_id
    LEFT JOIN users u ON a.collector_id = u.id
    WHERE c.user_id = ?
    ORDER BY c.created_at DESC
    LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $user_id, $per_page, $offset);
$stmt->execute();
$complaints = $stmt->get_result();

// Get total complaints for pagination
$total_sql = "SELECT COUNT(*) as count FROM complaints WHERE user_id = ?";
$total_stmt = $conn->prepare($total_sql);
$total_stmt->bind_param("i", $user_id);
$total_stmt->execute();
$total_result = $total_stmt->get_result();
$total_complaints = $total_result->fetch_assoc()['count'];
$total_pages = ceil($total_complaints / $per_page);

$page_title = "User Dashboard";
include '../includes/header.php';
?>

<style>
    .notification {
        background-color: #f8f9fa;
        border-left: 4px solid #007bff;
        padding: 10px;
        margin-bottom: 10px;
        cursor: pointer;
    }

    .notification.read {
        border-left-color: #28a745;
        background-color: #e2f7e1;
    }

    .badge {
        padding: 0.5em 1em;
        font-size: 0.875rem;
    }
</style>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="#">
            <div class="eco-logo">
                <i class="fas fa-leaf"></i>
                <span>EcoManage</span>
            </div>
        </a>
        <div class="navbar-nav ml-auto">
            <span class="navbar-text text-white mr-3">
                <i class="fas fa-user"></i> Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>
            </span>
            <a class="nav-link text-white" href="../logout.php">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <!-- Display Notifications -->
    <div class="row">
        <div class="col-md-12 mb-4">
            <h5>Notifications</h5>
            <?php if ($notifications->num_rows > 0): ?>
                <?php while ($notification = $notifications->fetch_assoc()): ?>
                    <div class="notification <?php echo $notification['status']; ?>" data-id="<?php echo $notification['id']; ?>">
                        <strong><?php echo htmlspecialchars($notification['message']); ?></strong>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="notification">
                    <strong>No new notifications</strong>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Rest of the Dashboard -->
    <div class="row">
        <div class="col-md-12 mb-4">
            <button class="btn btn-success" data-toggle="modal" data-target="#newComplaintModal">
                <i class="fas fa-plus"></i> New Complaint
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h3 class="mb-0">My Complaints</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Title</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($complaints->num_rows > 0): ?>
                                    <?php while ($row = $complaints->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                                            <td><?php echo htmlspecialchars($row['location']); ?></td>
                                            <td>
                                                <span class="badge badge-<?php
                                                                            echo match ($row['status']) {
                                                                                'pending' => 'warning',
                                                                                'assigned' => 'info',
                                                                                'in_progress' => 'primary',
                                                                                'completed' => 'success',
                                                                                'cancelled' => 'danger',
                                                                                default => 'secondary'
                                                                            };
                                                                            ?> status-badge">
                                                    <?php echo ucfirst($row['status']); ?>
                                                </span>
                                            </td>

                                            <td><?php echo date('Y-m-d H:i', strtotime($row['created_at'])); ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-info view-complaint"
                                                    data-title="<?php echo htmlspecialchars($row['title']); ?>"
                                                    data-description="<?php echo htmlspecialchars($row['description']); ?>"
                                                    data-location="<?php echo htmlspecialchars($row['location']); ?>"
                                                    data-status="<?php echo htmlspecialchars($row['status']); ?>"
                                                    data-created-at="<?php echo date('Y-m-d H:i', strtotime($row['created_at'])); ?>"
                                                    data-toggle="modal"
                                                    data-target="#viewComplaintModal">
                                                    <i class="fas fa-eye"></i> View Details
                                                </button>
                                            </td>

                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center">No complaints found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if ($total_pages > 1): ?>
                        <nav aria-label="Page navigation" class="mt-4">
                            <ul class="pagination justify-content-center">
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                    <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- New Complaint Modal -->
<div class="modal fade" id="newComplaintModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">New Complaint</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="submit_complaint.php" method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    <div class="form-group">
                        <label>Location</label>
                        <input type="text" class="form-control" name="location" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" name="description" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit Complaint</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Complaint Modal -->
<div class="modal fade" id="viewComplaintModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-info-circle"></i> Complaint Details
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="complaint-details">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="complaint-title text-primary mb-3"></h5>
                            <div class="row">
                                <div class="col-12">
                                    <strong>Location: </strong><span class="complaint-location"></span>
                                </div>
                                <div class="col-12">
                                    <strong>Status: </strong><span class="complaint-status"></span>
                                </div>
                                <div class="col-12">
                                    <strong>Created At: </strong><span class="complaint-created-at"></span>
                                </div>
                                <div class="col-12 mt-3">
                                    <strong>Description: </strong><p class="complaint-description"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Handle view complaint modal
        $('.view-complaint').on('click', function() {
            const title = $(this).data('title');
            const description = $(this).data('description');
            const location = $(this).data('location');
            const status = $(this).data('status');
            const createdAt = $(this).data('created-at');

            $('.complaint-title').text(title);
            $('.complaint-description').text(description);
            $('.complaint-location').text(location);
            $('.complaint-status').text(status);
            $('.complaint-created-at').text(createdAt);
        });

        // Handle notification click (optional)
        $('.notification').on('click', function() {
            const notificationId = $(this).data('id');
            // Optionally, mark the notification as read
            $(this).addClass('read');
        });
    });
</script>

<?php include '../includes/footer.php'; ?>
