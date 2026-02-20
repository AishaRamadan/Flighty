<?php
include("./Connection/db-connection.php");

// make query 
$sql = "SELECT Id, flight_name, source, destination,date, time FROM flights;";
$result = $conn->query(query: $sql);  // result is an object from type mysqli_result

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./style/style.css">
</head>

<body>
    <div class="navbar">
        <div class="logo">Flighty</div>
        <div class="nav-links">
            <a href="mainPageAdmin.php">Flights</a>
            <a href="manageFlight.php" class="nav-graycolor">Manage flights</a>
            <a href="manageAirport.php" class="nav-graycolor">Manage airports</a>
            <a href="accountAdmin.php" class="Account-btn">Account</a>
        </div>
    </div>

    <div class="img">
        <div class="to-center">
            <table class="flights-tbl-admin">
                <tr>
                    <th>ID</th>
                    <th>Flight name</th>
                    <th>source</th>
                    <th>destination</th>
                    <th>date</th>
                    <th>time</th>
                </tr>

                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td> <?= $row['Id'] ?></td>
                        <td> <?= $row['flight_name'] ?></td>
                        <td> <?= $row['source'] ?></td>
                        <td> <?= $row['destination'] ?></td>
                        <td> <?= $row['date'] ?></td>
                        <td> <?= $row['time'] ?></td>
                    </tr>
                <?php endwhile ?>  
            </table>
        </div>

        <div>

        </div>
    </div>

</body>

</html>