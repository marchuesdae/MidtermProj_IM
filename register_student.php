<?php
session_start();
require 'db.php'; 

$error_message = ''; 
$success_message = ''; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password']; 
    $full_name = $conn->real_escape_string($_POST['full_name']);
    $role = isset($_POST['role']) ? $conn->real_escape_string($_POST['role']) : 'student';

    if (!$username || !$password || !$full_name) {
        $error_message = "Error: All fields are required.";
    } else {
        try {
            $stmt = $conn->prepare("CALL AddStudent(?, ?, ?, ?)");
            if (!$stmt) {
                $error_message = 'Failed to prepare: ' . $conn->error;
            } else {
                $stmt->bind_param("ssss", $username, $password, $full_name, $role);
                if ($stmt->execute()) {
                    $success_message = 'Student registered successfully!';
                } else {
                    $error_message = 'Failed to execute: ' . $stmt->error;
                }
            }
        } catch (Exception $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    }

    echo json_encode([
        'success' => $success_message,
        'error' => $error_message
    ]);
    exit;
}
?>
