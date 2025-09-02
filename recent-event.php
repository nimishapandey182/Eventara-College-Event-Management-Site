<?php
// Sanitize $club_name if coming from user input or external source
$club_name_safe = isset($club_name) ? mysqli_real_escape_string($conn, $club_name) : '';

// Determine SQL based on current script
if ($_SERVER['SCRIPT_NAME'] == "/test/college-clubs-events-management/event-list.php") {
    $recent_event_sql = "SELECT * FROM `event-list` WHERE end_date < CURRENT_DATE()";
} else {
    $recent_event_sql = "SELECT * FROM `event-list` WHERE end_date < CURRENT_DATE() AND organizer = '{$club_name_safe}'";
}

$recent_event_sql_query = mysqli_query($conn, $recent_event_sql);

if ($recent_event_sql_query && mysqli_num_rows($recent_event_sql_query) > 0) {
    while ($data = mysqli_fetch_assoc($recent_event_sql_query)) {
        ?>
        <div class="recent-event-box border">
            <div class="row">
                <div class="col-md-12">
                    <div class="event-img">
                        <img src="upload-image/<?php echo htmlspecialchars($data["event_image"]); ?>" alt="event images" />
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="event-meta">
                        <span><i class="fas fa-calendar-alt"></i> <?php echo htmlspecialchars($data["date"]); ?></span>
                        <span><i class="fas fa-users"></i> <?php echo htmlspecialchars($data["organizer"]); ?></span>
                    </div>
                    <div class="event-description">
                        <span><strong>Name:</strong> <?php echo htmlspecialchars($data["event_name"]); ?></span>
                        <span><strong>Venue:</strong> <?php echo htmlspecialchars($data["event_venue"]); ?></span>
                        <span><strong>Start Date:</strong> <?php echo htmlspecialchars($data["start_date"]); ?> | <strong>End Date:</strong> <?php echo htmlspecialchars($data["end_date"]); ?></span>
                    </div>
                    <div class="event-join">
                        <a href="event_view?event_id=<?php echo urlencode($data['id']); ?>&cc_name=<?php echo urlencode($data['organizer']); ?>" class="badge p-2 px-3 mb-2">View</a>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
} else {
    echo '<div class="recent-event-box p-2">No Recent Events</div>';
}
?>
