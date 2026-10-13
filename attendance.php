<?php

session_start();

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

include "db.php";

$date = $_POST['attendance_date'] ?? $_GET['attendance_date'] ?? date("Y-m-d");

$message = "";


/* Save attendance */

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_attendance'])) {

    $attendance = $_POST['attendance'] ?? [];

    /* Remove existing attendance for this date */
    $delete_stmt = mysqli_prepare(
        $conn,
        "DELETE FROM attendance WHERE attendance_date = ?"
    );

    mysqli_stmt_bind_param(
        $delete_stmt,
        "s",
        $date
    );

    mysqli_stmt_execute($delete_stmt);


    /* Insert attendance */

    $insert_stmt = mysqli_prepare(
        $conn,
        "INSERT INTO attendance
        (student_id, attendance_date, status)
        VALUES (?, ?, ?)"
    );

    foreach ($attendance as $student_id => $status) {

        $student_id = (int)$student_id;

        mysqli_stmt_bind_param(
            $insert_stmt,
            "iss",
            $student_id,
            $date,
            $status
        );

        mysqli_stmt_execute($insert_stmt);
    }

    $message = "Attendance saved successfully.";
}


/* Get students */

$students = mysqli_query(
    $conn,
    "SELECT * FROM students ORDER BY roll_no ASC"
);


/* Get existing attendance */

$existing_attendance = [];

$stmt = mysqli_prepare(
    $conn,
    "SELECT student_id, status
     FROM attendance
     WHERE attendance_date = ?"
);

mysqli_stmt_bind_param(
    $stmt,
    "s",
    $date
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

while ($row = mysqli_fetch_assoc($result)) {

    $existing_attendance[$row['student_id']] = $row['status'];
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Attendance - Attendance Management System</title>

<link rel="stylesheet" href="style.css">

</head>


<body>

<header>

<h1>Student Attendance Management System</h1>

<p>
Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>
</p>

</header>


<nav>

<a href="dashboard.php">Dashboard</a>

<a href="students.php">Students</a>

<a href="attendance.php">Attendance</a>

<a href="attendance_history.php">History</a>

<a href="report.php">Reports</a>

<a href="change_password.php">Change Password</a>

<a href="logout.php">Logout</a>

</nav>


<main>


<div class="page-header">

<div>

<h2>Mark Attendance</h2>

<p>Select a date and mark student attendance</p>

</div>

</div>


<?php if ($message != ""): ?>

<div class="success">

<?php echo htmlspecialchars($message); ?>

</div>

<?php endif; ?>


<!-- Date Selection -->

<div class="dashboard-section">

<form method="GET" class="filter-box">

<label>Attendance Date</label>

<input
type="date"
name="attendance_date"
value="<?php echo htmlspecialchars($date); ?>"
required
>

<button type="submit" class="edit-btn">
View Date
</button>

</form>

</div>


<!-- Attendance -->

<div class="dashboard-section">

<div class="attendance-actions">

<button
type="button"
class="add-btn"
onclick="markAll('Present')"
>
✓ Mark All Present
</button>


<button
type="button"
class="delete-btn"
onclick="markAll('Absent')"
>
✕ Mark All Absent
</button>

</div>


<form method="POST">

<input
type="hidden"
name="attendance_date"
value="<?php echo htmlspecialchars($date); ?>"
>


<div class="table-container">

<table>

<thead>

<tr>

<th>Roll No</th>

<th>Student Name</th>

<th>Course</th>

<th>Semester</th>

<th>Present</th>

<th>Absent</th>

</tr>

</thead>


<tbody>


<?php if (mysqli_num_rows($students) > 0): ?>


<?php while ($student = mysqli_fetch_assoc($students)): ?>

<?php

$current_status =
$existing_attendance[$student['id']] ?? '';

?>


<tr>

<td>

<strong>

<?php echo htmlspecialchars($student['roll_no']); ?>

</strong>

</td>


<td>

<?php echo htmlspecialchars($student['name']); ?>

</td>


<td>

<?php echo htmlspecialchars($student['course']); ?>

</td>


<td>

<?php echo htmlspecialchars($student['semester']); ?>

</td>


<td>

<input
type="radio"
name="attendance[<?php echo $student['id']; ?>]"
value="Present"
class="attendance-present"
<?php echo ($current_status == 'Present') ? 'checked' : ''; ?>
>

</td>


<td>

<input
type="radio"
name="attendance[<?php echo $student['id']; ?>]"
value="Absent"
class="attendance-absent"
<?php echo ($current_status == 'Absent') ? 'checked' : ''; ?>
>

</td>

</tr>


<?php endwhile; ?>


<?php else: ?>


<tr>

<td colspan="6" style="text-align:center;">

No students found.

<br><br>

<a href="add_student.php" class="add-btn">
+ Add Student
</a>

</td>

</tr>


<?php endif; ?>


</tbody>

</table>

</div>


<br>


<button
type="submit"
name="save_attendance"
class="edit-btn"
>
💾 Save Attendance
</button>


</form>

</div>


</main>


<script src="script.js"></script>

</body>

</html>