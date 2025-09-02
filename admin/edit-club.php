<?php
include "master-file.php";

if (!isset($_GET['id'])) {
    echo "<script>alert('Club ID missing.'); window.history.back();</script>";
    exit;
}

$club_id = intval($_GET['id']);
$query = "SELECT * FROM club_info WHERE id = '$club_id'";
$result = mysqli_query($conn, $query);
$club = mysqli_fetch_assoc($result);

if (!$club) {
    echo "<script>alert('Club not found.'); window.history.back();</script>";
    exit;
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $club_name = mysqli_real_escape_string($conn, $_POST['club_name']);
    $club_description = mysqli_real_escape_string($conn, $_POST['club_description']);
    $club_requirement = mysqli_real_escape_string($conn, $_POST['club_requirement']);

    $update_query = "UPDATE club_info 
                     SET club_name='$club_name', club_description='$club_description', club_requirement='$club_requirement'
                     WHERE id='$club_id'";

    if (mysqli_query($conn, $update_query)) {
        echo "<script>alert('Club updated successfully.'); window.location.href='main.php';</script>";
    } else {
        echo "<script>alert('Update failed: " . mysqli_error($conn) . "');</script>";
    }
}
?>

<form method="POST">
    <div class="form-group">
        <label>Club Name</label>
        <input type="text" name="club_name" class="form-control" value="<?php echo htmlspecialchars($club['club_name']); ?>" required>
    </div>
    <div class="form-group">
        <label>Description</label>
        <textarea name="club_description" class="form-control" rows="5" required><?php echo htmlspecialchars($club['club_description']); ?></textarea>
    </div>
    <div class="form-group">
        <label>Requirements</label>
        <input type="text" name="club_requirement" class="form-control" value="<?php echo htmlspecialchars($club['club_requirement']); ?>" required>
    </div>
    <button type="submit" class="btn btn-primary">Update Club</button>
</form>
