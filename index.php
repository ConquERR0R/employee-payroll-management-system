<?php

include "db.php";


// ==========================================
// COUNT TOTAL EMPLOYEES
// ==========================================

$employee_query =
    mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total
         FROM employees"
    );

$employee_data =
    mysqli_fetch_assoc(
        $employee_query
    );

$total_employees =
    $employee_data["total"];


// ==========================================
// COUNT ATTENDANCE RECORDS
// ==========================================

$attendance_query =
    mysqli_query(
        $conn,
        "SELECT COUNT(*) AS total
         FROM attendance"
    );

$attendance_data =
    mysqli_fetch_assoc(
        $attendance_query
    );

$total_attendance =
    $attendance_data["total"];

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
Employee Payroll System
</title>


<style>

/* ==========================================
GENERAL
========================================== */

* {

    box-sizing: border-box;

}


body {

    margin: 0;

    font-family:
        Arial,
        Helvetica,
        sans-serif;

    background: #f4f6f9;

}


/* ==========================================
TOP NAVIGATION
========================================== */

.navbar {

    background: #212529;

    color: white;

    padding:
        18px 40px;

    display: flex;

    justify-content:
        space-between;

    align-items: center;

}


.navbar h2 {

    margin: 0;

}


.navbar span {

    font-size: 14px;

}


/* ==========================================
MAIN CONTAINER
========================================== */

.dashboard {

    max-width: 1200px;

    margin:
        40px auto;

    padding:
        0 20px;

}


/* ==========================================
WELCOME
========================================== */

.welcome {

    margin-bottom: 30px;

}


.welcome h1 {

    margin-bottom: 8px;

}


.welcome p {

    color: #666;

}


/* ==========================================
STATISTICS
========================================== */

.stats {

    display: grid;

    grid-template-columns:
        repeat(
            auto-fit,
            minmax(220px, 1fr)
        );

    gap: 20px;

    margin-bottom: 35px;

}


.stat-card {

    background: white;

    padding: 25px;

    border-radius: 10px;

    box-shadow:
        0 2px 8px
        rgba(0,0,0,0.08);

}


.stat-card h3 {

    margin-top: 0;

    color: #555;

}


.stat-number {

    font-size: 35px;

    font-weight: bold;

}


/* ==========================================
MENU CARDS
========================================== */

.menu-grid {

    display: grid;

    grid-template-columns:
        repeat(
            auto-fit,
            minmax(250px, 1fr)
        );

    gap: 25px;

}


/* CARD */

.menu-card {

    background: white;

    padding: 30px;

    border-radius: 12px;

    text-decoration: none;

    color: #222;

    box-shadow:
        0 3px 10px
        rgba(0,0,0,0.08);

    transition:
        transform 0.2s,
        box-shadow 0.2s;

}


.menu-card:hover {

    transform:
        translateY(-5px);

    box-shadow:
        0 8px 20px
        rgba(0,0,0,0.15);

}


/* ICON */

.icon {

    font-size: 40px;

    margin-bottom: 15px;

}


.menu-card h2 {

    margin:
        0 0 10px;

}


.menu-card p {

    margin: 0;

    color: #666;

    line-height: 1.5;

}


/* ==========================================
FOOTER
========================================== */

.footer {

    text-align: center;

    margin-top: 50px;

    color: #888;

    font-size: 14px;

}


/* ==========================================
MOBILE
========================================== */

@media
(max-width: 600px) {


    .navbar {

        padding:
            15px 20px;

    }


    .navbar span {

        display: none;

    }


    .dashboard {

        margin-top: 25px;

    }

}

</style>

</head>


<body>


<!-- ==========================================
NAVIGATION
========================================== -->


<div class="navbar">

    <h2>
        Employee Payroll System
    </h2>

    <span>
        Payroll Management Dashboard
    </span>

</div>



<!-- ==========================================
DASHBOARD
========================================== -->


<div class="dashboard">


<!-- WELCOME -->


<div class="welcome">

    <h1>
        Dashboard
    </h1>

    <p>
        Manage employees, work hours,
        overtime and payroll.
    </p>

</div>



<!-- ==========================================
STATISTICS
========================================== -->


<div class="stats">


    <!-- TOTAL EMPLOYEES -->


    <div class="stat-card">

        <h3>
            Total Employees
        </h3>

        <div class="stat-number">

            <?php
            echo $total_employees;
            ?>

        </div>

    </div>



    <!-- WORK RECORDS -->


    <div class="stat-card">

        <h3>
            Work Records
        </h3>

        <div class="stat-number">

            <?php
            echo $total_attendance;
            ?>

        </div>

    </div>


</div>



<!-- ==========================================
MAIN MENU
========================================== -->


<div class="menu-grid">


    <!-- ======================================
    ADD EMPLOYEE
    ======================================= -->


    <a
        href="create.php"
        class="menu-card"
    >

        <div class="icon">
            ➕
        </div>

        <h2>
            Add Employee
        </h2>

        <p>
            Register a new employee,
            position and hourly rate.
        </p>

    </a>



    <!-- ======================================
    EMPLOYEE LIST
    ======================================= -->


    <a
        href="employees.php"
        class="menu-card"
    >

        <div class="icon">
            👥
        </div>

        <h2>
            Employee List
        </h2>

        <p>
            View, edit and manage
            all registered employees.
        </p>

    </a>



    <!-- ======================================
    ATTENDANCE
    ======================================= -->


    <a
        href="employees.php"
        class="menu-card"
    >

        <div class="icon">
            🕐
        </div>

        <h2>
            Attendance & Work Hours
        </h2>

        <p>
            Record regular working hours
            and employee overtime.
        </p>

    </a>



    <!-- ======================================
    PAYROLL
    ======================================= -->


    <a
        href="employees.php"
        class="menu-card"
    >

        <div class="icon">
            💰
        </div>

        <h2>
            Payroll & Payslip
        </h2>

        <p>
            Calculate salaries,
            deductions and generate payslips.
        </p>

    </a>


</div>



<!-- FOOTER -->


<div class="footer">

    Employee Payroll Management System

</div>


</div>


</body>

</html>