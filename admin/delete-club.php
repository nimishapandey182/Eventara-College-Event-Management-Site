<?php
include "master-file.php";

if (!isset($_GET['id'])) {
    echo "<script>alert('No club ID specified.'); window.location.href='club-list.php';</script>";
    exit();
}

$id = intval($_GET['id']);

// Get the associated club_table name before deleting the record
$get_table_query = "SELECT club_table FROM club_info WHERE id = $id";
$table_result = mysqli_query($conn, $get_table_query);

if ($table_result && mysqli_num_rows($table_result) > 0) {
    $row = mysqli_fetch_assoc($table_result);
    $club_table = $row['club_table'];

    // Delete the club record
    $delete_query = "DELETE FROM club_info WHERE id = $id";
    if (mysqli_query($conn, $delete_query)) {
        // Drop the associated club table if it exists
        if (!empty($club_table)) {
            $drop_table_query = "DROP TABLE IF EXISTS `$club_table`";
            mysqli_query($conn, $drop_table_query); // Optional: Check for success
        }

        echo "<script>alert('Club and its data deleted successfully.'); window.location.href='main.php';</script>";
    } else {
        echo "<script>alert('Error deleting club record.'); window.location.href='main.php';</script>";
    }
} else {
    echo "<script>alert('Club not found.'); window.location.href='club-list.php';</script>";
}
?>
