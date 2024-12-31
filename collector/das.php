<?php
session_start();
if (!isset($_SESSION['collector_id'])) {
    header("Location: login.php");
    exit;
}

include 'includes/config.php';

// Fetch assignments for the collector
$collector_id = $_SESSION['collector_id'];
$query = $conn->prepare("SELECT * FROM assignments WHERE collector_id = ?");
$query->bind_param("i", $collector_id);
$query->execute();
$result = $query->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Collector Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h2>Welcome to the Collector Dashboard</h2>
        <table class="table table-striped mt-4">
            <thead>
                <tr>
                    <th>Assignment ID</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Notes</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['assignment_id']) ?></td>
                        <td><?= htmlspecialchars($row['location']) ?></td>
                        <td>
                            <span id="status-<?= $row['assignment_id'] ?>"><?= htmlspecialchars($row['status']) ?></span>
                        </td>
                        <td>
                            <input type="text" id="notes-<?= $row['assignment_id'] ?>" class="form-control" value="<?= htmlspecialchars($row['notes']) ?>">
                        </td>
                        <td>
                            <button class="btn btn-primary update-status-btn" data-assignment-id="<?= $row['assignment_id'] ?>">Update</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function () {
            $('.update-status-btn').click(function () {
                const assignmentId = $(this).data('assignment-id');
                const status = prompt('Enter new status (Pending, In Progress, Completed):');
                const notes = $(`#notes-${assignmentId}`).val();

                if (status) {
                    $.ajax({
                        url: '/update.php',
                        type: 'POST',
                        data: { assignment_id: assignmentId, status: status, notes: notes },
                        success: function (response) {
                            const res = JSON.parse(response);
                            if (res.success) {
                                $(`#status-${assignmentId}`).text(status);
                                alert('Status updated successfully!');
                            } else {
                                alert('Error updating status: ' + res.message);
                            }
                        },
                        error: function () {
                            alert('An error occurred while updating the status.');
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
