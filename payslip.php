<?php

include "db.php";


// =====================================================
// 1. CHECK EMPLOYEE ID
// =====================================================

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {

    die("Invalid Employee ID.");

}

$employee_id = (int) $_GET["id"];


// =====================================================
// 2. GET EMPLOYEE INFORMATION
// =====================================================

$stmt = mysqli_prepare(
    $conn,
    "SELECT * FROM employees WHERE id = ?"
);

if (!$stmt) {

    die(
        "Employee Query Error: "
        . mysqli_error($conn)
    );

}

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $employee_id
);

mysqli_stmt_execute($stmt);

$result =
    mysqli_stmt_get_result($stmt);

$employee =
    mysqli_fetch_assoc($result);


if (!$employee) {

    die("Employee not found.");

}


// =====================================================
// 3. GET PAY PERIOD
// =====================================================

$cutoff_start =
    $_GET["start"] ?? "";

$cutoff_end =
    $_GET["end"] ?? "";


if (
    empty($cutoff_start) ||
    empty($cutoff_end)
) {

    die(
        "Pay period is missing. "
        . "Please go back to Man Hours."
    );

}


if (
    strtotime($cutoff_end)
    <
    strtotime($cutoff_start)
) {

    die(
        "Cutoff date cannot be earlier "
        . "than the start date."
    );

}


// =====================================================
// 4. EMPLOYEE HOURLY RATE
// =====================================================
//
// IMPORTANT:
//
// WALAY FIXED RATE DIRI.
//
// Kung hourly rate sa employee = 64.75,
// mao na gamiton.
//
// Kung manager = 104,
// mao na gamiton.
//
// Kung imo usbon ngadto 120,
// automatic 120 ang gamiton.
//
// =====================================================

$hourly_rate =
    (float)
    ($employee["hourly_rate"] ?? 0);


// =====================================================
// 5. PAY RATES
// =====================================================


// REGULAR RATE = 100%
$regular_rate =
    $hourly_rate;


// OT RATE = 125%
$ot_rate =
    $hourly_rate * 1.25;


// RDOT RATE = 130%
$rdot_rate =
    $hourly_rate * 1.30;


// DOUBLE PAY = 200%
$double_pay_rate =
    $hourly_rate * 2.00;


// 30% PREMIUM = 130%
$premium_30_rate =
    $hourly_rate * 1.30;


// PAID LEAVE
$paid_leave_rate =
    $hourly_rate;


// =====================================================
// 6. GET ATTENDANCE TOTALS
// =====================================================

$sql = "

    SELECT

        COUNT(*) AS attendance_days,

        COALESCE(
            SUM(regular_hours),
            0
        ) AS regular_total,

        COALESCE(
            SUM(ot_hours),
            0
        ) AS ot_total,

        COALESCE(
            SUM(rdot_hours),
            0
        ) AS rdot_total,

        COALESCE(
            SUM(double_pay_hours),
            0
        ) AS double_pay_total,

        COALESCE(
            SUM(premium_30_hours),
            0
        ) AS premium_30_total,

        COALESCE(
            SUM(paid_leave_hours),
            0
        ) AS paid_leave_total

    FROM attendance

    WHERE employee_id = ?

    AND work_date >= ?

    AND work_date <= ?

";


$stmt =
    mysqli_prepare(
        $conn,
        $sql
    );


if (!$stmt) {

    die(
        "Attendance Query Error: "
        . mysqli_error($conn)
    );

}


mysqli_stmt_bind_param(
    $stmt,
    "iss",
    $employee_id,
    $cutoff_start,
    $cutoff_end
);


mysqli_stmt_execute($stmt);


$hours_result =
    mysqli_stmt_get_result($stmt);


$hours =
    mysqli_fetch_assoc(
        $hours_result
    );


// =====================================================
// 7. GET TOTAL HOURS
// =====================================================

$attendance_days =
    (int)
    ($hours["attendance_days"] ?? 0);


$regular_hours =
    (float)
    ($hours["regular_total"] ?? 0);


$ot_hours =
    (float)
    ($hours["ot_total"] ?? 0);


$rdot_hours =
    (float)
    ($hours["rdot_total"] ?? 0);


$double_pay_hours =
    (float)
    ($hours["double_pay_total"] ?? 0);


$premium_30_hours =
    (float)
    ($hours["premium_30_total"] ?? 0);


$paid_leave_hours =
    (float)
    ($hours["paid_leave_total"] ?? 0);


// =====================================================
// 8. TOTAL ACTUAL MAN HOURS
// =====================================================
//
// TOTAL MAN HOURS:
//
// Regular
// + OT
// + RDOT
// + Double Pay
// + 30% Premium
//
// Paid Leave dili actual worked hours,
// mao dili nato iapil sa TOTAL MAN HOURS.
//
// =====================================================

$total_man_hours =

    $regular_hours

    + $ot_hours

    + $rdot_hours

    + $double_pay_hours

    + $premium_30_hours;


// =====================================================
// 9. REGULAR PAY
// =====================================================

$regular_pay =

    $regular_hours

    * $regular_rate;


// =====================================================
// 10. OT PAY
// =====================================================

$ot_pay =

    $ot_hours

    * $ot_rate;


// =====================================================
// 11. RDOT PAY
// =====================================================

$rdot_pay =

    $rdot_hours

    * $rdot_rate;


// =====================================================
// 12. DOUBLE PAY
// =====================================================

$double_pay =

    $double_pay_hours

    * $double_pay_rate;


// =====================================================
// 13. 30% PREMIUM PAY
// =====================================================

$premium_30_pay =

    $premium_30_hours

    * $premium_30_rate;


// =====================================================
// 14. PAID LEAVE PAY
// =====================================================

$paid_leave_pay =

    $paid_leave_hours

    * $paid_leave_rate;


// =====================================================
// 15. TOTAL GROSS PAY
// =====================================================

$gross_pay =

    $regular_pay

    + $ot_pay

    + $rdot_pay

    + $double_pay

    + $premium_30_pay

    + $paid_leave_pay;


// =====================================================
// 16. DEFAULT DEDUCTIONS
// =====================================================

$sss = 0;

$philhealth = 0;

$pagibig = 0;

$cash_advance = 0;

$other_deduction = 0;


// =====================================================
// 17. CHECK IF DEDUCTIONS WERE SUBMITTED
// =====================================================

$generated =
    $_SERVER["REQUEST_METHOD"]
    ===
    "POST";


if ($generated) {


    $sss =
        max(
            0,
            (float)
            ($_POST["sss"] ?? 0)
        );


    $philhealth =
        max(
            0,
            (float)
            ($_POST["philhealth"] ?? 0)
        );


    $pagibig =
        max(
            0,
            (float)
            ($_POST["pagibig"] ?? 0)
        );


    $cash_advance =
        max(
            0,
            (float)
            ($_POST["cash_advance"] ?? 0)
        );


    $other_deduction =
        max(
            0,
            (float)
            ($_POST["other_deduction"] ?? 0)
        );

}


// =====================================================
// 18. TOTAL DEDUCTIONS
// =====================================================

$total_deductions =

    $sss

    + $philhealth

    + $pagibig

    + $cash_advance

    + $other_deduction;


// =====================================================
// 19. NET PAY
// =====================================================

$net_pay =

    $gross_pay

    - $total_deductions;

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
Employee Payslip
</title>


<style>


/* =====================================================
GENERAL
===================================================== */

* {

    box-sizing: border-box;

}


body {

    margin: 0;

    padding: 35px 20px;

    background: #f4f6f8;

    font-family:
        Arial,
        Helvetica,
        sans-serif;

    color: #111;

}


/* =====================================================
PAYSLIP CONTAINER
===================================================== */

.payslip {

    width: 100%;

    max-width: 800px;

    margin: auto;

    padding: 35px;

    background: #ffffff;

    border-radius: 10px;

    box-shadow:
        0 2px 12px
        rgba(0, 0, 0, 0.10);

}


/* =====================================================
TITLE
===================================================== */

h1 {

    text-align: center;

    margin-top: 0;

    margin-bottom: 25px;

}


.title-line {

    border: 0;

    border-top:
        3px solid #222;

    margin-bottom: 30px;

}


/* =====================================================
SECTION TITLE
===================================================== */

.section-title {

    text-align: center;

    margin-top: 35px;

    margin-bottom: 15px;

    padding-bottom: 10px;

    border-bottom:
        2px solid #333;

}


/* =====================================================
ROWS
===================================================== */

.row {

    display: flex;

    justify-content:
        space-between;

    align-items: center;

    gap: 20px;

    padding: 10px 0;

    border-bottom:
        1px solid #ddd;

}


.row span:last-child,
.row strong:last-child {

    text-align: right;

}


/* =====================================================
TOTAL ROW
===================================================== */

.total-row {

    margin-top: 10px;

    padding-top: 15px;

    padding-bottom: 15px;

    border-top:
        2px solid #222;

    border-bottom:
        2px solid #222;

    font-size: 18px;

    font-weight: bold;

}


/* =====================================================
NET PAY
===================================================== */

.net-row {

    margin-top: 20px;

    padding-top: 20px;

    padding-bottom: 20px;

    border-top:
        4px double #222;

    border-bottom:
        4px double #222;

    font-size: 21px;

    font-weight: bold;

}


/* =====================================================
DEDUCTION BOX
===================================================== */

.deduction-box {

    margin-top: 30px;

    padding: 25px;

    background: #f8f9fa;

    border-radius: 8px;

}


.deduction-box h2 {

    margin-top: 0;

}


/* =====================================================
FORM
===================================================== */

.form-group {

    margin-bottom: 15px;

}


.form-group label {

    display: block;

    margin-bottom: 6px;

    font-weight: bold;

}


.form-group input {

    width: 100%;

    padding: 10px;

    border:
        1px solid #ccc;

    border-radius: 5px;

    font-size: 15px;

}


/* =====================================================
WARNING
===================================================== */

.warning {

    margin-top: 20px;

    padding: 15px;

    background: #fff3cd;

    color: #664d03;

    border-radius: 6px;

}


/* =====================================================
BUTTONS
===================================================== */

.buttons {

    display: flex;

    flex-wrap: wrap;

    gap: 10px;

    margin-top: 25px;

}


.btn {

    display: inline-block;

    padding:
        12px 18px;

    border: none;

    border-radius: 5px;

    cursor: pointer;

    text-decoration: none;

    font-size: 15px;

}


.btn-blue {

    background: #0d6efd;

    color: white;

}


.btn-green {

    background: #198754;

    color: white;

}


.btn-gray {

    background: #6c757d;

    color: white;

}


/* =====================================================
PRINT
===================================================== */

@media print {


    body {

        padding: 0;

        background: white;

    }


    .payslip {

        max-width: 100%;

        box-shadow: none;

        border-radius: 0;

    }


    .no-print {

        display: none !important;

    }

}


/* =====================================================
MOBILE
===================================================== */

@media(max-width: 600px) {


    body {

        padding: 10px;

    }


    .payslip {

        padding: 20px;

    }


    .row {

        align-items: flex-start;

    }

}


</style>


</head>


<body>


<div class="payslip">


<!-- ==================================================
TITLE
================================================== -->


<h1>
EMPLOYEE PAYSLIP
</h1>


<hr class="title-line">



<!-- ==================================================
EMPLOYEE INFORMATION
================================================== -->


<div class="row">


<strong>
Employee:
</strong>


<span>

<?php

echo htmlspecialchars(
    $employee["name"]
);

?>

</span>


</div>



<!-- POSITION -->


<div class="row">


<strong>
Position:
</strong>


<span>

<?php

echo htmlspecialchars(
    $employee["position"]
);

?>

</span>


</div>



<!-- PAY PERIOD -->


<div class="row">


<strong>
Pay Period:
</strong>


<span>


<?php

echo date(
    "M d, Y",
    strtotime($cutoff_start)
);

?>


-


<?php

echo date(
    "M d, Y",
    strtotime($cutoff_end)
);

?>


</span>


</div>



<!-- ==================================================
MAN HOURS
================================================== -->


<h2 class="section-title">

MAN HOURS

</h2>



<!-- SAVED DAYS -->


<div class="row">


<span>
Saved Attendance Days
</span>


<strong>

<?php

echo $attendance_days;

?>

Days

</strong>


</div>



<!-- REGULAR HOURS -->


<div class="row">


<span>
Regular Work Hours
</span>


<strong>

<?php

echo number_format(
    $regular_hours,
    2
);

?>

Hours

</strong>


</div>



<!-- OT HOURS -->


<div class="row">


<span>
Overtime Hours
</span>


<strong>

<?php

echo number_format(
    $ot_hours,
    2
);

?>

Hours

</strong>


</div>



<!-- RDOT HOURS -->


<div class="row">


<span>
RDOT Hours
</span>


<strong>

<?php

echo number_format(
    $rdot_hours,
    2
);

?>

Hours

</strong>


</div>



<!-- DOUBLE PAY HOURS -->


<div class="row">


<span>
Double Pay Hours
</span>


<strong>

<?php

echo number_format(
    $double_pay_hours,
    2
);

?>

Hours

</strong>


</div>



<!-- 30% PREMIUM HOURS -->


<div class="row">


<span>
30% Premium Hours
</span>


<strong>

<?php

echo number_format(
    $premium_30_hours,
    2
);

?>

Hours

</strong>


</div>



<!-- TOTAL MAN HOURS -->


<div class="
    row
    total-row
">


<span>
TOTAL MAN HOURS
</span>


<span>

<?php

echo number_format(
    $total_man_hours,
    2
);

?>

Hours

</span>


</div>



<!-- PAID LEAVE HOURS -->


<div class="row">


<span>
Paid Leave Hours
</span>


<span>

<?php

echo number_format(
    $paid_leave_hours,
    2
);

?>

Hours

</span>


</div>



<!-- ==================================================
EARNINGS
================================================== -->


<h2 class="section-title">

EARNINGS

</h2>



<!-- HOURLY RATE -->


<div class="row">


<span>
Base Hourly Rate
</span>


<strong>

₱<?php

echo number_format(
    $hourly_rate,
    2
);

?>

/ hour

</strong>


</div>



<!-- ==================================================
REGULAR PAY
================================================== -->


<div class="row">


<span>
Regular Pay
</span>


<strong>

₱<?php

echo number_format(
    $regular_pay,
    2
);

?>

</strong>


</div>



<!-- ==================================================
OT PAY
================================================== -->


<div class="row">


<span>

OT Pay

<?php if ($ot_hours > 0): ?>

<small>

(
<?php
echo number_format(
    $ot_hours,
    2
);
?>

hrs @

₱<?php
echo number_format(
    $ot_rate,
    2
);
?>

/hr
)

</small>

<?php endif; ?>

</span>


<strong>

₱<?php

echo number_format(
    $ot_pay,
    2
);

?>

</strong>


</div>



<!-- ==================================================
RDOT PAY
================================================== -->


<div class="row">


<span>

RDOT Pay

<?php if ($rdot_hours > 0): ?>

<small>

(
<?php
echo number_format(
    $rdot_hours,
    2
);
?>

hrs @

₱<?php
echo number_format(
    $rdot_rate,
    2
);
?>

/hr
)

</small>

<?php endif; ?>

</span>


<strong>

₱<?php

echo number_format(
    $rdot_pay,
    2
);

?>

</strong>


</div>



<!-- ==================================================
DOUBLE PAY
================================================== -->


<div class="row">


<span>

Double Pay

<?php if ($double_pay_hours > 0): ?>

<small>

(
<?php
echo number_format(
    $double_pay_hours,
    2
);
?>

hrs @

₱<?php
echo number_format(
    $double_pay_rate,
    2
);
?>

/hr
)

</small>

<?php endif; ?>

</span>


<strong>

₱<?php

echo number_format(
    $double_pay,
    2
);

?>

</strong>


</div>



<!-- ==================================================
30% PREMIUM PAY
================================================== -->


<div class="row">


<span>

30% Premium Pay

<?php if ($premium_30_hours > 0): ?>

<small>

(
<?php
echo number_format(
    $premium_30_hours,
    2
);
?>

hrs @

₱<?php
echo number_format(
    $premium_30_rate,
    2
);
?>

/hr
)

</small>

<?php endif; ?>

</span>


<strong>

₱<?php

echo number_format(
    $premium_30_pay,
    2
);

?>

</strong>


</div>



<!-- ==================================================
PAID LEAVE PAY
================================================== -->


<div class="row">


<span>
Paid Leave Pay
</span>


<strong>

₱<?php

echo number_format(
    $paid_leave_pay,
    2
);

?>

</strong>


</div>



<!-- ==================================================
TOTAL GROSS PAY
================================================== -->


<div class="
    row
    total-row
">


<span>
TOTAL SALARY / GROSS PAY
</span>


<span>

₱<?php

echo number_format(
    $gross_pay,
    2
);

?>

</span>


</div>



<!-- ==================================================
NO ATTENDANCE WARNING
================================================== -->


<?php if ($attendance_days === 0): ?>


<div class="
    warning
    no-print
">


<strong>
Warning:
</strong>


No saved attendance was found
for this employee inside the
selected pay period.


<br><br>


Please go back to Man Hours
and save the attendance first.


</div>


<?php endif; ?>



<!-- ==================================================
DEDUCTIONS FORM
================================================== -->


<?php if (!$generated): ?>


<div class="
    deduction-box
    no-print
">


<h2>
Deductions
</h2>



<form method="POST">



<!-- SSS -->


<div class="form-group">


<label>
SSS
</label>


<input

    type="number"

    name="sss"

    value="0.00"

    min="0"

    step="0.01"

>


</div>



<!-- PHILHEALTH -->


<div class="form-group">


<label>
PhilHealth
</label>


<input

    type="number"

    name="philhealth"

    value="0.00"

    min="0"

    step="0.01"

>


</div>



<!-- PAGIBIG -->


<div class="form-group">


<label>
Pag-IBIG
</label>


<input

    type="number"

    name="pagibig"

    value="0.00"

    min="0"

    step="0.01"

>


</div>



<!-- CASH ADVANCE -->


<div class="form-group">


<label>
Cash Advance
</label>


<input

    type="number"

    name="cash_advance"

    value="0.00"

    min="0"

    step="0.01"

>


</div>



<!-- OTHER DEDUCTION -->


<div class="form-group">


<label>
Other Deduction
</label>


<input

    type="number"

    name="other_deduction"

    value="0.00"

    min="0"

    step="0.01"

>


</div>



<!-- BUTTONS -->


<div class="buttons">


<button

    type="submit"

    class="
        btn
        btn-green
    "

>

Calculate Net Salary

</button>



<a

    href="attendance.php?id=<?php

        echo $employee_id;

    ?>&start=<?php

        echo urlencode(
            $cutoff_start
        );

    ?>&end=<?php

        echo urlencode(
            $cutoff_end
        );

    ?>"

    class="
        btn
        btn-gray
    "

>

Back to Man Hours

</a>


</div>


</form>


</div>



<?php else: ?>



<!-- ==================================================
DEDUCTIONS
================================================== -->


<h2 class="section-title">

DEDUCTIONS

</h2>



<!-- SSS -->


<div class="row">


<span>
SSS
</span>


<span>

₱<?php

echo number_format(
    $sss,
    2
);

?>

</span>


</div>



<!-- PHILHEALTH -->


<div class="row">


<span>
PhilHealth
</span>


<span>

₱<?php

echo number_format(
    $philhealth,
    2
);

?>

</span>


</div>



<!-- PAGIBIG -->


<div class="row">


<span>
Pag-IBIG
</span>


<span>

₱<?php

echo number_format(
    $pagibig,
    2
);

?>

</span>


</div>



<!-- CASH ADVANCE -->


<div class="row">


<span>
Cash Advance
</span>


<span>

₱<?php

echo number_format(
    $cash_advance,
    2
);

?>

</span>


</div>



<!-- OTHER DEDUCTION -->


<div class="row">


<span>
Other Deduction
</span>


<span>

₱<?php

echo number_format(
    $other_deduction,
    2
);

?>

</span>


</div>



<!-- TOTAL DEDUCTIONS -->


<div class="
    row
    total-row
">


<span>
TOTAL DEDUCTIONS
</span>


<span>

₱<?php

echo number_format(
    $total_deductions,
    2
);

?>

</span>


</div>



<!-- ==================================================
NET PAY
================================================== -->


<div class="
    row
    net-row
">


<span>
NET SALARY / TAKE HOME PAY
</span>


<span>

₱<?php

echo number_format(
    $net_pay,
    2
);

?>

</span>


</div>



<!-- ==================================================
BUTTONS
================================================== -->


<div class="
    buttons
    no-print
">


<button

    type="button"

    onclick="window.print()"

    class="
        btn
        btn-blue
    "

>

Print Payslip

</button>



<a

    href="attendance.php?id=<?php

        echo $employee_id;

    ?>&start=<?php

        echo urlencode(
            $cutoff_start
        );

    ?>&end=<?php

        echo urlencode(
            $cutoff_end
        );

    ?>"

    class="
        btn
        btn-gray
    "

>

Back to Man Hours

</a>



<a

    href="employees.php"

    class="
        btn
        btn-gray
    "

>

Employee List

</a>


</div>


<?php endif; ?>


</div>


</body>

</html>