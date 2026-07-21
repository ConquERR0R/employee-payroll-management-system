<?php

include "db.php";


// ==========================================
// GET ALL EMPLOYEES
// ==========================================

$sql = "
    SELECT *
    FROM employees
    ORDER BY id DESC
";


$result =
    mysqli_query(
        $conn,
        $sql
    );


if (!$result) {

    die(
        "Error loading employees: "
        . mysqli_error($conn)
    );

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
Employee List
</title>

<link
    rel="stylesheet"
    href="style.css"
>


<style>

.page-header {

    display: flex;

    justify-content:
        space-between;

    align-items: center;

    gap: 15px;

    margin-bottom: 25px;

}


.action-buttons {

    display: flex;

    gap: 5px;

    flex-wrap: wrap;

}


@media
(max-width: 700px) {


    .page-header {

        flex-direction: column;

        align-items:
            flex-start;

    }

}

</style>

</head>


<body>


<div class="container">


<!-- ==========================================
HEADER
========================================== -->


<div class="page-header">


    <div>

        <h1>
            Employee List
        </h1>

        <p>
            Manage all registered employees.
        </p>

    </div>


    <div>


        <a
            href="index.php"
            class="btn cancel"
        >

            Dashboard

        </a>


        <a
            href="create.php"
            class="btn add"
        >

            + Add Employee

        </a>


    </div>


</div>



<!-- ==========================================
EMPLOYEE TABLE
========================================== -->


<table>


<thead>


<tr>

    <th>
        ID
    </th>

    <th>
        Employee Name
    </th>

    <th>
        Email
    </th>

    <th>
        Position
    </th>

    <th>
        Hourly Rate
    </th>

    <th>
        Actions
    </th>

</tr>


</thead>


<tbody>


<?php if (
    mysqli_num_rows($result) > 0
): ?>


<?php while (
    $employee =
    mysqli_fetch_assoc($result)
): ?>


<tr>


<!-- ID -->

<td>

<?php
echo $employee["id"];
?>

</td>



<!-- NAME -->

<td>

<strong>

<?php

echo htmlspecialchars(
    $employee["name"]
);

?>

</strong>

</td>



<!-- EMAIL -->

<td>

<?php

echo htmlspecialchars(
    $employee["email"]
);

?>

</td>



<!-- POSITION -->

<td>

<?php

echo htmlspecialchars(
    $employee["position"]
);

?>

</td>



<!-- RATE -->

<td>

₱<?php

echo number_format(
    $employee["hourly_rate"],
    2
);

?> / hr

</td>



<!-- ACTIONS -->

<td>


<div class="action-buttons">


<!-- ATTENDANCE -->


<a
    href="attendance.php?id=<?php
    echo $employee["id"];
    ?>"
    class="btn attendance"
>

Attendance

</a>



<!-- PAYSLIP -->


<a
    href="payslip.php?id=<?php
    echo $employee["id"];
    ?>"
    class="btn payslip"
>

Payslip

</a>



<!-- EDIT -->


<a
    href="edit.php?id=<?php
    echo $employee["id"];
    ?>"
    class="btn edit"
>

Edit

</a>



<!-- DELETE -->


<a
    href="delete.php?id=<?php
    echo $employee["id"];
    ?>"
    class="btn delete"

    onclick="
    return confirm(
        'Are you sure you want to delete this employee?'
    );
    "
>

Delete

</a>


</div>


</td>


</tr>


<?php endwhile; ?>


<?php else: ?>


<tr>


<td
    colspan="6"
    style="text-align:center;"
>

No employees registered yet.

</td>


</tr>


<?php endif; ?>


</tbody>


</table>


</div>


</body>

</html>