<?php

session_start();

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

include "db.php";


header("Content-Type: text/csv; charset=utf-8");

header(
    "Content-Disposition: attachment; filename=attendance_report.csv"
);


/* Open output */

$output = fopen("php://output", "w");


/* CSV heading */

fputcsv($output, [
    "Roll No",
    "Name",
    "Course",
    "Semester",
    "Total Days",
    "Present Days",
    "Absent Days",
    "Attendance Percentage"
]);


/* Get report */

$sql = "
SELECT
    students.roll_no,
    students.name,
    students.course,
    students.semester,

    COUNT(attendance.id) AS total_days,

    COALESCE(
        SUM(
            CASE
                WHEN attendance.status = 'Present'
                THEN 1
                ELSE 0
            END
        ),
        0
    ) AS present_days,

    COALESCE(
        SUM(
            CASE
                WHEN attendance.status = 'Absent'
                THEN 1
                ELSE 0
            END
        ),
        0
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


while ($row = mysqli_fetch_assoc($result)) {

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


    fputcsv($output, [

        $row['roll_no'],

        $row['name'],

        $row['course'],

        $row['semester'],

        $total_days,

        $present_days,

        $absent_days,

        $percentage . "%"

    ]);
}


fclose($output);

exit();

?>