<?php
session_start();
require_once 'db.php';


if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}


$student_id = isset($_GET['id']) ? $_GET['id'] : $_SESSION['user_id'];


$result = $conn->query("SELECT * FROM students_info WHERE id = $student_id");
if ($result->num_rows === 0) {
    echo "Student not found.";
    exit;
}

$student = $result->fetch_assoc();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['full_name'];
    $age = $_POST['age'];
    $course = $_POST['course'];
    $room_assignment = $_POST['room_assignment'];
    $schedule = $_POST['schedule'];

    $stmt = $conn->prepare(
        "UPDATE students_info 
         SET name = ?, age = ?, age = ?, course = ?, room_assignment = ?, schedule = ? 
         WHERE id = ?"
    );
    $stmt->bind_param("sisssi", $name, $age, $course, $room_assignment, $schedule, $student_id);

    if ($stmt->execute()) {
        echo "Student information updated successfully!";
        header('Location: admin_dashboard.php');
        exit;
    } else {
        echo "Error updating student: " . $conn->error;
    }
}
?>
