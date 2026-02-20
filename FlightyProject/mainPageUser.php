<?php
// mainPageUser.php 
session_start();
// connect to the database
include("./Connection/db-connection.php");

// Define user_id from session --> default to 1 for testing
$user_id = $_SESSION['user_id'];


$sql = "SELECT id, flight_name, source, destination, date, time FROM Flights;";
$results = [];
$airports = [];
$airports_res = $conn->query("SELECT airport_name FROM airport");
if ($airports_res->num_rows > 0) {
    while ($row = $airports_res->fetch_assoc()) {
        $airports[] = $row["airport_name"];
    }
}

// when search button is clicked
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["AllFlights"])) {
        $sql = "SELECT id, flight_name, source, destination, date, time FROM Flights;";
    } else {
        $FromWhere = trim($_POST["FromWhere"] ?? "");
        $ToWhere = trim($_POST["ToWhere"] ?? "");
        $date = trim($_POST["date"] ?? "");

        if ($FromWhere === "" && $ToWhere === "" && $date === "") {
            $sql = "SELECT id, flight_name, source, destination, date, time FROM Flights;";
        } else {
            // Prepared Statement for search
            $sql_search = "SELECT id, flight_name, source, destination, date, time
                            FROM Flights
                            WHERE source LIKE ? AND destination LIKE ? AND date LIKE ?";

            $stmt = $conn->prepare(query: $sql_search);

            $param_fromw = "%$FromWhere%";
            $param_tow = "%$ToWhere%";
            $param_dt = "%$date%";

            $stmt->bind_param("sss", $param_fromw, $param_tow, $param_dt);

            $stmt->execute();

            $result_obj = $stmt->get_result();
            $results = $result_obj->fetch_all(MYSQLI_ASSOC);

            $stmt->close();

            // If search returns no results --> show warning
            if (empty($results)) {
                set_flash_message("No flights found matching your search.", "warning");
                header("Location: mainPageUser.php");
                exit;
            }
        }
    }
}

if (empty($results)) {

    $result_obj = $conn->query($sql);

    if ($result_obj) {
        $results = $result_obj->fetch_all(MYSQLI_ASSOC);
        $result_obj->free();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width">
    <title>Flights - Flighty</title>
    <link rel="stylesheet" href="./style/style.css">
</head>

<body>
    <div class="navbar">
        <div class="logo">Flighty</div>
        <div class="nav-links">
            <a href="mainPageUser.php">Flights</a>
            <a href="Booked.php" class="nav-graycolor">Booked</a>
            <a href="accountUser.php" class="Account-btn">Account</a>
        </div>
    </div>

    <!-- Display flash message if exists -->
    <?php
    if (isset($_SESSION['flash'])) {
        $message = htmlspecialchars($_SESSION['flash']);
        $type = htmlspecialchars($_SESSION['flash_type'] ?? 'info');

        echo "<div class='flash flash-{$type}'>{$message}</div>";

        //clear flash message after displaying
        unset($_SESSION['flash'], $_SESSION['flash_type']);
    }
    ?>

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
                    <th>Action</th>
                </tr>

                <?php foreach ($results as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row["flight_name"]) ?></td>
                        <td><?= htmlspecialchars($row["source"]) ?></td>
                        <td><?= htmlspecialchars($row["destination"]) ?></td>
                        <td><?= htmlspecialchars($row["date"]) ?></td>
                        <td><?= htmlspecialchars($row["time"]) ?></td>
                        <td>
                            <!--  send flight_id via GET -->
                            <a class="Book-btn" href="Book.php?flight_id=<?= (int) $row['id'] ?>">Book</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>

    </div>
</body>

</html>