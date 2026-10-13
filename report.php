<?php

session_start();

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

include "db.php";


/* Get student attendance report */

$sql = "
SELECT
    students.id,
    students.roll_no,
    students.name,
    students.course,
    students.semester,

    COUNT(attendance.id) AS total_days,

    SUM(
        CASE
            WHEN attendance.status = 'Present'
            THEN 1
            ELSE 0
        END
    ) AS present_days,

    SUM(
        CASE
            WHEN attendance.status = 'Absent'
            THEN 1
            ELSE 0
        END
    ) AS absent_days

FROM students

LEFT JOIN attendance
ON students.id = attendance.student_id

GROUP BY
    students.id,
    students.roll_no,
    students.name,
    students.course,
    students.semester

ORDER BY students.roll_no ASC
";


$result = mysqli_query($conn, $sql);

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Attendance Reports</title>

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

<h2>Attendance Reports</h2>

<p>Student-wise attendance summary</p>

</div>


<div>

<button
onclick="window.print()"
class="print-btn"
>
🖨 Print Report
</button>


<a
href="export.php"
class="add-btn"
>
📥 Export CSV
</a>

</div>

</div>


<div class="dashboard-section">


<div class="table-container">

<table>

<thead>

<tr>

<th>Roll No</th>

<th>Student Name</th>

<th>Course</th>

<th>Semester</th>

<th>Total Days</th>

<th>Present</th>

<th>Absent</th>

<th>Attendance %</th>

<th>Action</th>

</tr>

</thead>


<tbody>


<?php if (mysqli_num_rows($result) > 0): ?>


<?php while ($row = mysqli_fetch_assoc($result)): ?>


<?php

$total_days = (int)$row['total_days'];

$present_days = (int)$row['present_days'];

$absent_days = (int)$row['absent_days'];


if ($total_days > 0) {

    $percentage =
        round(
            ($present_days / $total_days) * 100,
            1
        );

} else {

    $percentage = 0;

}


?>


<tr>


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

<?php echo $total_days; ?>

</td>


<td>

<span class="status-present">

<?php echo $present_days; ?>

</span>

</td>


<td>

<span class="status-absent">

<?php echo $absent_days; ?>

</span>

</td>


<td>


<?php if ($total_days == 0): ?>

<span class="attendance-none">

No Data

</span>


<?php elseif ($percentage < 75): ?>

<span class="attendance-low">

<?php echo $percentage; ?>%

</span>


<?php else: ?>

<span class="attendance-good">

<?php echo $percentage; ?>%

</span>

<?php endif; ?>


</td>


<td>

<a
href="student_report.php?id=<?php echo $row['id']; ?>"
class="edit-btn"
>
View
</a>

</td>


</tr>


<?php endwhile; ?>


<?php else: ?>


<tr>

<td colspan="9" style="text-align:center;">

No students found.

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