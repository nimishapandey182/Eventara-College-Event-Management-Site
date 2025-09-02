<?php
include "master-file.php";
include "admin-header.php";

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('Event ID not specified.'); window.location.href='event-list.php';</script>";
    exit();
}

$event_id = intval($_GET['id']);

// Fetch current event details
$query = "SELECT * FROM `event-list` WHERE id = $event_id LIMIT 1";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    echo "<script>alert('Event not found.'); window.location.href='event-list.php';</script>";
    exit();
}

$event = mysqli_fetch_assoc($result);

if (isset($_POST['submit'])) {
    $e_name = mysqli_real_escape_string($conn, $_POST['event_name']);
    $e_venue = mysqli_real_escape_string($conn, $_POST['event_venue']);
    $s_date = $_POST['start_date'];
    $e_date = $_POST['end_date'];
    $e_organizer = mysqli_real_escape_string($conn, $_POST['organizer']);
    $e_type = mysqli_real_escape_string($conn, $_POST['event_type']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $ev_fee = mysqli_real_escape_string($conn, $_POST['ev_fee']);
    
    // Handle image upload (optional)
    $image_name = $_FILES['event_image']['name'];
    $temp_name = $_FILES['event_image']['tmp_name'];
    $image_updated = false;

    if (!empty($image_name)) {
        $destination = $_SERVER['DOCUMENT_ROOT'] . '/upload-image/' . $image_name;
        if (!file_exists(dirname($destination))) {
            mkdir(dirname($destination), 0755, true);
        }
        if (move_uploaded_file($temp_name, $destination)) {
            $image_updated = true;
        } else {
            echo "<script>alert('Failed to upload image.');</script>";
        }
    }

    // Prepare update query
    if ($image_updated) {
        $update_query = "UPDATE `event-list` SET 
            event_name = '$e_name',
            event_venue = '$e_venue',
            organizer = '$e_organizer',
            event_type = '$e_type',
            description = '$description',
            start_date = '$s_date',
            end_date = '$e_date',
            event_image = '$image_name',
            ev_fee = '$ev_fee'
            WHERE id = $event_id";
    } else {
        // No new image uploaded, keep old image
        $update_query = "UPDATE `event-list` SET 
            event_name = '$e_name',
            event_venue = '$e_venue',
            organizer = '$e_organizer',
            event_type = '$e_type',
            description = '$description',
            start_date = '$s_date',
            end_date = '$e_date',
            ev_fee = '$ev_fee'
            WHERE id = $event_id";
    }

    if (mysqli_query($conn, $update_query)) {
        echo "<script>alert('Event updated successfully!'); window.location.href='event-list.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error updating event: " . mysqli_error($conn) . "');</script>";
    }
}
?>

<div class="container mt-4">
    <h1>Edit Event</h1>
    <a href="event-list.php" class="btn btn-secondary mb-3">Back to Event List</a>

    <form action="event-edit.php?id=<?php echo $event_id; ?>" method="post" enctype="multipart/form-data" class="p-4 border rounded">
        <div class="mb-3">
            <label for="event_name" class="form-label">Event Name *</label>
            <input type="text" name="event_name" id="event_name" class="form-control" required value="<?php echo htmlspecialchars($event['event_name']); ?>">
        </div>

        <div class="mb-3">
            <label for="event_venue" class="form-label">Event Venue *</label>
            <input type="text" name="event_venue" id="event_venue" class="form-control" required value="<?php echo htmlspecialchars($event['event_venue']); ?>">
        </div>

        <div class="mb-3">
            <label for="organizer" class="form-label">Organizer *</label>
            <input type="text" name="organizer" id="organizer" class="form-control" required value="<?php echo htmlspecialchars($event['organizer']); ?>">
        </div>

        <div class="mb-3">
            <label for="event_type" class="form-label">Event Type *</label>
            <select name="event_type" id="event_type" class="form-control" required>
                <option value="free" <?php if($event['event_type'] === 'free') echo 'selected'; ?>>Free</option>
                <option value="paid" <?php if($event['event_type'] === 'paid') echo 'selected'; ?>>Paid</option>
            </select>
        </div>

        <div class="mb-3" id="fee-section" style="<?php echo ($event['event_type'] === 'paid') ? '' : 'display:none;'; ?>">
            <label for="ev_fee" class="form-label">Event Fee (₹)</label>
            <input type="text" name="ev_fee" id="ev_fee" class="form-control" value="<?php echo htmlspecialchars($event['ev_fee']); ?>">
        </div>

        <div class="mb-3">
            <label for="start_date" class="form-label">Start Date *</label>
            <input type="date" name="start_date" id="start_date" class="form-control" required value="<?php echo $event['start_date']; ?>">
        </div>

        <div class="mb-3">
            <label for="end_date" class="form-label">End Date *</label>
            <input type="date" name="end_date" id="end_date" class="form-control" required value="<?php echo $event['end_date']; ?>">
        </div>

        <div class="mb-3">
            <label for="event_image" class="form-label">Event Image</label>
            <input type="file" name="event_image" id="event_image" class="form-control" accept="image/*">
            <small>Current Image: <?php echo htmlspecialchars($event['event_image']); ?></small>
            <div class="mt-2">
                <img src="/upload-image/<?php echo htmlspecialchars($event['event_image']); ?>" alt="Current Event Image" style="max-width: 200px;">
            </div>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description *</label>
            <textarea name="description" id="description" rows="6" class="form-control" required><?php echo htmlspecialchars($event['description']); ?></textarea>
        </div>

        <button type="submit" name="submit" class="btn btn-primary">Update Event</button>
    </form>
</div>

<script>
    // Show/hide fee input based on event type selection
    document.getElementById('event_type').addEventListener('change', function() {
        const feeSection = document.getElementById('fee-section');
        if (this.value === 'paid') {
            feeSection.style.display = '';
        } else {
            feeSection.style.display = 'none';
            document.getElementById('ev_fee').value = '';
        }
    });
</script>
