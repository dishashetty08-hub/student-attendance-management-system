<?php

session_start();

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

include "db.php";

$date = $_GET['date'] ?? '';

$sql = "SELECT 
            attendance.attendance_date,
            students.roll_no,
            students.name,
            students.course,
            students.semester,
            attendance.status
        FROM attendance
        INNER JOIN students
        ON attendance.student_id = students.id";

$params = [];
$types = "";

if ($date != '') {
    $sql .= " WHERE attendance.attendance_date = ?";
    $params[] = $date;
    $types .= "s";
}

$sql .= " ORDER BY attendance.attendance_date DESC,
                   students.roll_no ASC";

$stmt = mysqli_prepare($conn, $sql);

if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Attendance History</title>

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

<h2>Attendance History</h2>

<p>View previously recorded attendance</p>

</div>

</div>


<!-- Date Filter -->

<div class="dashboard-section">

<h3>Filter Attendance</h3>

<form method="GET" class="filter-box">

<label>Date</label>

<input
type="date"
name="date"
value="<?php echo htmlspecialchars($date); ?>"
>

<button type="submit" class="edit-btn">
🔍 Search
</button>

<a href="attendance_history.php" class="clear-btn">
Clear
</a>

</form>

</div>


<!-- Attendance Table -->

<div class="dashboard-section">

<div class="table-container">

<table>

<thead>

<tr>

<th>Date</th>

<th>Roll No</th>

<th>Student Name</th>

<th>Course</th>

<th>Semester</th>

<th>Status</th>

</tr>

</thead>


<tbody>

<?php if (mysqli_num_rows($result) > 0): ?>


<?php while ($row = mysqli_fetch_assoc($result)): ?>

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

<strong>

<?php echo htmlspecialchars($row['roll_no']); ?>

</strong>

</td>


<td>

<?php echo htmlspecialchars($row['name']); ?>

</td>


<td>

<span class="course-badge">

<?php echo htmlspecialchars($row['course']); ?>

</span>

</td>


<td>

Semester <?php echo htmlspecialchars($row['semester']); ?>

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

<td colspan="6" style="text-align:center;">

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