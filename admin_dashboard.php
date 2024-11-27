<?php
session_start();
require_once 'db.php'; 

if (isset($_GET['message']) && $_GET['message'] === 'updated') {
    echo "<script>alert('Student details updated successfully!');</script>";
}
if (isset($_GET['message']) && $_GET['message'] === 'deleted') {
    echo "<script>alert('Schedule deleted successfully!');</script>";
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_schedule'])) {
    $student_id = (int)$_POST['student_id'];
    $subject = trim($_POST['subject']);
    $schedule_day = trim($_POST['schedule_day']);
    $schedule_start_time = date('H:i:s', strtotime($_POST['schedule_start_time']));
    $schedule_end_time = date('H:i:s', strtotime($_POST['schedule_end_time']));
    $schedule_room = trim($_POST['schedule_room']);

    if ($schedule_start_time >= $schedule_end_time) {
        echo "<script>alert('End time must be later than start time.');</script>";
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO student_schedules (student_id, subject, schedule_day, schedule_start_time, schedule_end_time, schedule_room) 
            VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("isssss", $student_id, $subject, $schedule_day, $schedule_start_time, $schedule_end_time, $schedule_room);

        if ($stmt->execute()) {
            header('Location: admin_dashboard.php?message=success');
            exit;
        } else {
            echo "<script>alert('Error adding schedule: " . $stmt->error . "');</script>";
        }
    }
}

$query = "SELECT id, full_name FROM students";
$students_result = $conn->query($query);

$schedules_query = "
    SELECT si.full_name, ss.subject, ss.schedule_day, ss.schedule_start_time, ss.schedule_end_time, ss.schedule_room, ss.id AS schedule_id, ss.student_id
    FROM student_schedules ss
    JOIN students si ON ss.student_id = si.id
    ORDER BY si.full_name, ss.schedule_day, ss.schedule_start_time
";
$schedules_result = $conn->query($schedules_query);
$schedules_by_student = [];
while ($row = $schedules_result->fetch_assoc()) {
    $student_id = $row['student_id'];
    if (!isset($schedules_by_student[$student_id])) {
        $schedules_by_student[$student_id] = [];
    }
    $schedules_by_student[$student_id][] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="CSS\admindash_style.css">
</head>
<body>

<div class="container mx-auto px-4 py-4 flex justify-between items-center bg-purple-600">
    <h1 class="text-2xl font-bold text-white">Admin Dashboard</h1>
    <nav class="navbar">
        <a href="student_record.php">
            <button class="btn">Students</button>
        </a>
        <a href="login.php">
            <button class="btn">Logout</button>
        </a>
    </nav>
</div>

<main class="container mx-auto px-4 py-8">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Student Schedules</h2>
            <button class="btn btn-outline" id="addScheduleButton">Add Schedule</button>
        </div>
        <div class="card-content">
            
            <div id="addScheduleForm" style="display: none;">
                <form method="POST" action="admin_dashboard.php">
                    <div class="form-group">
                        <label for="student">Student</label>
                        <select name="student_id" id="student" required>
                            <?php while ($student = $students_result->fetch_assoc()) { ?>
                                <option value="<?php echo $student['id']; ?>"><?php echo $student['full_name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" id="subject" name="subject" required>
                    </div>

                    <div class="form-group">
                        <label for="schedule_day">Day</label>
                        <input type="text" id="schedule_day" name="schedule_day" required>
                    </div>

                    <div class="form-group">
                        <label for="schedule_start_time">Start Time</label>
                        <input type="time" id="schedule_start_time" name="schedule_start_time" required>
                    </div>

                    <div class="form-group">
                        <label for="schedule_end_time">End Time</label>
                        <input type="time" id="schedule_end_time" name="schedule_end_time" required>
                    </div>

                    <div class="form-group">
                        <label for="schedule_room">Room</label>
                        <input type="text" id="schedule_room" name="schedule_room" required>
                    </div>

                    <button type="submit" name="add_schedule" class="btn btn-primary">Add Schedule</button>
                </form>
            </div>

            <div class="schedule-table">
    <?php foreach ($schedules_by_student as $student_id => $schedules) { ?>
        <div class="schedule-table-header">
            <h3><?php echo $schedules[0]['full_name']; ?>'s Schedule</h3>
        </div>
        <table>
            <thead>
                <tr>
                <th>Subject</th>
                    <th>Day</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Room</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($schedules as $schedule) { ?>
                    <tr>
                        <td><?php echo $schedule['subject']; ?></td>
                        <td><?php echo $schedule['schedule_day']; ?></td>
                        <td><?= htmlspecialchars(date('h:i A', strtotime($schedule['schedule_start_time']))) ?></td>
                    <td><?= htmlspecialchars(date('h:i A', strtotime($schedule['schedule_end_time']))) ?></td>
                        <td><?php echo $schedule['schedule_room']; ?></td>
                        <td>
    <button class="btn btn-edit" onclick="editSchedule(<?php echo $schedule['schedule_id']; ?>)">Edit</button>
    <button class="btn btn-delete" onclick="deleteSchedule(<?php echo $schedule['schedule_id']; ?>)">Delete</button>
</td>

                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } ?>
</div>

        </div>
    </div>
</main>

<script>
    document.getElementById('addScheduleButton').onclick = function() {
        var form = document.getElementById('addScheduleForm');
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
    };

     
     function editSchedule(scheduleId) {
       
        window.location.href = "edit_schedule.php?schedule_id=" + scheduleId;
    }

    
    function deleteSchedule(scheduleId) {
       
        if (confirm("Are you sure you want to delete this schedule?")) {
         
            window.location.href = "delete_schedule.php?schedule_id=" + scheduleId;
        }
    }
</script>

</body>
</html>
