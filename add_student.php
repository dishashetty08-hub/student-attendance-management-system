<?php
session_start();
if (!isset($_SESSION['username'])) 
    {
    header("Location: index.php");
    exit();
    }
include "db.php";
$message = "";
if (isset($_POST['add_student'])) 
    {
    $roll_no = trim($_POST['roll_no']);
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $course = trim($_POST['course']);
    $semester = (int) $_POST['semester'];
    $stmt = mysqli_prepare($conn,"INSERT INTO students(roll_no, name, phone, email, course, semester)VALUES (?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt,"sssssi",$roll_no,$name,$phone,$email,$course,$semester);

    if (mysqli_stmt_execute($stmt)) 
        {
        header("Location: students.php");
        exit();
        }
        else 
            {
                $message = "Unable to add student. Roll number may already exist.";
            }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h1>Student Attendance Management System</h1>
<nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="students.php">Students</a>
    <a href="attendance.php">Attendance</a>
    <a href="report.php">Reports</a>
    <a href="logout.php">Logout</a>
</nav>
<div class="container">
    <h2>Add New Student</h2>
    <?php if ($message != "") 
        { ?>
        <p class="error">
            <?php echo htmlspecialchars($message); ?>
        </p>

    <?php } ?>
    <form method="POST">
        <label>Roll Number</label>
        <input type="text" name="roll_no" required>

        <label>Student Name</label>
        <input type="text" name="name" required>

        <label>Phone</label>
        <input type="text" name="phone" maxlength="15">

        <label>Email</label>
        <input type="email" name="email">

        <label>Course</label>
<select name="course" required>
    <option value="">Select Course</option>
    <option value="BCA">BCA</option>
    <option value="BBA">BBA</option>
    <option value="BCom">BCom</option>
    <option value="BA">BA</option>
    <option value="BSc">BSc</option>
</select>

        <label>Semester</label>
        <select name="semester" required>
            <option value="">Select Semester</option>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
            <option value="5">5</option>
            <option value="6">6</option>
        </select>

        <br><br>

        <button type="submit" name="add_student">
            Add Student
        </button>

        <a href="students.php" class="clear-btn">
            Cancel
        </a>
    </form>

</div>

</body>
</html>