<?php
session_start();
require_once 'db.php';

$login_error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
   
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role = isset($_POST['role']) ? trim($_POST['role']) : '';

    if (!empty($username) && !empty($password) && !empty($role)) {
        if ($role === 'admin') {
            
            $hardcoded_admin_username = "admin";
            $hardcoded_admin_password = "adminPassword";

            if ($username === $hardcoded_admin_username && $password === $hardcoded_admin_password) {
                $_SESSION['user_id'] = 1; 
                $_SESSION['role'] = 'admin';
                $_SESSION['full_name'] = 'Administrator'; 
                header('Location: admin_dashboard.php');
                exit;
            } else {
                $login_error = "Invalid admin username or password.";
            }
        } elseif ($role === 'student') {
           
            $stmt = $conn->prepare("SELECT id, username, password, full_name FROM students WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                if (password_verify($password, $user['password'])) {
                  
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['role'] = 'student';
                    $_SESSION['full_name'] = $user['full_name'];
                    header('Location: student_dashboard.php');
                    exit;
                } else {
                    $login_error = "Invalid student password.";
                }
            } else {
                $login_error = "Student user not found.";
            }
        } else {
            $login_error = "Invalid role selected.";
        }
    } else {
        $login_error = "All fields are required.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login System</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="CSS/login_style.css">
</head>
<body>

    <nav>
        <div class="logo">WindVale University</div>
        <!-- <div class="nav-buttons">
            <button class="button">Home</button>
            <button class="button">About</button>
            <button class="button">Contact</button>
        </div> -->
    </nav>

    <div class="main-container">
        <div class="form-container">
            <div class="tabs">
                <button class="tab-button active" id="loginTab">Login</button>
                <button class="tab-button" id="registerTab">Register</button>
            </div>
            
           
            <div id="loginForm" class="form-content active">
                <form action="login.php" method="POST">
                    <input type="text" name="username" placeholder="Username" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <select name="role" required>
                        <option value="" disabled selected>Select Role</option>
                        <option value="student">Student</option>
                        <option value="admin">Admin</option>
                    </select>
                    <button type="submit" name="login" class="form-button">Log In</button>
                    <?php if (!empty($login_error)): ?>
                        <div class="error-message"><?= htmlspecialchars($login_error) ?></div>
                    <?php endif; ?>
                </form>
            </div>

           
            <div id="registerForm" class="form-content">
            <form method="POST" action="register.php">
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" required>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required>

    <label for="full_name">Full Name:</label>
    <input type="text" id="full_name" name="full_name" required>

    <label for="age">Age:</label>
    <input type="text" id="age" name="age" required>

    <label for="course">Course:</label>
    <input type="text" id="course" name="course" required>

    <button type="submit">Register</button>
</form>

            </div>
        </div>
    </div>

    <footer>
        <p>© 2024 WindVale University. All rights reserved.</p>
    </footer>

    <script>
        document.getElementById("loginTab").onclick = function () {
            document.getElementById("loginForm").classList.add("active");
            document.getElementById("registerForm").classList.remove("active");
            document.getElementById("loginTab").classList.add("active");
            document.getElementById("registerTab").classList.remove("active");
        };

        document.getElementById("registerTab").onclick = function () {
            document.getElementById("loginForm").classList.remove("active");
            document.getElementById("registerForm").classList.add("active");
            document.getElementById("registerTab").classList.add("active");
            document.getElementById("loginTab").classList.remove("active");
        };
    </script>
</body>
</html>
