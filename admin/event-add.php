<?php
// Your OpenWeatherMap API key
$apiKey = '2254406ddee3d46d953afb66c3ff8533';

// Latitude and Longitude for the event location (example: New York)
$lat = 29.124468;
$lon =79.515659;

// Build the API URL with lat/lon
$apiUrl = "https://api.openweathermap.org/data/2.5/forecast?lat=$lat&lon=$lon&appid=$apiKey&units=metric";

// Fetch weather data
$weatherData = @file_get_contents($apiUrl);
$weather = null;
if ($weatherData !== false) {
    $weather = json_decode($weatherData, true);
}

// Extract 5-day daily forecasts (pick 12:00:00 for each day)
$dailyForecasts = [];
if ($weather && isset($weather['list'])) {
    foreach ($weather['list'] as $forecast) {
        if (strpos($forecast['dt_txt'], '12:00:00') !== false) {
            $dailyForecasts[] = $forecast;
            if (count($dailyForecasts) == 5) break;
        }
    }
}
?>


<?php  
include "master-file.php";
include "admin-header.php";
?>

<?php if ($dailyForecasts): ?>
<div class="container my-4 p-3 border rounded bg-light">
    <h4>5-Day Weather Forecast GEHU HALDWANI CAMPUS(Lat: <?php echo $lat; ?>, Lon: <?php echo $lon; ?>) </h4>
    <div class="d-flex justify-content-between flex-wrap">
        <?php foreach ($dailyForecasts as $day): 
            $date = date('D, M j', strtotime($day['dt_txt']));
            $temp = round($day['main']['temp']);
            $icon = $day['weather'][0]['icon'];
            $desc = ucfirst($day['weather'][0]['description']);
        ?>
        <div class="text-center mx-2" style="min-width: 100px;">
            <strong><?php echo $date; ?></strong><br />
            <img src="https://openweathermap.org/img/wn/<?php echo $icon; ?>@2x.png" alt="<?php echo $desc; ?>" style="width:50px;height:50px;"><br />
            <span><?php echo $temp; ?>°C</span><br />
            <small><?php echo $desc; ?></small>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php else: ?>
<div class="container my-4 p-3 border rounded bg-warning text-center">
    <strong>Weather data unavailable at the moment.</strong>
</div>
<?php endif; ?>

<?php
if (isset($_POST['submit'])) {
    // Sanitize inputs
    $e_name = mysqli_real_escape_string($conn, trim($_POST['event_name']));
    $e_venue = mysqli_real_escape_string($conn, trim($_POST['event_venue']));
    $s_date = $_POST['start_date'];
    $e_date = $_POST['end_date'];
    $s_time = $_POST['start_time'];
    $e_time = $_POST['end_time'];
    $e_organizer = mysqli_real_escape_string($conn, trim($_POST['club']));
    $event_type = mysqli_real_escape_string($conn, $_POST['event_type']);
    $event_fee = ($event_type === 'paid') ? mysqli_real_escape_string($conn, trim($_POST['ev_fee'])) : null;
    $description = mysqli_real_escape_string($conn, trim($_POST['desc']));
    $date = date('Y-m-d');

    // Create event table name
    $tb_event_name = strtolower(str_ireplace(' ', '', $e_name));
    $table_event = '_tb_' . $tb_event_name;

    // File upload paths
    $image_name = $_FILES['event_image']['name'];
    $temp_name = $_FILES['event_image']['tmp_name'];
    $image_url = null;

    $qr_name = $_FILES['e_qr']['name'] ?? '';
    $qr_temp = $_FILES['e_qr']['tmp_name'] ?? '';
    $qr_url = null;

    $upload_dir = $_SERVER['DOCUMENT_ROOT'] .'/Eventara/upload-image/';
    // $upload_dir = $_SERVER['DOCUMENT_ROOT'] .'/upload-image/';

 
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Upload event image
    if (!empty($image_name)) {
        $image_url = $image_name;
        if (!move_uploaded_file($temp_name, $upload_dir . $image_name)) {
            echo "<script>alert('Error uploading event image.');</script>";
            exit();
        }
    } else {
        echo "<script>alert('Event image is required.');</script>";
        exit();
    }

    // Upload QR code (optional)
    if (!empty($qr_name)) {
        $qr_url = $qr_name;
        if (!move_uploaded_file($qr_temp, $upload_dir . $qr_name)) {
            echo "<script>alert('Error uploading QR code image.');</script>";
            exit();
        }
    }

    // Check if event table already exists
    $checkTable = mysqli_query($conn, "SHOW TABLES LIKE '$table_event'");
    if (mysqli_num_rows($checkTable) > 0) {
        echo "<script>alert('Event table already exists, please choose a different event name.');</script>";
        exit();
    }

    // Insert event record
    $query = "INSERT INTO `event-list` 
    (`event_name`, `event_venue`, `organizer`, `event_type`, `ev_fee`, `e_qr`, `description`, `start_date`, `end_date`, `start_time`, `end_time`, `event_image`, `event_table`, `date`) 
    VALUES 
    ('$e_name', '$e_venue', '$e_organizer', '$event_type', " . ($event_fee ? "'$event_fee'" : "NULL") . ", " . ($qr_url ? "'$qr_url'" : "NULL") . ", '$description', '$s_date', '$e_date', '$s_time', '$e_time', '$image_url', '$table_event', '$date')";

    if (mysqli_query($conn, $query)) {
        // Create event joiner table
        $createTableQuery = "CREATE TABLE `$table_event` (
            id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            joiner_id VARCHAR(100) NOT NULL,
            joiner_name VARCHAR(100) NOT NULL,
            joiner_email VARCHAR(100) NOT NULL,
            joinDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";

        if (mysqli_query($conn, $createTableQuery)) {
            echo "<script>alert('Event added successfully!'); window.location.href='main.php';</script>";
        } else {
            echo "<script>alert('Event added but failed to create join table: " . mysqli_error($conn) . "');</script>";
        }
    } else {
        echo "<script>alert('Database error while inserting event: " . mysqli_error($conn) . "');</script>";
    }
}
?>

<div class="form-wrap">
    <div class="container shadow">
        <div class="row">
            <div class="col-md-12 form-heading">
                <h1 class="btn-block">Add Event</h1>
            </div>
        </div>
        <div class="row justify-content-center align-items-center" id="signup">
            <div class="col-md-12">
                <form action="event-add.php" method="post" class="form p-md-5" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mt-3">
                            <label>Event Name<span class="imp-op">*</span></label>
                            <input required type="text" name="event_name" class="form-control" />
                        </div>
                        <div class="col-md-6 mt-3">
                            <label>Event Venue<span class="imp-op">*</span></label>
                            <input required type="text" name="event_venue" class="form-control" />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 mt-3">
                            <label>Start Date<span class="imp-op">*</span></label>
                            <input required type="date" name="start_date" class="form-control" />
                        </div>
                        <div class="col-md-3 mt-3">
                            <label>Start Time<span class="imp-op">*</span></label>
                            <input required type="time" name="start_time" class="form-control" />
                        </div>
                        <div class="col-md-3 mt-3">
                            <label>End Date<span class="imp-op">*</span></label>
                            <input required type="date" name="end_date" class="form-control" />
                        </div>
                        <div class="col-md-3 mt-3">
                            <label>End Time<span class="imp-op">*</span></label>
                            <input required type="time" name="end_time" class="form-control" />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mt-3">
                            <label>Organizer (Club)<span class="imp-op">*</span></label>
                            <select name="club" class="form-control" required>
                                <option value="">Choose Club</option>
                                <?php
                                $clubs_query = "SELECT club_name FROM club_info WHERE verify='1' ORDER BY club_name ASC";
                                $clubs_result = mysqli_query($conn, $clubs_query);
                                if ($clubs_result && mysqli_num_rows($clubs_result) > 0) {
                                    while ($row = mysqli_fetch_assoc($clubs_result)) {
                                        $club_name = htmlspecialchars($row['club_name'], ENT_QUOTES, 'UTF-8');
                                        echo "<option value=\"$club_name\">$club_name</option>";
                                    }
                                } else {
                                    echo "<option value=\"\">No clubs found</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-6 mt-3">
                            <label>Event Type<span class="imp-op">*</span></label>
                            <select id="event_type" name="event_type" class="form-control" required onchange="toggleFeeInput()">
                                <option value="free" selected>Free</option>
                                <option value="paid">Paid</option>
                            </select>
                        </div>
                    </div>

                    <div class="row" id="feeRow" style="display:none;">
                        <div class="col-md-6 mt-3">
                            <label>Event Fee (₹)<span class="imp-op">*</span></label>
                            <input type="text" name="ev_fee" id="ev_fee" class="form-control" />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mt-3">
                            <label>Event Image<span class="imp-op">*</span></label>
                            <input required type="file" name="event_image" class="form-control" accept="image/*" />
                        </div>
                        <div class="col-md-6 mt-3">
                            <label>Event QR Code (Optional)</label>
                            <input type="file" name="e_qr" class="form-control" accept="image/*" />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mt-3">
                            <label>Event Description<span class="imp-op">*</span></label>
                            <textarea required class="form-control" name="desc" style="height: 300px;"></textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <button name="submit" type="submit" class="form-control mt-4 btn btn-success">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function toggleFeeInput() {
    const eventType = document.getElementById('event_type').value;
    const feeRow = document.getElementById('feeRow');
    const feeInput = document.getElementById('ev_fee');
    if (eventType === 'paid') {
        feeRow.style.display = 'block';
        feeInput.setAttribute('required', 'required');
    } else {
        feeRow.style.display = 'none';
        feeInput.removeAttribute('required');
        feeInput.value = '';
    }
}
toggleFeeInput();
</script>
