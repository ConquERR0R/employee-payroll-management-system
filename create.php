<?php

include "db.php";


// ==========================================
// ADD EMPLOYEE
// ==========================================

if ($_SERVER["REQUEST_METHOD"] == "POST") {


    // ======================================
    // GET FORM DATA
    // ======================================

    $name =
        trim($_POST["name"] ?? "");


    $email =
        trim($_POST["email"] ?? "");


    $position =
        trim($_POST["position"] ?? "");


    $hourly_rate =
        (float) ($_POST["hourly_rate"] ?? 0);


    // ======================================
    // VALIDATION
    // ======================================

    if (
        $name == "" ||
        $email == "" ||
        $position == ""
    ) {

        $error =
            "Please fill in all required fields.";

    }


    elseif (
        !filter_var(
            $email,
            FILTER_VALIDATE_EMAIL
        )
    ) {

        $error =
            "Please enter a valid email address.";

    }


    elseif ($hourly_rate <= 0) {

        $error =
            "Hourly rate must be greater than 0.";

    }


    else {


        // ==================================
        // INSERT EMPLOYEE
        // ==================================

        $stmt = mysqli_prepare(
            $conn,

            "INSERT INTO employees
            (
                name,
                email,
                position,
                hourly_rate
            )

            VALUES
            (
                ?,
                ?,
                ?,
                ?
            )"
        );


        mysqli_stmt_bind_param(
            $stmt,
            "sssd",

            $name,
            $email,
            $position,
            $hourly_rate
        );


        // ==================================
        // EXECUTE
        // ==================================

        if (mysqli_stmt_execute($stmt)) {


            header(
                "Location: index.php"
            );


            exit();


        }


        else {


            $error =
                "Failed to add employee: "
                . mysqli_error($conn);


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
        Add Employee
    </title>


    <link
        rel="stylesheet"
        href="style.css"
    >


</head>


<body>


<div class="form-container">


    <h2>
        Add Employee
    </h2>


    <!-- =====================================
    ERROR
    ====================================== -->


    <?php if (isset($error)): ?>


        <p class="error">


            <?php

            echo htmlspecialchars(
                $error
            );

            ?>


        </p>


    <?php endif; ?>



    <!-- =====================================
    FORM
    ====================================== -->


    <form method="POST">



        <!-- EMPLOYEE NAME -->


        <label>

            Employee Name

        </label>


        <input
            type="text"
            name="name"

            placeholder="Enter employee name"

            value="<?php
            echo isset($name)
                ? htmlspecialchars($name)
                : '';
            ?>"

            required
        >



        <!-- EMAIL -->


        <label>

            Email Address

        </label>


        <input
            type="email"
            name="email"

            placeholder="Enter email address"

            value="<?php
            echo isset($email)
                ? htmlspecialchars($email)
                : '';
            ?>"

            required
        >



        <!-- POSITION -->


        <label>

            Position

        </label>


        <input
            type="text"
            name="position"

            placeholder="Example: Technician"

            value="<?php
            echo isset($position)
                ? htmlspecialchars($position)
                : '';
            ?>"

            required
        >



        <!-- HOURLY RATE -->


        <label>

            Hourly Rate

        </label>


        <input
            type="number"
            name="hourly_rate"

            placeholder="Example: 104"

            step="0.01"

            min="0.01"

            value="<?php
            echo isset($hourly_rate) &&
                 $hourly_rate > 0
                ? htmlspecialchars($hourly_rate)
                : '';
            ?>"

            required
        >


        <small>

            Example: ₱104.00 per hour

        </small>


        <br><br>



        <!-- SAVE -->


        <button
            type="submit"
            class="btn save"
        >

            Add Employee

        </button>



        <!-- CANCEL -->


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