<?php 
include "master.php";

if (!isset($_GET['ev_id'], $_SESSION['register_id'], $_SESSION['user_name'], $_SESSION['last_name'], $_SESSION['email'], $_SESSION['club_name'])) {
    echo "<script>alert('Required data missing.'); window.history.back();</script>";
    exit();
}

$event_id = mysqli_real_escape_string($conn, $_GET['ev_id']);
$date = date('Y-m-d H:i:s');

$register_id = mysqli_real_escape_string($conn, $_SESSION["register_id"]);
$full_name = mysqli_real_escape_string($conn, $_SESSION["user_name"] . " " . $_SESSION["last_name"]);
$email = mysqli_real_escape_string($conn, $_SESSION["email"]);
$club_name = $_SESSION['club_name'];

// Check if already registered
$check_query = "SELECT * FROM `event_registrations` WHERE event_id = '$event_id' AND register_id = '$register_id'";
$check_result = mysqli_query($conn, $check_query);

if (mysqli_num_rows($check_result) > 0) {
    echo "<script>alert('You have already registered for this event!');</script>";
    redirect('event_view?event_id=' . $event_id . '&cc_name=' . urlencode($club_name));
} else {
    $insert_query = "INSERT INTO `event_registrations` 
        (`event_id`, `register_id`, `user_name`, `user_email`, `join_date`, `verified`) 
        VALUES 
        ('$event_id', '$register_id', '$full_name', '$email', '$date', 1)";
    
    if (mysqli_query($conn, $insert_query)) {
        echo "<script>alert('Successfully registered for the event!');</script>";
        redirect('event_view?event_id=' . $event_id . '&cc_name=' . urlencode($club_name));
    } else {
        echo "<script>alert('Error registering: " . mysqli_error($conn) . "');</script>";
    }
}
?>
