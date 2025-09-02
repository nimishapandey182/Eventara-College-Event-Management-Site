<div class="col-md-12">
  <div class="event-container">
    <h2 class="pr-center-2"><span>Events</span> 
    
    <?php 
      if (isset($_GET['cc_name'])) {
        // if($_SESSION['club_name'] == $_GET['cc_name'] ){
          include "event-management-code.php";
        // }
      }
    ?>
    </h2>
    <?php

    // Debug: uncomment to check
    // echo 'SCRIPT_NAME: ' . $_SERVER['SCRIPT_NAME'] . '<br>';
    // echo 'SERVER_NAME: ' . $_SERVER['SERVER_NAME'] . '<br>';

    // Use basename to get the script file name only
    $scriptName = basename($_SERVER['SCRIPT_NAME']);
    $serverName = $_SERVER['SERVER_NAME'];

    // Sanitize cc_name input if set
    $cc_name_safe = isset($_GET['cc_name']) ? mysqli_real_escape_string($conn, $_GET['cc_name']) : '';

    if ($serverName == 'localhost') {
      if ($scriptName == 'club-view.php') {
        $event_view = "SELECT * FROM `event-list` WHERE organizer = '$cc_name_safe' AND end_date > CURRENT_DATE()";
      } else if ($scriptName == 'event-list.php') {
        $event_view = "SELECT * FROM `event-list` WHERE end_date > CURRENT_DATE()";
      }
    } else {
      if ($scriptName == 'club-view.php') {
        $event_view = "SELECT * FROM `event-list` WHERE organizer = '$cc_name_safe' AND end_date > CURRENT_DATE()";
      } else if ($scriptName == 'event-list.php') {
        $event_view = "SELECT * FROM `event-list` WHERE end_date > CURRENT_DATE()";
      }
    }

    // Check if event_view is set before querying
    if (!isset($event_view) || empty($event_view)) {
      echo "<p>Error: Unable to determine event query.</p>";
    } else {
      $event_output = mysqli_query($conn, $event_view);

      if ($event_output && mysqli_num_rows($event_output) > 0) {
        while ($data = mysqli_fetch_assoc($event_output)) {
    ?>
          <div class="event-box">
            <div class="row">
              <div class="col-md-4">
                <div class="event-img">
                  <img
                    src="upload-image/<?php echo htmlspecialchars($data['event_image']); ?>"
                    alt="event image"
                  />
                </div>
              </div>
              <div class="col-md-8">
                <div class="event-meta">
                  <span><i class="fas fa-calendar-alt"></i> <?php echo htmlspecialchars($data['date']); ?></span>
                  <span><i class="fas fa-users"></i> <?php echo htmlspecialchars($data['organizer']); ?></span>
                </div>
                <div class="event-description mt-1">
                  <span><strong>Name:</strong> <?php echo htmlspecialchars($data['event_name']); ?></span>
                  <span><strong>Venue:</strong> <?php echo htmlspecialchars($data['event_venue']); ?></span>
                  <span><strong>Start Date:</strong> <?php echo htmlspecialchars($data['start_date']); ?> | <strong>End Date:</strong> <?php echo htmlspecialchars($data['end_date']); ?></span>
                </div>
                <div class="event-join mt-1">
                  <a href="event_view?event_id=<?php echo urlencode($data['id']); ?>&cc_name=<?php echo urlencode($data['organizer']); ?>" class="badge p-2 px-4 bg-primary">View</a>
                </div>
              </div>
            </div>
          </div>
    <?php
        }
      } else {
        echo "<h3 class='border p-4 border-dotted'>No Events added</h3>";
      }
    }
    ?>
  </div>
</div>
