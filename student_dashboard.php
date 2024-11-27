<?php
session_start();
require_once 'db.php'; 

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: login.php'); 
    exit;
}

$student_id = $_SESSION['user_id'];

function getStudentInfo($conn, $student_id) {
    $stmt = $conn->prepare("SELECT full_name, age, course FROM students_info WHERE id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function getStudentSchedule($conn, $student_id) {
    $stmt = $conn->prepare("SELECT subject, schedule_day, schedule_start_time, schedule_end_time, schedule_room FROM student_schedules WHERE student_id = ?");
    
    if ($stmt === false) {
        die('MySQL prepare error: ' . $conn->error);  
    }

    $stmt->bind_param("i", $student_id);
    
    if (!$stmt->execute()) {
        die('Query execution error: ' . $stmt->error); 
    }

    return $stmt->get_result();
}

$student = getStudentInfo($conn, $student_id);

$schedule_result = getStudentSchedule($conn, $student_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="CSS\studentdashboard_style.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="header">
            <h2>Welcome, <?= htmlspecialchars($student['full_name']) ?>!</h2>
            <a href="logout.php" class="logout-button">
                <span>Logout</span>
            </a>
        </div>

        <div class="profile-section">
            <div class="profile-info">
                <h3>Your Profile</h3>
                <p><strong>Full Name:</strong> <?= htmlspecialchars($student['full_name']) ?></p>
                <p><strong>Age:</strong> <?= htmlspecialchars($student['age']) ?></p>
                <p><strong>Course:</strong> <?= htmlspecialchars($student['course']) ?></p>
            </div>
        </div>

        <h3>Your Schedule</h3>
        <?php if ($schedule_result->num_rows > 0): ?>
        <table class="schedule-table">
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>Day</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Room</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($schedule = $schedule_result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($schedule['subject']) ?></td>
                    <td><?= htmlspecialchars($schedule['schedule_day']) ?></td>
                    <td><?= htmlspecialchars(date('h:i A', strtotime($schedule['schedule_start_time']))) ?></td>
                    <td><?= htmlspecialchars(date('h:i A', strtotime($schedule['schedule_end_time']))) ?></td>
                    <td><?= htmlspecialchars($schedule['schedule_room']) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
        <p>You don't have any scheduled classes yet. Please contact your admin for your schedule.</p>
        <?php endif; ?>
    </div>
</body>
</html>
