<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}
$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile</title>
</head>
<body>
<h1>Welcome, <?= htmlspecialchars($student['full_name']) ?>!</h1>
<p>Your Username: <?= htmlspecialchars($student['username']) ?></p>

    <p>Role: <?php echo $user['role']; ?></p>
</body>
</html>
