<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
</head>
<body>
    <h1>Welcome, Admin</h1>
    <button onclick="fetchStudents()">View Students</button>
    <div id="students"></div>

    <script>
        function fetchStudents() {
            axios.get('students.php')
                .then(response => {
                    let students = response.data;
                    let output = '<ul>';
                    students.forEach(student => {
                        output += `<li>${student.name} - ${student.course}</li>`;
                    });
                    output += '</ul>';
                    $('#students').html(output);
                })
                .catch(error => console.error(error));
        }
    </script>
</body>
</html>
