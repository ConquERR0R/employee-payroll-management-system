<?php

include "db.php";


// =====================================================
// CHECK EMPLOYEE ID
// =====================================================

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    die("Invalid Employee ID.");
}

$employee_id = (int) $_GET["id"];


// =====================================================
// GET EMPLOYEE
// =====================================================

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


// =====================================================
// GET DATE RANGE
// =====================================================

$cutoff_start = $_GET["start"] ?? "";
$cutoff_end   = $_GET["end"] ?? "";


// =====================================================
// SAVE ATTENDANCE
// =====================================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $cutoff_start =
        $_POST["cutoff_start"] ?? "";

    $cutoff_end =
        $_POST["cutoff_end"] ?? "";

    $action =
        $_POST["action"] ?? "save";


    if (
        empty($cutoff_start) ||
        empty($cutoff_end)
    ) {

        die("Start date and cutoff date are required.");
    }


    if (
        strtotime($cutoff_end) <
        strtotime($cutoff_start)
    ) {

        die("Invalid pay period.");
    }


    if (!isset($_POST["attendance"])) {

        die("No attendance data received.");
    }


    // =================================================
    // INSERT OR UPDATE
    // =================================================

    $save_stmt = mysqli_prepare(
        $conn,
        "
        INSERT INTO attendance
        (
            employee_id,
            work_date,
            status,
            regular_hours,
            ot_hours,
            rdot_hours,
            double_pay_hours,
            premium_30_hours,
            paid_leave_hours
        )

        VALUES
        (
            ?, ?, ?, ?, ?, ?, ?, ?, ?
        )

        ON DUPLICATE KEY UPDATE

            status =
                VALUES(status),

            regular_hours =
                VALUES(regular_hours),

            ot_hours =
                VALUES(ot_hours),

            rdot_hours =
                VALUES(rdot_hours),

            double_pay_hours =
                VALUES(double_pay_hours),

            premium_30_hours =
                VALUES(premium_30_hours),

            paid_leave_hours =
                VALUES(paid_leave_hours)
        "
    );


    if (!$save_stmt) {

        die(
            "Prepare Error: "
            . mysqli_error($conn)
        );
    }


    // =================================================
    // SAVE EVERY DATE
    // =================================================

    foreach (
        $_POST["attendance"]
        as $work_date => $data
    ) {


        $status =
            $data["status"] ?? "Regular";


        $regular_hours =
            max(
                0,
                (float)
                ($data["regular_hours"] ?? 0)
            );


        $ot_hours =
            max(
                0,
                (float)
                ($data["ot_hours"] ?? 0)
            );


        $rdot_hours =
            max(
                0,
                (float)
                ($data["rdot_hours"] ?? 0)
            );


        $double_pay_hours =
            max(
                0,
                (float)
                ($data["double_pay_hours"] ?? 0)
            );


        $premium_30_hours =
            max(
                0,
                (float)
                ($data["premium_30_hours"] ?? 0)
            );


        $paid_leave_hours = 0;


        // =================================================
        // STATUS RULES
        // =================================================


        // -------------------------
        // REGULAR
        // -------------------------

        if ($status === "Regular") {

            $rdot_hours = 0;

            $double_pay_hours = 0;

            $premium_30_hours = 0;

            $paid_leave_hours = 0;

        }


        // -------------------------
        // DAY OFF
        // -------------------------

        elseif ($status === "Day Off") {

            $regular_hours = 0;

            $ot_hours = 0;

            $rdot_hours = 0;

            $double_pay_hours = 0;

            $premium_30_hours = 0;

            $paid_leave_hours = 0;

        }


        // -------------------------
        // RDOT
        // -------------------------

        elseif ($status === "RDOT") {

            $regular_hours = 0;

            $ot_hours = 0;

            $double_pay_hours = 0;

            $premium_30_hours = 0;

            $paid_leave_hours = 0;

        }


        // -------------------------
        // DOUBLE PAY
        // -------------------------

        elseif ($status === "Double Pay") {

            $regular_hours = 0;

            $ot_hours = 0;

            $rdot_hours = 0;

            $premium_30_hours = 0;

            $paid_leave_hours = 0;

        }


        // -------------------------
        // 30% PREMIUM
        // -------------------------

        elseif ($status === "30 Percent") {

            $regular_hours = 0;

            $ot_hours = 0;

            $rdot_hours = 0;

            $double_pay_hours = 0;

            $paid_leave_hours = 0;

        }


        // -------------------------
        // LEAVE WITH PAY
        // -------------------------

        elseif ($status === "Leave with Pay") {

            $regular_hours = 0;

            $ot_hours = 0;

            $rdot_hours = 0;

            $double_pay_hours = 0;

            $premium_30_hours = 0;

            $paid_leave_hours = 8;

        }


        // -------------------------
        // LEAVE WITHOUT PAY
        // -------------------------

        elseif (
            $status === "Leave without Pay"
        ) {

            $regular_hours = 0;

            $ot_hours = 0;

            $rdot_hours = 0;

            $double_pay_hours = 0;

            $premium_30_hours = 0;

            $paid_leave_hours = 0;

        }


        // -------------------------
        // ABSENT
        // -------------------------

        elseif ($status === "Absent") {

            $regular_hours = 0;

            $ot_hours = 0;

            $rdot_hours = 0;

            $double_pay_hours = 0;

            $premium_30_hours = 0;

            $paid_leave_hours = 0;

        }


        // =================================================
        // SAVE
        // =================================================

        mysqli_stmt_bind_param(
            $save_stmt,
            "issdddddd",

            $employee_id,
            $work_date,
            $status,

            $regular_hours,
            $ot_hours,
            $rdot_hours,

            $double_pay_hours,
            $premium_30_hours,

            $paid_leave_hours
        );


        if (!mysqli_stmt_execute($save_stmt)) {

            die(
                "Save Error on "
                . htmlspecialchars($work_date)
                . ": "
                . mysqli_stmt_error($save_stmt)
            );
        }

    }


    mysqli_stmt_close($save_stmt);


    // =================================================
    // SAVE THEN GENERATE PAYSLIP
    // =================================================

    if (
        $action ===
        "save_and_payslip"
    ) {

        header(
            "Location: payslip.php?id="
            . $employee_id
            . "&start="
            . urlencode($cutoff_start)
            . "&end="
            . urlencode($cutoff_end)
        );

        exit();
    }


    // =================================================
    // SAVE ONLY
    // =================================================

    header(
        "Location: attendance.php?id="
        . $employee_id
        . "&start="
        . urlencode($cutoff_start)
        . "&end="
        . urlencode($cutoff_end)
        . "&saved=1"
    );

    exit();
}


// =====================================================
// LOAD SAVED ATTENDANCE
// =====================================================

$saved_records = [];


if (
    !empty($cutoff_start) &&
    !empty($cutoff_end)
) {

    $stmt = mysqli_prepare(
        $conn,
        "
        SELECT *

        FROM attendance

        WHERE employee_id = ?

        AND work_date
        BETWEEN ? AND ?

        ORDER BY work_date ASC
        "
    );


    mysqli_stmt_bind_param(
        $stmt,
        "iss",
        $employee_id,
        $cutoff_start,
        $cutoff_end
    );


    mysqli_stmt_execute($stmt);


    $records =
        mysqli_stmt_get_result($stmt);


    while (
        $record =
        mysqli_fetch_assoc($records)
    ) {

        $saved_records[
            $record["work_date"]
        ] = $record;
    }
}


// =====================================================
// TOTALS
// =====================================================

$total_regular = 0;

$total_ot = 0;

$total_rdot = 0;

$total_double = 0;

$total_premium30 = 0;

$total_paid_leave = 0;

$total_man_hours = 0;

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
Employee Attendance
</title>


<style>

* {
    box-sizing: border-box;
}


body {

    margin: 0;

    background: #f4f6f9;

    font-family:
        Arial,
        Helvetica,
        sans-serif;

}


.container {

    width: 98%;

    max-width: 1450px;

    margin: 30px auto;

    padding: 30px;

    background: white;

    border-radius: 10px;

    box-shadow:
        0 2px 10px
        rgba(0,0,0,.08);

}


.employee-info,
.cutoff-box,
.summary {

    padding: 20px;

    margin-bottom: 25px;

    background: #f8f9fa;

    border-radius: 8px;

}


.cutoff-form {

    display: flex;

    align-items: end;

    gap: 15px;

    flex-wrap: wrap;

}


.field {

    display: flex;

    flex-direction: column;

    gap: 5px;

}


.field label {

    font-weight: bold;

}


input,
select {

    padding: 9px;

    border:
        1px solid #bbb;

    border-radius: 5px;

}


.table-wrapper {

    overflow-x: auto;

}


table {

    width: 100%;

    border-collapse: collapse;

    min-width: 1250px;

}


th {

    padding: 12px 8px;

    background: #212529;

    color: white;

    font-size: 14px;

}


td {

    padding: 8px;

    text-align: center;

    border-bottom:
        1px solid #ddd;

}


.status-select {

    min-width: 170px;

}


.hours-input {

    width: 105px;

}


.daily-total {

    font-weight: bold;

}


.summary-row {

    display: flex;

    justify-content:
        space-between;

    padding: 10px;

    border-bottom:
        1px solid #ddd;

}


.total-row {

    margin-top: 10px;

    border-top:
        2px solid #222;

    font-size: 20px;

    font-weight: bold;

}


.buttons {

    display: flex;

    flex-wrap: wrap;

    gap: 10px;

    margin-top: 25px;

}


.btn {

    padding: 12px 18px;

    border: none;

    border-radius: 5px;

    text-decoration: none;

    cursor: pointer;

    font-size: 15px;

}


.green {

    background: #198754;

    color: white;

}


.blue {

    background: #0d6efd;

    color: white;

}


.gray {

    background: #6c757d;

    color: white;

}


.success {

    padding: 15px;

    margin-bottom: 20px;

    background: #d1e7dd;

    color: #0f5132;

    border-radius: 5px;

}


.note {

    padding: 15px;

    margin-bottom: 20px;

    background: #fff3cd;

    border-radius: 5px;

}

</style>

</head>


<body>


<div class="container">


<h1>
Employee Daily Man Hours
</h1>


<div class="employee-info">


<h2>

<?php

echo htmlspecialchars(
    $employee["name"]
);

?>

</h2>


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
    (float)$employee["hourly_rate"],
    2
);

?> / hour

</p>


</div>



<?php if (isset($_GET["saved"])): ?>


<div class="success">

Attendance successfully saved.

</div>


<?php endif; ?>



<!-- ==================================================
PAY PERIOD
================================================== -->


<div class="cutoff-box">


<h3>
Select Pay Period
</h3>


<form
    method="GET"
    class="cutoff-form"
>


<input
    type="hidden"
    name="id"
    value="<?php echo $employee_id; ?>"
>


<div class="field">


<label>
Start Date
</label>


<input
    type="date"
    name="start"
    value="<?php
        echo htmlspecialchars(
            $cutoff_start
        );
    ?>"
    required
>


</div>


<div class="field">


<label>
Cutoff Date
</label>


<input
    type="date"
    name="end"
    value="<?php
        echo htmlspecialchars(
            $cutoff_end
        );
    ?>"
    required
>


</div>


<button
    type="submit"
    class="btn blue"
>

Load Daily Table

</button>


</form>


</div>



<?php

if (
    !empty($cutoff_start) &&
    !empty($cutoff_end)
):


$start =
    new DateTime(
        $cutoff_start
    );


$end =
    new DateTime(
        $cutoff_end
    );


if ($end < $start) {

    die(
        "Invalid pay period."
    );
}


$end_inclusive =
    clone $end;


$end_inclusive->modify(
    "+1 day"
);


$period =
    new DatePeriod(
        $start,
        new DateInterval("P1D"),
        $end_inclusive
    );

?>


<div class="note">

<strong>Default:</strong>

Monday–Saturday = 8 Regular Hours.

Sunday = Day Off.

Everything is editable.

</div>



<form method="POST">


<input
    type="hidden"
    name="cutoff_start"
    value="<?php
        echo htmlspecialchars(
            $cutoff_start
        );
    ?>"
>


<input
    type="hidden"
    name="cutoff_end"
    value="<?php
        echo htmlspecialchars(
            $cutoff_end
        );
    ?>"
>



<div class="table-wrapper">


<table>


<thead>


<tr>

<th>Date</th>

<th>Day</th>

<th>Status</th>

<th>Regular Hours</th>

<th>OT Hours</th>

<th>RDOT Hours</th>

<th>Double Pay Hours</th>

<th>30% Hours</th>

<th>Paid Leave</th>

<th>Daily Man Hours</th>

</tr>


</thead>


<tbody>


<?php foreach ($period as $date): ?>


<?php


$work_date =
    $date->format("Y-m-d");


$day =
    $date->format("D");


$is_sunday =
    $date->format("N") == 7;


// =====================================================
// SAVED RECORD
// =====================================================

if (
    isset(
        $saved_records[$work_date]
    )
) {


    $record =
        $saved_records[$work_date];


    $status =
        $record["status"];


    $regular =
        (float)
        $record["regular_hours"];


    $ot =
        (float)
        $record["ot_hours"];


    $rdot =
        (float)
        $record["rdot_hours"];


    $double =
        (float)
        $record["double_pay_hours"];


    $premium30 =
        (float)
        $record["premium_30_hours"];


    $paid_leave =
        (float)
        $record["paid_leave_hours"];

}


// =====================================================
// DEFAULT NEW RECORD
// =====================================================

else {


    if ($is_sunday) {

        $status =
            "Day Off";

        $regular = 0;
    }

    else {

        $status =
            "Regular";

        $regular = 8;
    }


    $ot = 0;

    $rdot = 0;

    $double = 0;

    $premium30 = 0;

    $paid_leave = 0;

}


// =====================================================
// DAILY MAN HOURS
// =====================================================

$daily_total =
    $regular
    + $ot
    + $rdot
    + $double
    + $premium30;


// =====================================================
// TOTALS
// =====================================================

$total_regular +=
    $regular;


$total_ot +=
    $ot;


$total_rdot +=
    $rdot;


$total_double +=
    $double;


$total_premium30 +=
    $premium30;


$total_paid_leave +=
    $paid_leave;


$total_man_hours +=
    $daily_total;

?>


<tr class="work-row">


<!-- DATE -->


<td>

<?php

echo $date->format(
    "M d, Y"
);

?>

</td>



<!-- DAY -->


<td>

<?php echo $day; ?>

</td>



<!-- STATUS -->


<td>


<select

    name="attendance[<?php
        echo $work_date;
    ?>][status]"

    class="status-select"

>


<option
    value="Regular"

    <?php
    echo $status === "Regular"
        ? "selected"
        : "";
    ?>
>

Regular

</option>



<option
    value="Day Off"

    <?php
    echo $status === "Day Off"
        ? "selected"
        : "";
    ?>
>

Day Off

</option>



<option
    value="RDOT"

    <?php
    echo $status === "RDOT"
        ? "selected"
        : "";
    ?>
>

RDOT

</option>



<option
    value="Double Pay"

    <?php
    echo $status === "Double Pay"
        ? "selected"
        : "";
    ?>
>

Double Pay

</option>



<option
    value="30 Percent"

    <?php
    echo $status === "30 Percent"
        ? "selected"
        : "";
    ?>
>

30% Premium

</option>



<option
    value="Leave with Pay"

    <?php
    echo $status === "Leave with Pay"
        ? "selected"
        : "";
    ?>
>

Leave with Pay

</option>



<option
    value="Leave without Pay"

    <?php
    echo $status === "Leave without Pay"
        ? "selected"
        : "";
    ?>
>

Leave without Pay

</option>



<option
    value="Absent"

    <?php
    echo $status === "Absent"
        ? "selected"
        : "";
    ?>
>

Absent

</option>


</select>


</td>



<!-- REGULAR -->


<td>


<input

    type="number"

    name="attendance[<?php
        echo $work_date;
    ?>][regular_hours]"

    class="
        hours-input
        regular-hours
    "

    value="<?php
        echo number_format(
            $regular,
            2,
            ".",
            ""
        );
    ?>"

    min="0"

    step="0.25"

>


</td>



<!-- OT -->


<td>


<input

    type="number"

    name="attendance[<?php
        echo $work_date;
    ?>][ot_hours]"

    class="
        hours-input
        ot-hours
    "

    value="<?php
        echo number_format(
            $ot,
            2,
            ".",
            ""
        );
    ?>"

    min="0"

    step="0.25"

>


</td>



<!-- RDOT -->


<td>


<input

    type="number"

    name="attendance[<?php
        echo $work_date;
    ?>][rdot_hours]"

    class="
        hours-input
        rdot-hours
    "

    value="<?php
        echo number_format(
            $rdot,
            2,
            ".",
            ""
        );
    ?>"

    min="0"

    step="0.25"

>


</td>



<!-- DOUBLE PAY -->


<td>


<input

    type="number"

    name="attendance[<?php
        echo $work_date;
    ?>][double_pay_hours]"

    class="
        hours-input
        double-hours
    "

    value="<?php
        echo number_format(
            $double,
            2,
            ".",
            ""
        );
    ?>"

    min="0"

    step="0.25"

>


</td>



<!-- 30% -->


<td>


<input

    type="number"

    name="attendance[<?php
        echo $work_date;
    ?>][premium_30_hours]"

    class="
        hours-input
        premium30-hours
    "

    value="<?php
        echo number_format(
            $premium30,
            2,
            ".",
            ""
        );
    ?>"

    min="0"

    step="0.25"

>


</td>



<!-- PAID LEAVE -->


<td>


<span class="paid-leave">

<?php

echo number_format(
    $paid_leave,
    2
);

?>

</span>


</td>



<!-- DAILY TOTAL -->


<td>


<strong class="daily-total">

<?php

echo number_format(
    $daily_total,
    2
);

?>

</strong>


</td>


</tr>


<?php endforeach; ?>


</tbody>


</table>


</div>



<!-- ==================================================
SUMMARY
================================================== -->


<div class="summary">


<h2>
Man Hours Summary
</h2>



<div class="summary-row">

<span>
Regular Hours
</span>

<strong id="totalRegular">

<?php
echo number_format(
    $total_regular,
    2
);
?>

</strong>

</div>



<div class="summary-row">

<span>
OT Hours
</span>

<strong id="totalOT">

<?php
echo number_format(
    $total_ot,
    2
);
?>

</strong>

</div>



<div class="summary-row">

<span>
RDOT Hours
</span>

<strong id="totalRDOT">

<?php
echo number_format(
    $total_rdot,
    2
);
?>

</strong>

</div>



<div class="summary-row">

<span>
Double Pay Hours
</span>

<strong id="totalDouble">

<?php
echo number_format(
    $total_double,
    2
);
?>

</strong>

</div>



<div class="summary-row">

<span>
30% Premium Hours
</span>

<strong id="totalPremium30">

<?php
echo number_format(
    $total_premium30,
    2
);
?>

</strong>

</div>



<div class="summary-row">

<span>
Paid Leave Hours
</span>

<strong id="totalPaidLeave">

<?php
echo number_format(
    $total_paid_leave,
    2
);
?>

</strong>

</div>



<div class="
    summary-row
    total-row
">


<span>
TOTAL MAN HOURS
</span>


<span>

<strong id="totalManHours">

<?php

echo number_format(
    $total_man_hours,
    2
);

?>

</strong>

Hours

</span>


</div>


</div>



<!-- BUTTONS -->


<div class="buttons">


<button

    type="submit"

    name="action"

    value="save"

    class="btn green"

>

Save All Man Hours

</button>



<button

    type="submit"

    name="action"

    value="save_and_payslip"

    class="btn blue"

>

Save & Generate Payslip

</button>



<a
    href="employees.php"
    class="btn gray"
>

Back to Employees

</a>


</div>


</form>


<?php endif; ?>


</div>



<script>


// =====================================================
// GET ROWS
// =====================================================

const rows =
    document.querySelectorAll(
        ".work-row"
    );


// =====================================================
// UPDATE ROW
// =====================================================

function updateRow(
    row,
    statusChanged
) {


    const status =
        row.querySelector(
            ".status-select"
        );


    const regular =
        row.querySelector(
            ".regular-hours"
        );


    const ot =
        row.querySelector(
            ".ot-hours"
        );


    const rdot =
        row.querySelector(
            ".rdot-hours"
        );


    const doubleHours =
        row.querySelector(
            ".double-hours"
        );


    const premium30 =
        row.querySelector(
            ".premium30-hours"
        );


    const paidLeave =
        row.querySelector(
            ".paid-leave"
        );


    // =================================================
    // STATUS CHANGE
    // =================================================

    if (statusChanged) {


        // Reset everything first

        regular.value =
            "0.00";


        ot.value =
            "0.00";


        rdot.value =
            "0.00";


        doubleHours.value =
            "0.00";


        premium30.value =
            "0.00";


        paidLeave.textContent =
            "0.00";


        // =============================================
        // REGULAR
        // =============================================

        if (
            status.value ===
            "Regular"
        ) {

            regular.value =
                "8.00";

        }


        // =============================================
        // RDOT
        // =============================================

        else if (
            status.value ===
            "RDOT"
        ) {

            rdot.value =
                "8.00";

        }


        // =============================================
        // DOUBLE PAY
        // =============================================

        else if (
            status.value ===
            "Double Pay"
        ) {

            doubleHours.value =
                "8.00";

        }


        // =============================================
        // 30% PREMIUM
        // =============================================

        else if (
            status.value ===
            "30 Percent"
        ) {

            premium30.value =
                "8.00";

        }


        // =============================================
        // LEAVE WITH PAY
        // =============================================

        else if (
            status.value ===
            "Leave with Pay"
        ) {

            paidLeave.textContent =
                "8.00";

        }

    }


    // =================================================
    // CALCULATE DAILY TOTAL
    // =================================================

    const regularValue =
        parseFloat(
            regular.value
        ) || 0;


    const otValue =
        parseFloat(
            ot.value
        ) || 0;


    const rdotValue =
        parseFloat(
            rdot.value
        ) || 0;


    const doubleValue =
        parseFloat(
            doubleHours.value
        ) || 0;


    const premiumValue =
        parseFloat(
            premium30.value
        ) || 0;


    const dailyTotal =
        regularValue
        + otValue
        + rdotValue
        + doubleValue
        + premiumValue;


    row.querySelector(
        ".daily-total"
    ).textContent =
        dailyTotal.toFixed(2);


    calculateTotals();

}


// =====================================================
// CALCULATE TOTALS
// =====================================================

function calculateTotals() {


    let regular = 0;

    let ot = 0;

    let rdot = 0;

    let doubleHours = 0;

    let premium30 = 0;

    let paidLeave = 0;


    rows.forEach(
        function(row) {


            regular +=
                parseFloat(
                    row.querySelector(
                        ".regular-hours"
                    ).value
                ) || 0;


            ot +=
                parseFloat(
                    row.querySelector(
                        ".ot-hours"
                    ).value
                ) || 0;


            rdot +=
                parseFloat(
                    row.querySelector(
                        ".rdot-hours"
                    ).value
                ) || 0;


            doubleHours +=
                parseFloat(
                    row.querySelector(
                        ".double-hours"
                    ).value
                ) || 0;


            premium30 +=
                parseFloat(
                    row.querySelector(
                        ".premium30-hours"
                    ).value
                ) || 0;


            paidLeave +=
                parseFloat(
                    row.querySelector(
                        ".paid-leave"
                    ).textContent
                ) || 0;

        }
    );


    const total =
        regular
        + ot
        + rdot
        + doubleHours
        + premium30;


    document.getElementById(
        "totalRegular"
    ).textContent =
        regular.toFixed(2);


    document.getElementById(
        "totalOT"
    ).textContent =
        ot.toFixed(2);


    document.getElementById(
        "totalRDOT"
    ).textContent =
        rdot.toFixed(2);


    document.getElementById(
        "totalDouble"
    ).textContent =
        doubleHours.toFixed(2);


    document.getElementById(
        "totalPremium30"
    ).textContent =
        premium30.toFixed(2);


    document.getElementById(
        "totalPaidLeave"
    ).textContent =
        paidLeave.toFixed(2);


    document.getElementById(
        "totalManHours"
    ).textContent =
        total.toFixed(2);

}


// =====================================================
// EVENTS
// =====================================================

rows.forEach(
    function(row) {


        const status =
            row.querySelector(
                ".status-select"
            );


        status.addEventListener(
            "change",
            function() {

                updateRow(
                    row,
                    true
                );

            }
        );


        row.querySelectorAll(
            ".hours-input"
        ).forEach(
            function(input) {


                input.addEventListener(
                    "input",
                    function() {

                        updateRow(
                            row,
                            false
                        );

                    }
                );

            }
        );

    }
);


// INITIAL CALCULATION

calculateTotals();


</script>


</body>

</html>