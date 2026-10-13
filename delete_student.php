<?php

session_start();

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

include "db.php";

if (isset($_GET['id'])) {

    $id = (int) $_GET['id'];

    mysqli_query(
        $conn,
        "DELETE FROM attendance WHERE student_id = $id"
    );

    $stmt = mysqli_prepare(
        $conn,
        "DELETE FROM students WHERE id = ?"
    );

    mysqli_stmt_bind_param($stmt, "i", $id);

    mysqli_stmt_execute($stmt);
}

header("Location: students.php");
exit();

?>