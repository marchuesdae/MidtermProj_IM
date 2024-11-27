<?php
session_start();
require_once 'db.php'; 

// Ensure admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Fetch student records
$query = "SELECT id, username, full_name FROM students";
$students_result = $conn->query($query);

if (!$students_result) {
    die("Query failed: " . $conn->error);
}

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_student'])) {
    $student_id = filter_var($_POST['student_id'], FILTER_VALIDATE_INT);
    if (!$student_id) {
        echo "<script>alert('Invalid student ID.');</script>";
        exit;
    }

    $delete_query = "DELETE FROM students WHERE id = ?";
    $stmt = $conn->prepare($delete_query);

    if (!$stmt) {
        echo "<script>alert('Error preparing statement: {$conn->error}');</script>";
        exit;
    }

    $stmt->bind_param("i", $student_id);

    if ($stmt->execute()) {
        header('Location: student_record.php?message=deleted');
        exit;
    } else {
        echo "<script>alert('Error executing delete query: {$stmt->error}');</script>";
    }
}

$message = isset($_GET['message']) ? $_GET['message'] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Records</title>
    <link rel="stylesheet" href="CSS\studrecord_style.css">
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Student Records</h2>
                <a href="admin_dashboard.php" class="btn-back">Back to Dashboard</a>
            </div>
            <div class="card-content">
                <?php if ($message === 'deleted'): ?>
                    <div class="alert success">
                        Student deleted successfully!
                    </div>
                <?php endif; ?>
                <table class="student-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Full Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($student = $students_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($student['id']); ?></td>
                                <td><?php echo htmlspecialchars($student['username']); ?></td>
                                <td><?php echo htmlspecialchars($student['full_name']); ?></td>
                                <td>
                                    <form method="POST" action="student_record.php" onsubmit="return confirm('Are you sure you want to delete this student?');">
                                        <input type="hidden" name="student_id" value="<?php echo htmlspecialchars($student['id']); ?>">
                                        <button type="submit" name="delete_student" class="btn-delete">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
