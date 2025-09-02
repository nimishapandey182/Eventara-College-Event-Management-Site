<?php
include "master.php";
include "header.php";

// Fetch verified events with start_date, end_date, start_time, end_time
$events = [];
$query = "SELECT id, event_name, organizer, start_date, end_date, start_time, end_time FROM `event-list` ORDER BY start_date ASC";
$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($result)) {
    $startDatetime = $row['start_date'];
    if ($row['start_time'] && $row['start_time'] != "00:00:00") {
        $startDatetime .= 'T' . $row['start_time'];
    }

    $endDatetime = $row['end_date'] ? $row['end_date'] : $row['start_date'];
    if ($row['end_time'] && $row['end_time'] != "00:00:00") {
        $endDatetime .= 'T' . $row['end_time'];
    } else {
        if ($endDatetime == $row['start_date']) {
            $endDatetime = date('Y-m-d', strtotime($endDatetime . ' +1 day'));
        }
    }

    $events[] = [
        'id' => $row['id'],
        'title' => $row['event_name'],
        'start' => $startDatetime,
        'end' => $endDatetime,
        'organizer' => $row['organizer']
    ];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Event Calendar</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet" />

  <style>
    body {
      padding: 20px;
      background: #f8f9fa;
    }
    #calendar {
      max-width: 900px;
      margin: 0 auto;
      max-height: 700px;
      overflow-y: auto;
      background: #fff;
      border-radius: 6px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
      padding: 15px;
    }
  </style>
</head>
<body>

  <div class="container">
    <h1 class="mb-4 text-center">Event Calendar</h1>
    <div class="text-center container mt-3">
  <a href="download-event-calendar.php" class="btn btn-primary" target="_blank">
    <i class="fa fa-download"></i> Download Event Calendar (PDF)
  </a>
</div>


    <div id="calendar"></div>

    <div class="text-center mt-4">
      <a href="event-list.php" class="btn btn-secondary">Back to Event List</a>
    </div>
    
  </div>
  

  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      var calendarEl = document.getElementById('calendar');

      var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        height: 650,
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: <?php echo json_encode($events); ?>,
        eventClick: function(info) {
  var eventId = info.event.id;
  var organizer = info.event.extendedProps.organizer || '';
  // encode organizer for URL
  organizer = encodeURIComponent(organizer);

  var url = `event_view?event_id=${eventId}&cc_name=${organizer}`;
  window.location.href = url;
}

      });

      calendar.render();
    });
  </script>

</body>
</html>
