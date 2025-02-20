<?php
require_once '../config/config.php';
require_once '../config/db_connect.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'collector') {
    header("Location: " . BASE_URL . "/login.php");
    exit();
}

$db = new Database();
$conn = $db->connect();

// Debug: Log collector ID
$collector_id = $_SESSION['user_id'];
error_log("Collector ID: " . $collector_id);

// Add this right after the database connection
// Debug information
echo "<!-- Debug Info:
Collector ID: {$_SESSION['user_id']}
Role: {$_SESSION['role']}
-->";

// Log session data to browser console
echo "<script>
console.log('Session Data:', " . json_encode([
    'user_id' => $_SESSION['user_id'],
    'role' => $_SESSION['role'],
    'full_name' => $_SESSION['full_name']
]) . ");
</script>";

// Add this near the top after session check
error_log("Collector Dashboard - User ID: " . $_SESSION['user_id']);

// Add this before the main query
error_log("Fetching assignments for collector ID: " . $collector_id);

try {
    // First verify if the collector exists and has any assignments
    $check_sql = "SELECT COUNT(*) as count FROM assignments WHERE collector_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    if ($check_stmt === false) {
        throw new Exception("Error preparing check query: " . $conn->error);
    }
    
    $check_stmt->bind_param("i", $collector_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    $assignment_count = $check_result->fetch_assoc()['count'];

    // Debug: Log assignment count
    error_log("Total assignments found: " . $assignment_count);

    // Main query to fetch assignments
    $sql = "SELECT 
    a.id as assignment_id,
    a.status as assignment_status,
    a.notes,
    a.completed_at,
    c.id as complaint_id,
    c.title,
    c.description,
    c.location,
    c.status as complaint_status,
    c.photo,  -- Added this line to include the photo
    u.full_name as user_name,
    u.phone as user_phone
FROM assignments a 
LEFT JOIN complaints c ON a.complaint_id = c.id 
LEFT JOIN users u ON c.user_id = u.id 
WHERE a.collector_id = ?";


    // Debug: Log the SQL query
    error_log("SQL Query: " . $sql);

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        throw new Exception("Error preparing main query: " . $conn->error);
    }

    $stmt->bind_param("i", $collector_id);
    $stmt->execute();
    $assignments = $stmt->get_result();

    // Debug: Log number of results
    error_log("Number of assignments fetched: " . $assignments->num_rows);

    // Store assignments in array for JavaScript
    $assignments_data = [];
    while ($row = $assignments->fetch_assoc()) {
        $assignments_data[] = $row;
    }

    // Add this after executing the main query
    error_log("Found " . $stmt->num_rows . " assignments");

} catch (Exception $e) {
    error_log("Error in collector dashboard: " . $e->getMessage());
    die("Database error: " . $e->getMessage());
}

$page_title = "Collector Dashboard";
include '../includes/header.php';
?>

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
                <i class="fas fa-truck"></i> Collector Dashboard
            </span>
            <span class="navbar-text text-white mr-3">
                <i class="fas fa-user"></i> Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>
            </span>
            <a class="nav-link text-white" href="transactions.php">
    <i class="fas fa-money-check-alt"></i> Transactions
</a>
            <a class="nav-link text-white" href="../logout.php">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <!--remains  -->
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            Status updated successfully!
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?php echo htmlspecialchars($_GET['message'] ?? 'An error occurred'); ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>

    <div class="row text-center mb-4">
            <div class="col-md-3">
                <div class="card p-3">
                    <h5>Total Assignments</h5>
                    <h2><?php echo $assignment_count; ?></h2>
                </div>
            </div>
            <?php
        // Get status counts
        $status_sql = "SELECT status, COUNT(*) as count 
                       FROM assignments 
                       WHERE collector_id = ? 
                       GROUP BY status";
        $status_stmt = $conn->prepare($status_sql);
        $status_stmt->bind_param("i", $collector_id);
        $status_stmt->execute();
        $status_result = $status_stmt->get_result();
        $status_counts = [];
        while ($row = $status_result->fetch_assoc()) {
            $status_counts[$row['status']] = $row['count'];
        }
        ?>
            <div class="col-md-3">
                <div class="card p-3">
                    <h5>Pending</h5>
                    <h2 class="text-warning"><?php echo $status_counts['pending'] ?? 0; ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3">
                    <h5>In Progress</h5>
                    <h2 class="text-primary"><?php echo $status_counts['in_progress'] ?? 0; ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3">
                    <h5>Completed</h5>
                    <h2 class="text-success"><?php echo $status_counts['completed'] ?? 0; ?></h2>
                </div>
            </div>
        </div>

    <div class="card">
        <div class="card-header">
            <h3 class="mb-0">My Assignments</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Complaint ID</th>
                            <th>User Details</th>
                            <th>Title</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Actions</th>
                            <th>Photo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($assignments->num_rows > 0): ?>
                            <?php foreach ($assignments_data as $row): ?>
                            <tr>
                                <td><?php echo $row['complaint_id']; ?></td>
                                <td>
                                    <?php echo htmlspecialchars($row['user_name']); ?><br>
                                    <small class="text-muted"><?php echo $row['user_phone']; ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($row['title']); ?></td>
                                <td><?php echo htmlspecialchars($row['location']); ?></td>
                                <td>
                                    <span class="badge badge-<?php 
                                        echo match($row['assignment_status']) {
                                            'pending' => 'warning',
                                            'in_progress' => 'primary',
                                            'completed' => 'success',
                                            'rejected' => 'danger',
                                            default => 'secondary'
                                        };
                                    ?>">
                                        <?php echo ucfirst($row['assignment_status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($row['assignment_status'] !== 'completed'): ?>
                                    <button class="btn btn-sm btn-primary update-status" 
                                            data-id="<?php echo $row['assignment_id']; ?>"
                                            data-toggle="modal" 
                                            data-target="#updateStatusModal"
                                            data-status="<?php echo $row['assignment_status']; ?>">
                                        <i class="fas fa-edit"></i> Update Status
                                    </button>
                                    <?php endif; ?>
                                    <button class="btn btn-sm btn-info view-details" 
                                            data-id="<?php echo $row['complaint_id']; ?>"
                                            data-title="<?php echo htmlspecialchars($row['title']); ?>"
                                            data-description="<?php echo htmlspecialchars($row['description']); ?>"
                                            data-location="<?php echo htmlspecialchars($row['location']); ?>"
                                            data-user="<?php echo htmlspecialchars($row['user_name']); ?>"
                                            data-phone="<?php echo htmlspecialchars($row['user_phone']); ?>"
                                            data-status="<?php echo htmlspecialchars($row['assignment_status']); ?>"
                                            data-notes="<?php echo htmlspecialchars($row['notes']); ?>"
                                            data-toggle="modal" 
                                            data-target="#viewDetailsModal">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                </td>
                                <td>
                                <?php if (!empty($row['photo'])): ?>
                                    <a href="<?php echo htmlspecialchars($row['photo']); ?>" target="_blank">
                                    <img src="<?php echo htmlspecialchars($row['photo']); ?>" alt="Complaint Photo" width="50" height="50" />
                                </a>
                            <?php else: ?>
                                No photo uploaded
                            <?php endif; ?>

                            </td>
                                
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">No assignments found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Status</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="update_status.php" method="POST" id="updateStatusForm">
                <div class="modal-body">
                    <input type="hidden" name="assignment_id" id="updateAssignmentId">
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" name="status" required>
                            <option value="pending">Pending</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Notes</label>
                        <textarea class="form-control" name="notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Details Modal -->
<div class="modal fade" id="viewDetailsModal">
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
                                <div class="col-12 mb-3">
                                    <label class="text-muted mb-1">Description</label>
                                    <p class="complaint-description mb-0"></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted mb-1">Location</label>
                                    <p class="complaint-location mb-0"></p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted mb-1">Status</label>
                                    <p class="complaint-status mb-0"></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted mb-1">Reported By</label>
                                    <p class="complaint-user mb-0"></p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted mb-1">Contact</label>
                                    <p class="complaint-phone mb-0"></p>
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

<!-- First load jQuery and other dependencies -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js"></script>

<!-- Then add our custom scripts -->
<script>
// Browser console logging
console.log('Page loaded');

// Debug: Log all assignments data to console
const assignmentsData = <?php echo json_encode($assignments_data); ?>;
console.log('Assignments Data:', assignmentsData);

// Wait for jQuery to load
$(document).ready(function() {
    console.log('jQuery loaded and document ready');

    // Update status modal
    $('.update-status').click(function() {
        const assignmentId = $(this).data('id');
        const currentStatus = $(this).data('status');
        console.log('Opening modal for assignment:', assignmentId, 'Current status:', currentStatus);
        $('#updateAssignmentId').val(assignmentId);
        $('#updateStatusModal select[name="status"]').val(currentStatus);
    });

    // Form submission
    $('#updateStatusForm').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        const formData = form.serialize();
        
        console.log('Submitting form data:', formData);
        
        // Disable submit button to prevent double submission
        submitBtn.prop('disabled', true);
        
        $.ajax({
        url: 'update_status.php',  // Assuming both are in the same directory
        method: 'POST',
        data: formData,
        success: function(response) {
        console.log('Update successful:', response);
        $('#updateStatusModal').modal('hide');
        location.reload();  // Reload the page to reflect changes
    },
    error: function(xhr, status, error) {
        console.error('Update failed:', error);
        console.log('Response:', xhr.responseText);  // Log the full error response from PHP
        alert('Error updating status. Please try again.');
    },
    complete: function() {
        submitBtn.prop('disabled', false);
    }
});


    });

    // View details modal handler
    $('.view-details').click(function() {
        const button = $(this);
        
        // Get data from data attributes
        const data = {
            title: button.data('title'),
            description: button.data('description'),
            location: button.data('location'),
            user: button.data('user'),
            phone: button.data('phone'),
            status: button.data('status')
        };

        // Update modal content with better formatting
        $('#viewDetailsModal .complaint-title').text(data.title);
        $('#viewDetailsModal .complaint-description').text(data.description || 'No description provided');
        $('#viewDetailsModal .complaint-location').text(data.location);
        $('#viewDetailsModal .complaint-user').text(data.user);
        $('#viewDetailsModal .complaint-phone').text(data.phone);
        
        // Add status with styled badge
        const statusClass = 
            data.status === 'completed' ? 'success' :
            data.status === 'in_progress' ? 'primary' :
            data.status === 'pending' ? 'warning' : 'secondary';
        
        $('#viewDetailsModal .complaint-status').html(
            `<span class="badge badge-${statusClass}">${data.status.toUpperCase()}</span>`
        );
    });

    // Debug: Log any errors from PHP
    <?php if (isset($_GET['error'])): ?>
    console.error('PHP Error:', <?php echo json_encode($_GET['message']); ?>);
    <?php endif; ?>

    // Auto-refresh after successful update
    <?php if (isset($_GET['success'])): ?>
    console.log('Success:', <?php echo json_encode($_GET['message']); ?>);
    setTimeout(function() {
        console.log('Reloading page...');
        window.location.reload();
    }, 2000);
    <?php endif; ?>

    // Log when modals are opened
    $('#updateStatusModal').on('show.bs.modal', function () {
        console.log('Update Status Modal Opening');
    });

    $('#viewDetailsModal').on('show.bs.modal', function () {
        console.log('View Details Modal Opening');
    });
});
</script>

<!-- Add this CSS to your header or in a style tag -->
<style>
.modal-header {
    border-radius: 0.25rem 0.25rem 0 0;
}

.complaint-details label {
    font-weight: 600;
    font-size: 0.875rem;
}

.complaint-details p {
    font-size: 1rem;
}

.complaint-title {
    font-size: 1.25rem;
    font-weight: bold;
}

.badge {
    padding: 0.5em 1em;
    font-size: 0.875rem;
}

.table td {
    vertical-align: middle;
}

.btn-sm {
    padding: 0.375rem 0.75rem;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: none;
}

.modal-content {
    border: none;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.alert {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

/* Status badge colors */
.badge-pending { background-color: #ffc107; color: #000; }
.badge-in-progress { background-color: #007bff; }
.badge-completed { background-color: #28a745; }
.badge-rejected { background-color: #dc3545; }

/* Card hover effect */
.card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    transition: box-shadow 0.3s ease-in-out;
}

/* Button hover effects */
.btn-primary:hover {
    transform: translateY(-1px);
    transition: transform 0.2s ease-in-out;
}

/* Modal animation */
.modal.fade .modal-dialog {
    transition: transform 0.3s ease-out;
}

.modal.show .modal-dialog {
    transform: none;
}
</style>

<?php 
$conn->close();
include '../includes/footer.php'; 
?>
</body>
</html>