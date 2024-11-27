<?php
require_once 'db.php';

if (isset($_POST['student_id'])) {
    $student_id = $_POST['student_id'];

    
    $query = "DELETE FROM students WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $student_id);

    if ($stmt->execute()) {
        echo 'success';
    } else {
        echo 'error';
    }
}
?>
