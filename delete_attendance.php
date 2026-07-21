<?php

include "db.php";


if (
    isset($_GET["id"]) &&
    isset($_GET["employee_id"])
) {


    $id =
        (int) $_GET["id"];


    $employee_id =
        (int) $_GET["employee_id"];


    $stmt = mysqli_prepare(
        $conn,
        "DELETE FROM attendance
         WHERE id = ?
         AND employee_id = ?"
    );


    mysqli_stmt_bind_param(
        $stmt,
        "ii",
        $id,
        $employee_id
    );


    mysqli_stmt_execute($stmt);


    header(
        "Location: attendance.php?id="
        . $employee_id
    );


    exit();

}


header("Location: index.php");

exit();

?>