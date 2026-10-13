<?php

session_start();

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

include "db.php";

$search = $_GET['search'] ?? '';
$course = $_GET['course'] ?? '';
$semester = $_GET['semester'] ?? '';

$sql = "SELECT * FROM students WHERE 1=1";

$params = [];
$types = "";

if ($search != '') {

    $sql .= " AND (roll_no LIKE ? OR name LIKE ? OR phone LIKE ? OR email LIKE ?)";

    $search_value = "%" . $search . "%";

    $params[] = $search_value;
    $params[] = $search_value;
    $params[] = $search_value;
    $params[] = $search_value;

    $types .= "ssss";
}

if ($course != '') {

    $sql .= " AND course = ?";

    $params[] = $course;

    $types .= "s";
}

if ($semester != '') {

    $sql .= " AND semester = ?";

    $params[] = $semester;

    $types .= "i";
}

$sql .= " ORDER BY roll_no ASC";

$stmt = mysqli_prepare($conn, $sql);

if (!empty($params)) {

    mysqli_stmt_bind_param(
        $stmt,
        $types,
        ...$params
    );
}

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Students - Attendance Management System</title>

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

<h2>Student Management</h2>

<p>Add, search and manage student information</p>

</div>

<a href="add_student.php" class="add-btn">
+ Add Student
</a>

</div>


<!-- Search and Filter -->

<div class="dashboard-section">

<h3>Search Students</h3>

<form method="GET" class="filter-box">

<input
type="text"
name="search"
placeholder="Search name, roll number, phone or email"
value="<?php echo htmlspecialchars($search); ?>"
>


<select name="course">

<option value="">All Courses</option>

<option value="BCA"
<?php echo ($course == 'BCA') ? 'selected' : ''; ?>>
BCA
</option>

<option value="BBA"
<?php echo ($course == 'BBA') ? 'selected' : ''; ?>>
BBA
</option>

<option value="BCom"
<?php echo ($course == 'BCom') ? 'selected' : ''; ?>>
BCom
</option>

</select>


<select name="semester">

<option value="">All Semesters</option>

<?php for ($i = 1; $i <= 6; $i++): ?>

<option
value="<?php echo $i; ?>"
<?php echo ($semester == $i) ? 'selected' : ''; ?>
>
Semester <?php echo $i; ?>
</option>

<?php endfor; ?>

</select>


<button type="submit" class="edit-btn">
🔍 Search
</button>


<a href="students.php" class="clear-btn">
Clear
</a>

</form>

</div>


<!-- Student Table -->

<div class="dashboard-section">

<div class="table-container">

<table>

<thead>

<tr>

<th>Roll No</th>

<th>Name</th>

<th>Phone</th>

<th>Email</th>

<th>Course</th>

<th>Semester</th>

<th>Actions</th>

</tr>

</thead>


<tbody>

<?php if (mysqli_num_rows($result) > 0): ?>

<?php while ($row = mysqli_fetch_assoc($result)): ?>

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
<?php echo htmlspecialchars($row['phone']); ?>
</td>


<td>
<?php echo htmlspecialchars($row['email']); ?>
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

<a
href="edit_student.php?id=<?php echo $row['id']; ?>"
class="edit-btn"
>
Edit
</a>


<a
href="delete_student.php?id=<?php echo $row['id']; ?>"
class="delete-btn"
onclick="return confirm('Are you sure you want to delete this student?');"
>
Delete
</a>

</td>

</tr>

<?php endwhile; ?>


<?php else: ?>

<tr>

<td colspan="7" style="text-align:center;">

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