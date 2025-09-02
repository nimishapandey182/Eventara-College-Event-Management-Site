<?php
include "master-file.php";
include "admin-header.php";

// Ensure event_id is provided
if (!isset($_GET['table']) || empty($_GET['table'])) {
    echo "<script>alert('Event ID not specified.'); window.history.back();</script>";
    exit();
}

$event_id = intval($_GET['table']); // 'table' actually refers to event_id

// Handle delete action
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $delete_query = "DELETE FROM `event_registrations` WHERE id = $delete_id AND event_id = $event_id";
    if (mysqli_query($conn, $delete_query)) {
        echo "<script>alert('Joiner deleted successfully.'); window.location.href='event-joiners.php?table=$event_id';</script>";
        exit();
    } else {
        echo "<script>alert('Error deleting joiner: " . mysqli_error($conn) . "');</script>";
    }
}

// Fetch all joiners for this event ID
$joiners_query = "SELECT * FROM `event_registrations` WHERE event_id = $event_id ORDER BY join_date DESC";
$joiners_result = mysqli_query($conn, $joiners_query);
?>

<div class="container mt-4">
    <h1>Event Joiners</h1>
    <a href="event-list.php" class="btn btn-secondary mb-3">Back to Events</a>

    <?php if (mysqli_num_rows($joiners_result) > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Register ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Join Date</th>
                        <th>Verified</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $count = 1;
                    while ($joiner = mysqli_fetch_assoc($joiners_result)) {
                        $id = $joiner['id'];
                        $register_id = htmlspecialchars($joiner['register_id'], ENT_QUOTES, 'UTF-8');
                        $user_name = htmlspecialchars($joiner['user_name'], ENT_QUOTES, 'UTF-8');
                        $user_email = htmlspecialchars($joiner['user_email'], ENT_QUOTES, 'UTF-8');
                        $join_date = htmlspecialchars($joiner['join_date'], ENT_QUOTES, 'UTF-8');
                        $verified = $joiner['verified'] == 1 ? "Yes" : "No";
                    ?>
                    <tr>
                        <td><?php echo $count++; ?></td>
                        <td><?php echo $register_id; ?></td>
                        <td><?php echo $user_name; ?></td>
                        <td><?php echo $user_email; ?></td>
                        <td><?php echo $join_date; ?></td>
                        <td><?php echo $verified; ?></td>
                        <td>
                            <a href="event-joiners.php?table=<?php echo urlencode($event_id); ?>&delete_id=<?php echo $id; ?>" 
                               class="btn btn-danger btn-sm" 
                               onclick="return confirm('Are you sure you want to delete this joiner?');">Delete</a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>No users have joined this event yet.</p>
    <?php endif; ?>
</div>
