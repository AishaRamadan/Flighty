<?php
include("./Connection/db-connection.php");

$airports = [];
$airports_res = $conn->query("SELECT airport_name FROM airport");
if ($airports_res->num_rows > 0) {
    while ($row = $airports_res->fetch_assoc()) {
        $airports[] = $row["airport_name"];
    }
}



$sql = "SELECT flight_name, source, destination,date, time FROM flights;";
//when search button is clicked
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST["AllFlights"])) {
        $sql = "SELECT flight_name, source, destination,date, time FROM flights;";
    } else {
        $FromWhere = $_POST["FromWhere"];
        $ToWhere = $_POST["ToWhere"];
        $date = $_POST["date"];

        // click search when all values is null
        if ($FromWhere === "" && $ToWhere == "" && $date === "") {
            $sql = "SELECT flight_name, source, destination,date, time FROM flights;";
        } else {
            $sql = "SELECT flight_name, source, destination,date, time 
                FROM flights 
                Where source Like '%$FromWhere%' and destination Like '%$ToWhere%' and date Like '%$date%' ";
        }
    }
}


$result = $conn->query($sql);  // result is an object from type mysqli_result

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
            <a href="mainPage.php">Flights</a>
            <a href="login.php" class="nav-graycolor">sign in</a>
            <a href="signUp.php" class="signup-btn">Sign up</a>
        </div>
    </div>

    <div class="img">
        <div class="to-center">
            <form method="POST">
                <input class="AllFlights-btn" name="AllFlights" type="submit" value="All flights">
            </form>
        </div>

        <div class="to-center">
            <p class="or-dashed">------------------------------------OR------------------------------------</p>
        </div>

        <div class="to-center">
            <form method="POST">

                <select class="Search" name="FromWhere">
                    <option value="">From Where</option>
                    <?php foreach ($airports as $airport): ?>
                        <option>
                            <?php echo $airport; ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select class="Search" name="ToWhere">
                    <option value="">To Where</option>
                    <?php foreach ($airports as $airport): ?>
                        <option>
                            <?php echo $airport; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <!-- <input class="Search" type="text" placeholder="From where?" name="FromWhere">
                <input class="Search" type="text" placeholder="To where?" name="ToWhere"> -->
                <input class="Search" type="date" name="date">
                <input type="submit" value="Search" class="search-btn">
            </form>
        </div>

        <div class="to-center">
            <table class="flights-tbl">
                <tr>
                    <th>Flight name</th>
                    <th>source</th>
                    <th>destination</th>
                    <th>date</th>
                    <th>time</th>
                </tr>

                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row["flight_name"] ?> </td>
                        <td><?= $row["source"] ?> </td>
                        <td><?= $row["destination"] ?> </td>
                        <td><?= $row["date"] ?> </td>
                        <td><?= $row["time"] ?> </td>
                    </tr>
                <?php endwhile; ?>


                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <tr>
                        <td style="padding: 12px 0px"></td>
                        <td></td>
                        <td></td>
                        <td> </td>
                        <td></td>
                    </tr>
                <?php endfor; ?>

            </table>
        </div>

        <div class="to-center">
            <a href="login.php" class="Book-btn">Book flight</a>
        </div>
    </div>
</body>

</html>