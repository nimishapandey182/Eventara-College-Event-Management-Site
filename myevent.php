<?php
include "master.php";
include "header.php";

// Ensure user is logged in
if (!isset($_SESSION['register_id'])) {
    echo "<script>alert('Please login to view your events.'); window.location.href='login';</script>";
    exit();
}

$register_id = $_SESSION['register_id'];

// Fetch all joined events by the user
$event_query = "SELECT el.* 
                FROM event_registrations er 
                JOIN `event-list` el ON er.event_id = el.id 
                WHERE er.register_id = '$register_id'
                ORDER BY el.start_date DESC";
$event_result = mysqli_query($conn, $event_query);
?>

<section class="container mt-5">
    <h2 class="mb-4">My Joined Events</h2>

    <?php if (mysqli_num_rows($event_result) > 0): ?>
        <div class="row">
            <?php while ($event = mysqli_fetch_assoc($event_result)): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="upload-image/<?php echo $event['event_image']; ?>" class="card-img-top" alt="Event Poster">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $event['event_name']; ?></h5>
                            <p class="card-text">
                                <strong>Venue:</strong> <?php echo $event['event_venue']; ?><br>
                                <strong>Start:</strong> <?php echo $event['start_date']; ?><br>
                                <strong>End:</strong> <?php echo $event['end_date']; ?><br>
                                <strong>Type:</strong> <?php echo ucfirst($event['event_type']); ?><br>
                                <?php if ($event['event_type'] === 'paid'): ?>
                                    <strong>Fee:</strong> ₹<?php echo $event['ev_fee']; ?><br>
                                <?php endif; ?>
                            </p>
                            <a href="event_view?event_id=<?php echo $event['id']; ?>&cc_name=<?php echo $event['organizer']; ?>" class="btn btn-primary">View Event</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">You have not joined any events yet.</div>
    <?php endif; ?>
</section>

<?php include "footer.php"; ?>
