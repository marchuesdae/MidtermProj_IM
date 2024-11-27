<?php
session_start();
require_once 'db.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);  
    $full_name = $_POST['full_name'];
    $age = $_POST['age'];
    $course = $_POST['course'];

    // Check if username already exists in the 'students' table
    $stmt = $conn->prepare("SELECT id FROM students WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Username exists, show SweetAlert
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Username already taken',
                    text: 'Please choose another username.',
                    confirmButtonText: 'OK'
                });
              </script>";
    } else {
        // Proceed with user registration
        $conn->begin_transaction();

        try {
            $stmt = $conn->prepare("INSERT INTO students (username, password, full_name) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $password, $full_name);
            $stmt->execute();
            $user_id = $conn->insert_id; 

            $stmt_info = $conn->prepare("INSERT INTO students_info (id, full_name, age, course) VALUES (?, ?, ?, ?)");
            $stmt_info->bind_param("isis", $user_id, $full_name, $age, $course);
            $stmt_info->execute();

            $conn->commit();

            $_SESSION['user_id'] = $user_id;
            $_SESSION['full_name'] = $full_name;

            header('Location: login.php');
            exit;

        } catch (Exception $e) {
            $conn->rollback();
            echo "Error: " . $e->getMessage();
        }
    }
}
?>
