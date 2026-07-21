<?php

// Connect database
include "db.php";


// Check if ID exists
if (isset($_GET["id"]) && is_numeric($_GET["id"])) {

    // Convert ID into integer
    $id = (int) $_GET["id"];


    // Prepare DELETE query
    $stmt = mysqli_prepare(
        $conn,
        "DELETE FROM employees WHERE id = ?"
    );


    // Bind employee ID
    mysqli_stmt_bind_param(
        $stmt,
        "i",
        $id
    );


    // Execute delete
    mysqli_stmt_execute($stmt);

}


// Return to employee list
header("Location: index.php");

exit();

?>