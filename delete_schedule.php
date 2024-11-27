<?php
// Include the database connection file
include 'db.php'; // Adjust this path if needed

// Check if $conn is set and valid
if (!isset($conn) || $conn === null) {
    die("Database connection error.");
}

// Check if schedule_id is set in the URL
if (isset($_GET['schedule_id'])) {
    $schedule_id = intval($_GET['schedule_id']); // Sanitize input

    // Prepare and execute the DELETE query
    $stmt = $conn->prepare("DELETE FROM student_schedules WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $schedule_id);
        $stmt->execute();
        header("Location: admin_dashboard.php?message=deleted");
        exit;
    } else {
        die("Failed to prepare the statement: " . $conn->error);
    }
} else {
    die("Invalid request. No schedule ID provided.");
}
?>
