<?php
session_start();

if (!isset($_SESSION['username'])) 
    {
        header("Location: index.php");
        exit();
    }

include "db.php";
if (!isset($_GET['id'])) 
    {
        header("Location: students.php");
        exit();
    }

$id = (int) $_GET['id'];
$stmt = mysqli_prepare($conn,"SELECT * FROM students WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_assoc($result);

if (!$student)
    {
        header("Location: students.php");
        exit();
    }

$message = "";

if (isset($_POST['update_student']))
    {
        $roll_no = trim($_POST['roll_no']);
        $name = trim($_POST['name']);
        $phone = trim($_POST['phone']);
        $email = trim($_POST['email']);
        $course = trim($_POST['course']);
        $semester = (int) $_POST['semester'];

    $stmt = mysqli_prepare($conn,"UPDATE studentsSET roll_no = ?,name = ?,phone = ?,email = ?,course = ?,semester = ? WHERE id = ?");

    mysqli_stmt_bind_param($stmt,"sssssii",$roll_no,$name,$phone,$email,$course,$semester,$id);

    if (mysqli_stmt_execute($stmt))
        {
            header("Location: students.php");
            exit();
        }
        else
            {
                $message = "Unable to update student.";
            }
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Student</title>
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
    <h2>Edit Student</h2>
    <?php if ($message != "") { ?>

        <p class="error">
            <?php echo htmlspecialchars($message); ?>
        </p>

    <?php } ?>

    <form method="POST">
        <label>Roll Number</label>
        <input type="text" name="roll_no" value="<?php echo htmlspecialchars($student['roll_no']); ?>" required>

        <label>Student Name</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($student['name']); ?>" required>

        <label>Phone</label>
        <input type="text" name="phone" value="<?php echo htmlspecialchars($student['phone'] ?? ''); ?>">

        <label>Email</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($student['email'] ?? ''); ?>">

        <label>Course</label>
<select name="course" required>
    <option value="">Select Course</option>

    <option value="BCA" <?php echo ($student['course'] == 'BCA') ? 'selected' : ''; ?>>
        BCA
    </option>

    <option value="BBA" <?php echo ($student['course'] == 'BBA') ? 'selected' : ''; ?>>
        BBA
    </option>

    <option value="BCom" <?php echo ($student['course'] == 'BCom') ? 'selected' : ''; ?>>
        BCom
    </option>

    <option value="BA" <?php echo ($student['course'] == 'BA') ? 'selected' : ''; ?>>
        BA
    </option>

    <option value="BSc" <?php echo ($student['course'] == 'BSc') ? 'selected' : ''; ?>>
        BSc
    </option>

</select>

        <label>Semester</label>
        <select name="semester" required>

            <?php for ($i = 1; $i <= 6; $i++) { ?>

                <option
                    value="<?php echo $i; ?>"
                    <?php
                    if ($student['semester'] == $i) {
                        echo "selected";
                    }
                    ?>
                >
                    <?php echo $i; ?>
                </option>

            <?php } ?>

        </select>

        <br><br>

        <button type="submit" name="update_student">
            Update Student
        </button>

        <a href="students.php" class="clear-btn">
            Cancel
        </a>

    </form>

</div>

</body>
</html>