<?php

include "db.php";


// ==========================================
// CHECK EMPLOYEE ID
// ==========================================

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {

    die("Invalid Employee ID.");

}

$employee_id = (int) $_GET["id"];


// ==========================================
// GET EMPLOYEE
// ==========================================

$stmt = mysqli_prepare(
    $conn,
    "SELECT * FROM employees WHERE id = ?"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $employee_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$employee = mysqli_fetch_assoc($result);


if (!$employee) {

    die("Employee not found.");

}


// ==========================================
// SAVE WORK HOURS
// ==========================================

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $work_date =
        $_POST["work_date"] ?? "";

    $regular_hours =
        (float) ($_POST["regular_hours"] ?? 8);

    $ot_hours =
        (float) ($_POST["ot_hours"] ?? 0);


    // ======================================
    // VALIDATION
    // ======================================

    if ($work_date == "") {

        $error = "Please select a work date.";

    }

    elseif ($regular_hours < 0) {

        $error = "Regular hours cannot be negative.";

    }

    elseif ($ot_hours < 0) {

        $error = "OT hours cannot be negative.";

    }

    else {


        // ==================================
        // CHECK IF DATE ALREADY EXISTS
        // ==================================

        $check = mysqli_prepare(
            $conn,

            "SELECT id
             FROM attendance
             WHERE employee_id = ?
             AND work_date = ?"
        );


        mysqli_stmt_bind_param(
            $check,
            "is",
            $employee_id,
            $work_date
        );


        mysqli_stmt_execute($check);

        $check_result =
            mysqli_stmt_get_result($check);


        if (
            mysqli_num_rows($check_result) > 0
        ) {

            $error =
                "Work hours for this date already exist.";

        }

        else {


            // ==================================
            // INSERT WORK HOURS
            // ==================================

            $stmt = mysqli_prepare(
                $conn,

                "INSERT INTO attendance
                (
                    employee_id,
                    work_date,
                    time_in,
                    time_out,
                    regular_hours,
                    ot_hours
                )

                VALUES
                (
                    ?,
                    ?,
                    '00:00:00',
                    '00:00:00',
                    ?,
                    ?
                )"
            );


            mysqli_stmt_bind_param(
                $stmt,
                "isdd",
                $employee_id,
                $work_date,
                $regular_hours,
                $ot_hours
            );


            if (mysqli_stmt_execute($stmt)) {

                header(
                    "Location: attendance.php?id="
                    . $employee_id
                );

                exit();

            }

            else {

                $error =
                    "Failed to save work hours: "
                    . mysqli_error($conn);

            }

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

    <title>
        Add Work Hours
    </title>

    <link
        rel="stylesheet"
        href="style.css"
    >

</head>


<body>


<div class="form-container">


    <h2>
        Add Work Hours
    </h2>


    <h3>

        <?php
        echo htmlspecialchars(
            $employee["name"]
        );
        ?>

    </h3>


    <p>

        <strong>
            Position:
        </strong>

        <?php
        echo htmlspecialchars(
            $employee["position"]
        );
        ?>

    </p>


    <p>

        <strong>
            Hourly Rate:
        </strong>

        ₱<?php
        echo number_format(
            $employee["hourly_rate"],
            2
        );
        ?>/hr

    </p>


    <?php if (isset($error)): ?>

        <p class="error">

            <?php
            echo htmlspecialchars($error);
            ?>

        </p>

    <?php endif; ?>



    <form method="POST">


        <!-- WORK DATE -->

        <label>
            Work Date
        </label>

        <input
            type="date"
            name="work_date"
            value="<?php
                echo htmlspecialchars(
                    $_POST["work_date"] ?? ""
                );
            ?>"
            required
        >



        <!-- REGULAR HOURS -->

        <label>
            Regular Work Hours
        </label>

        <input
            type="number"
            name="regular_hours"
            value="<?php
                echo htmlspecialchars(
                    $_POST["regular_hours"] ?? "8"
                );
            ?>"
            min="0"
            max="24"
            step="0.25"
            required
        >

        <small>
            Default: 8 hours. You can edit this
            for half-day or shorter work.
        </small>



        <!-- OT HOURS -->

        <label>
            Overtime (OT) Hours
        </label>

        <input
            type="number"
            name="ot_hours"
            value="<?php
                echo htmlspecialchars(
                    $_POST["ot_hours"] ?? "0"
                );
            ?>"
            min="0"
            max="24"
            step="0.25"
            required
        >

        <small>
            Default: 0. Enter OT hours if applicable.
        </small>


        <br><br>


        <button
            type="submit"
            class="btn save"
        >
            Save Work Hours
        </button>


        <a
            href="attendance.php?id=<?php
            echo $employee_id;
            ?>"
            class="btn cancel"
        >
            Cancel
        </a>


    </form>


</div>


</body>

</html>