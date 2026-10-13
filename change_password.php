<?php

session_start();

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

include "db.php";

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {

        $error = "New passwords do not match.";

    } elseif (strlen($new_password) < 6) {

        $error = "New password must be at least 6 characters.";

    } else {

        $username = $_SESSION['username'];

        $stmt = mysqli_prepare(
            $conn,
            "SELECT password FROM users WHERE username = ?"
        );

        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        if ($user && password_verify($current_password, $user['password'])) {

            $new_hash = password_hash(
                $new_password,
                PASSWORD_DEFAULT
            );

            $stmt = mysqli_prepare(
                $conn,
                "UPDATE users SET password = ? WHERE username = ?"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "ss",
                $new_hash,
                $username
            );

            if (mysqli_stmt_execute($stmt)) {

                $message = "Password changed successfully.";

            } else {

                $error = "Failed to change password.";

            }

        } else {

            $error = "Current password is incorrect.";

        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Change Password</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<header>

    <h1>Student Attendance Management System</h1>

    <p>
        Welcome,
        <?php echo htmlspecialchars($_SESSION['username']); ?>
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

    <div class="dashboard-section">

        <h2>Change Password</h2>

        <?php if ($message != ""): ?>

            <div class="success">
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php endif; ?>


        <?php if ($error != ""): ?>

            <div class="error">
                <?php echo htmlspecialchars($error); ?>
            </div>

        <?php endif; ?>


        <form method="POST">

            <label>Current Password</label>

            <input
                type="password"
                name="current_password"
                required
            >


            <label>New Password</label>

            <input
                type="password"
                name="new_password"
                required
            >


            <label>Confirm New Password</label>

            <input
                type="password"
                name="confirm_password"
                required
            >


            <button type="submit" class="edit-btn">
                Change Password
            </button>

        </form>

    </div>

</main>

</body>

</html>