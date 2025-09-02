<?php  
include "master-file.php";
include "admin-header.php";
?>

<div class="container">
    <div class="row mt-4">
        <div class="col-md-12">
            <h1>All Events</h1>
            <div class="event-wrap mt-4">
                <?php
                $event_query = "SELECT * FROM `event-list` ORDER BY `start_date` DESC";
                $event_result = mysqli_query($conn, $event_query);

                if (mysqli_num_rows($event_result) > 0) {
                    while ($event = mysqli_fetch_assoc($event_result)) {
                        // Escape output
                        $event_name = htmlspecialchars($event['event_name'], ENT_QUOTES, 'UTF-8');
                        $event_venue = htmlspecialchars($event['event_venue'], ENT_QUOTES, 'UTF-8');
                        $organizer = htmlspecialchars($event['organizer'], ENT_QUOTES, 'UTF-8');
                        $start_date = htmlspecialchars($event['start_date'], ENT_QUOTES, 'UTF-8');
                        $end_date = htmlspecialchars($event['end_date'], ENT_QUOTES, 'UTF-8');
                        $event_type = htmlspecialchars($event['event_type'], ENT_QUOTES, 'UTF-8');
                        $ev_fee = htmlspecialchars($event['ev_fee'], ENT_QUOTES, 'UTF-8');
                        $event_image = $event['event_image'];

                        $event_id = $event['id'];
                ?>
                <div class="event-info-box p-3 mb-4 shadow-sm rounded d-flex align-items-center">
                    <div class="event-img me-3" style="width: 150px;">
                        <img src="../upload-image/<?php echo $event_image; ?>" alt="Event Image" style="width: 100%; height: auto; border-radius: 5px;">
                    </div>
                    <div class="event-info flex-grow-1">
                        <h3><?php echo $event_name; ?></h3>
                        <p><strong>Venue:</strong> <?php echo $event_venue; ?></p>
                        <p><strong>Organizer:</strong> <?php echo $organizer; ?></p>
                        <p><strong>Duration:</strong> <?php echo $start_date . ' to ' . $end_date; ?></p>
                        <p><strong>Type:</strong> <?php echo ucfirst($event_type); ?> <?php if($event_type == 'paid') echo "(₹$ev_fee)"; ?></p>
                        <div>
                            <a href="event-joiners.php?table=<?php echo urlencode($event_id); ?>" class="btn btn-sm btn-info me-2">View Joiners</a>
                            <a href="event-edit.php?id=<?php echo $event_id; ?>" class="btn btn-sm btn-warning me-2">Edit</a>
                            <a href="event-delete.php?id=<?php echo $event_id; ?>" onclick="return confirm('Are you sure you want to delete this event? This will also delete all joiners.');" class="btn btn-sm btn-danger">Delete</a>
                        </div>
                    </div>
                </div>
                <?php
                    }
                } else {
                    echo "<p>No events found.</p>";
                }
                ?>
            </div>
        </div>
    </div>
</div>
