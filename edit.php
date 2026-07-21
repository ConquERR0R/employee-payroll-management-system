<?php

include "db.php";


// ==========================================
// CHECK EMPLOYEE ID
// ==========================================

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {

    die("Invalid employee ID.");

}

$id = (int) $_GET["id"];


// ==========================================
// GET EMPLOYEE INFORMATION
// ==========================================

$stmt = mysqli_prepare(
    $conn,
    "SELECT * FROM employees WHERE id = ?"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$employee = mysqli_fetch_assoc($result);


// Check if employee exists
if (!$employee) {

    die("Employee not found.");

}


// ==========================================
// UPDATE EMPLOYEE
// ==========================================

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get form values
    $name = trim($_POST["name"]);

    $email = trim($_POST["email"]);

    $position = trim($_POST["position"]);

    $hourly_rate = (float) $_POST["hourly_rate"];


    // ======================================
    // VALIDATION
    // ======================================

    if (
        empty($name) ||
        empty($email) ||
        empty($position)
    ) {

        $error = "Please fill in all fields.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    } elseif ($hourly_rate < 0) {

        $error = "Hourly rate cannot be negative.";

    } else {


        // ==================================
        // PREPARE UPDATE QUERY
        // ==================================

        $stmt = mysqli_prepare(
            $conn,

            "UPDATE employees

             SET name = ?,
                 email = ?,
                 position = ?,
                 hourly_rate = ?

             WHERE id = ?"
        );


        // ==================================
        // BIND VALUES
        // ==================================

        mysqli_stmt_bind_param(
            $stmt,
            "sssdi",
            $name,
            $email,
            $position,
            $hourly_rate,
            $id
        );


        // ==================================
        // EXECUTE UPDATE
        // ==================================

        if (mysqli_stmt_execute($stmt)) {

            header("Location: index.php");

            exit();

        } else {

            $error = "Failed to update employee.";

        }

    }

}

?>


<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Edit Employee</title>

    <link
        rel="stylesheet"
        href="style.css"
    >

</head>


<body>


<div class="form-container">


    <h2>
        Edit Employee
    </h2>


    <!-- ERROR MESSAGE -->

    <?php if (isset($error)): ?>

        <p class="error">

            <?php
            echo htmlspecialchars($error);
            ?>

        </p>

    <?php endif; ?>


    <form method="POST">


        <!-- ==============================
        EMPLOYEE NAME
        =============================== -->

        <label>
            Employee Name
        </label>

        <input
            type="text"
            name="name"

            value="<?php
                echo htmlspecialchars(
                    $employee["name"]
                );
            ?>"

            required
        >



        <!-- ==============================
        EMAIL ADDRESS
        =============================== -->

        <label>
            Email Address
        </label>

        <input
            type="email"
            name="email"

            value="<?php
                echo htmlspecialchars(
                    $employee["email"]
                );
            ?>"

            required
        >



        <!-- ==============================
        POSITION
        =============================== -->

        <label>
            Position
        </label>

        <input
            type="text"
            name="position"

            value="<?php
                echo htmlspecialchars(
                    $employee["position"]
                );
            ?>"

            required
        >



        <!-- ==============================
        HOURLY RATE
        =============================== -->

        <label>
            Hourly Rate
        </label>

        <input
            type="number"
            name="hourly_rate"

            value="<?php
                echo htmlspecialchars(
                    $employee["hourly_rate"]
                );
            ?>"

            step="0.01"
            min="0"
            required
        >



        <!-- ==============================
        UPDATE BUTTON
        =============================== -->

        <button
            type="submit"
            class="btn save"
        >

            Update Employee

        </button>



        <!-- ==============================
        CANCEL BUTTON
        =============================== -->

        <a
            href="index.php"
            class="btn cancel"
        >

            Cancel

        </a>


    </form>


</div>


</body>

</html>