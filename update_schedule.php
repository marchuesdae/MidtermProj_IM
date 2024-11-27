<?php
session_start();
require_once 'db.php';

if (isset($_POST['update_schedule'])) {

    $schedule_id = $_POST['schedule_id'];
    $subject = $_POST['subject'];
    $schedule_day = $_POST['schedule_day'];
    $schedule_start_time = $_POST['schedule_start_time'];
    $schedule_end_time = $_POST['schedule_end_time'];
    $schedule_room = $_POST['schedule_room'];

   
    $query = "UPDATE student_schedules 
              SET subject = ?, schedule_day = ?, schedule_start_time = ?, schedule_end_time = ?, schedule_room = ?
              WHERE id = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssi", $subject, $schedule_day, $schedule_start_time, $schedule_end_time, $schedule_room, $schedule_id);

    if ($stmt->execute()) {
        
        header("Location: admin_dashboard.php?status=success");
        exit;
    } else {
        echo "Error updating the schedule. Please try again.";
    }

    $stmt->close();
}
?>







