<?php
include "master-file.php";
include "admin-header.php";

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('Event ID not specified.'); window.location.href='event-list.php';</script>";
    exit();
}

$event_id = intval($_GET['id']);

// Fetch event info first to get event_table and event_image
$query = "SELECT event_table, event_image FROM `event-list` WHERE id = $event_id LIMIT 1";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    echo "<script>alert('Event not found.'); window.location.href='event-list.php';</script>";
    exit();
}

$event = mysqli_fetch_assoc($result);
$event_table = $event['event_table'];
$event_image = $event['event_image'];

// Delete event image file if exists
$image_path = $_SERVER['DOCUMENT_ROOT'] . '/upload-image/' . $event_image;
if (file_exists($image_path) && !empty($event_image)) {
    unlink($image_path);
}

// Drop joiners table if exists
if (!empty($event_table)) {
    $drop_table_query = "DROP TABLE IF EXISTS `$event_table`";
    mysqli_query($conn, $drop_table_query);
}

// Delete the event from event-list table
$delete_query = "DELETE FROM `event-list` WHERE id = $event_id";
if (mysqli_query($conn, $delete_query)) {
    echo "<script>alert('Event deleted successfully!'); window.location.href='event-list.php';</script>";
    exit();
} else {
    echo "<script>alert('Error deleting event: " . mysqli_error($conn) . "'); window.location.href='event-list.php';</script>";
    exit();
}
?>
