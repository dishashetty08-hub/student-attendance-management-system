<?php

session_start();

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

include "db.php";

if (!isset($_GET['id'])) {
    header("Location: report.php");
    exit();
}

$id = (int)$_GET['id'];


/* Get student details */

$stmt = mysqli_prepare(
    $conn,
    "SELECT * FROM students WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$student = mysqli_fetch_assoc($result);

if (!$student) {
    die("Student not found.");
}


/* Attendance summary */

$stmt = mysqli_prepare(
    $conn,
    "SELECT
        COUNT(*) AS total_days,
        SUM(status = 'Present') AS present_days,
        SUM(status = 'Absent') AS absent_days
     FROM attendance
     WHERE student_id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$summary = mysqli_fetch_assoc($result);


$total_days = (int)$summary['total_days'];
$present_days = (int)$summary['present_days'];
$absent_days = (int)$summary['absent_days'];


if ($total_days > 0) {

    $percentage =
        round(
            ($present_days / $total_days) * 100,
            1
        );

} else {

    $percentage = 0;

}


/* Attendance history */

$stmt = mysqli_prepare(
    $conn,
    "SELECT attendance_date, status
     FROM attendance
     WHERE student_id = ?
     ORDER BY attendance_date DESC"
);

mysqli_stmt_bind_param($stmt, "i", $id);

mysqli_stmt_execute($stmt);

$history = mysqli_stmt_get_result($stmt);

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Student Attendance Report</title>

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

<h2>Student Attendance Report</h2>

<p>Detailed attendance information</p>

</div>


<div>

<button
onclick="window.print()"
class="print-btn"
>
🖨 Print Report
</button>

<a
href="report.php"
class="clear-btn"
>
← Back to Reports
</a>

</div>

</div>


<!-- Student Information -->

<div class="dashboard-section">

<h2>Student Information</h2>


<div class="student-info-grid">


<div>

<strong>Roll Number</strong>

<p>
<?php echo htmlspecialchars($student['roll_no']); ?>
</p>

</div>


<div>

<strong>Name</strong>

<p>
<?php echo htmlspecialchars($student['name']); ?>
</p>

</div>


<div>

<strong>Phone</strong>

<p>
<?php echo htmlspecialchars($student['phone']); ?>
</p>

</div>


<div>

<strong>Email</strong>

<p>
<?php echo htmlspecialchars($student['email']); ?>
</p>

</div>


<div>

<strong>Course</strong>

<p>

<span class="course-badge">

<?php echo htmlspecialchars($student['course']); ?>

</span>

</p>

</div>


<div>

<strong>Semester</strong>

<p>
Semester <?php echo htmlspecialchars($student['semester']); ?>
</p>

</div>


</div>

</div>


<!-- Attendance Summary -->

<div class="dashboard-section">

<h2>Attendance Summary</h2>


<div class="dashboard-cards">


<div class="dashboard-card">

<h3>Total Days</h3>

<div class="card-number">

<?php echo $total_days; ?>

</div>

</div>


<div class="dashboard-card">

<h3>Present</h3>

<div class="card-number">

<?php echo $present_days; ?>

</div>

</div>


<div class="dashboard-card">

<h3>Absent</h3>

<div class="card-number">

<?php echo $absent_days; ?>

</div>

</div>


<div class="dashboard-card">

<h3>Attendance</h3>

<div class="card-number">

<?php echo $percentage; ?>%

</div>

</div>

</div>

</div>


<!-- Attendance History -->

<div class="dashboard-section">

<h2>Attendance History</h2>


<div class="table-container">

<table>

<thead>

<tr>

<th>Date</th>

<th>Status</th>

</tr>

</thead>


<tbody>


<?php if (mysqli_num_rows($history) > 0): ?>


<?php while ($row = mysqli_fetch_assoc($history)): ?>


<tr>

<td>

<?php

echo date(
    "d M Y",
    strtotime($row['attendance_date'])
);

?>

</td>


<td>


<?php if ($row['status'] == 'Present'): ?>

<span class="status-present">

✓ Present

</span>


<?php else: ?>

<span class="status-absent">

✕ Absent

</span>

<?php endif; ?>


</td>

</tr>


<?php endwhile; ?>


<?php else: ?>


<tr>

<td colspan="2" style="text-align:center;">

No attendance records found.

</td>

</tr>


<?php endif; ?>


</tbody>

</table>

</div>

</div>


</main>

</body>

</html>