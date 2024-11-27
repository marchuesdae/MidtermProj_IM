<?php
session_start();
require_once 'db.php';


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}


if (isset($_GET['schedule_id'])) {
    $schedule_id = (int)$_GET['schedule_id'];

    
    $query = "SELECT * FROM student_schedules WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $schedule_id);
    $stmt->execute();
    $result = $stmt->get_result();

    
    if ($result->num_rows > 0) {
        $schedule = $result->fetch_assoc();
    } else {
        echo "Schedule not found.";
        exit;
    }
} else {
    echo "Schedule ID is missing.";
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_schedule'])) {
    $subject = trim($_POST['subject']);
    $schedule_day = trim($_POST['schedule_day']);
    $schedule_start_time = date('H:i:s', strtotime($_POST['schedule_start_time']));
    $schedule_end_time = date('H:i:s', strtotime($_POST['schedule_end_time']));
    $schedule_room = trim($_POST['schedule_room']);

    
    if ($schedule_start_time >= $schedule_end_time) {
        echo "<script>alert('End time must be later than start time.');</script>";
    } else {
       
        $query = "UPDATE student_schedules SET subject = ?, schedule_day = ?, schedule_start_time = ?, schedule_end_time = ?, schedule_room = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssssi", $subject, $schedule_day, $schedule_start_time, $schedule_end_time, $schedule_room, $schedule_id);

       
        if ($stmt->execute()) {
            header('Location: admin_dashboard.php?message=updated');
            exit;
        } else {
            echo "<script>alert('Error updating schedule: " . $stmt->error . "');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Schedule</title>
    <link rel="stylesheet" href="CSS\editsched.css">
</head>
<body>

<h1>Edit Schedule</h1>

<div class="container">

    <form method="POST" action="edit_schedule.php?schedule_id=<?php echo $schedule_id; ?>">
        <div class="form-group">
            <label for="subject">Subject</label>
            <input type="text" id="subject" name="subject" value="<?php echo $schedule['subject']; ?>" required>
        </div>

        <div class="form-group">
            <label for="schedule_day">Day</label>
            <input type="text" id="schedule_day" name="schedule_day" value="<?php echo $schedule['schedule_day']; ?>" required>
        </div>

        <div class="form-group">
            <label for="schedule_start_time">Start Time</label>
            <input type="time" id="schedule_start_time" name="schedule_start_time" value="<?php echo date('H:i', strtotime($schedule['schedule_start_time'])); ?>" required>
        </div>

        <div class="form-group">
            <label for="schedule_end_time">End Time</label>
            <input type="time" id="schedule_end_time" name="schedule_end_time" value="<?php echo date('H:i', strtotime($schedule['schedule_end_time'])); ?>" required>
        </div>

        <div class="form-group">
            <label for="schedule_room">Room</label>
            <input type="text" id="schedule_room" name="schedule_room" value="<?php echo $schedule['schedule_room']; ?>" required>
        </div>

        <button type="submit" name="update_schedule">Update Schedule</button>
    </form>
</div>


</body>
</html>
