<!-- <?php
echo "Helooooooo from mangeFlight.php  :) ";
?> -->


<?php
include("./Connection/db-connection.php");

$errors_msg = [];
$airports = [];

$airport_res = $conn->query("SELECT airport_name FROM airport ");
if ($airport_res->num_rows > 0) {
    while ($row = $airport_res->fetch_assoc()) {
        $airports[] = $row["airport_name"];
    }
}


function searchFlight(&$errors_msg)
{
    global $id, $flightName, $source, $destination, $date, $time, $B_seat, $E_seat, $bag_num, $price, $sprice, $dprice, $conn;
    if (empty($_POST["id"])) {
        $errors_msg["id"] = "*Enter id";
    } elseif (!is_numeric($_POST["id"])) {
        $errors_msg["id"] = "*Enter valid id";
    } else {
        $id = $_POST["id"];
        $sql = "SELECT * FROM flights WHERE id = $id";
        $result = $conn->query($sql); //$result is an object in select 

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $flightName = $row["flight_name"];    //  $row["var"]  (var as written in database )
            $source = $row["source"];
            $destination = $row["destination"];
            $date = $row["date"];
            $time = $row["time"];
            $B_seat = $row["business_seat"];
            $E_seat = $row["economy_seat"];
            $bag_num = $row["bag_number"];

            $sprice_res = $conn->query("SELECT price FROM airport WHERE airport_name = '$source'");
            $dprice_res = $conn->query("SELECT price FROM airport WHERE airport_name = '$destination'");
            $sprice = $sprice_res->fetch_assoc()["price"];
            $dprice = $dprice_res->fetch_assoc()["price"];
            $price = $sprice + $dprice;

        } else {
            $errors_msg["id"] = "Flight not found";
        }
    }

}

function addFlight(&$errors_msg)
{
    global $conn, $flightName, $source, $destination, $date, $time, $B_seat, $E_seat, $bag_num;

    $flightName = $_POST["flightName"];
    $source = $_POST["source"];
    $destination = $_POST["destination"];
    $date = $_POST["date"];
    $time = $_POST["time"];
    $B_seat = $_POST["B_seat"];
    $E_seat = $_POST["E_seat"];
    $bag_num = $_POST["Bag_num"];
    $valid = true;

    if (empty($flightName)) {
        $errors_msg["flightName"] = "*Enter flight name";
        $valid = false;
    }
    if (empty($source)) {
        $errors_msg["source"] = "*choose source";
        $valid = false;
    }
    if (empty($destination)) {
        $errors_msg["destination"] = "*choose destination";
        $valid = false;
    }
    if (empty($date)) {
        $errors_msg["date"] = "*Enter date";
        $valid = false;
    }
    if (empty($time)) {
        $errors_msg["time"] = "*Enter time";
        $valid = false;
    }
    if (empty($B_seat)) {
        $errors_msg["B_seat"] = "*Enter B_seat ";
        $valid = false;
    }
    if (empty($E_seat)) {
        $errors_msg["E_seat"] = "*Enter E_seat ";
        $valid = false;
    }
    if (empty($bag_num)) {
        $errors_msg["Bag_num"] = "*Enter Bag_num ";
        $valid = false;
    }
    if ($valid) {
        $sql_search = "SELECT flight_name FROM flights where flight_name= '$flightName' ";
        $result_search = $conn->query($sql_search);
        if ($result_search->num_rows > 0) {
            echo "<script>
            alert('This Flight aleardy exists ');
            </script>";
            return;
        }
        $sql = "INSERT INTO flights (flight_name,source, destination,date,time,business_seat,economy_seat,bag_number) 
        values('$flightName','$source','$destination','$date','$time',$B_seat,$E_seat,$bag_num)";
        $result = $conn->query($sql);
        if ($result == true) {
            echo "<script>
            alert('Flight added successfully :)');
            window.location.href = 'manageFlight.php';
            </script>";
        } else {
            echo "error add flight :(";
        }
    } else {
        return;
    }

}

function updateFlight(&$errors_msg)
{
    global $conn, $id, $flightName, $source, $destination, $date, $time, $B_seat, $E_seat, $bag_num;

    $id = $_POST["id"];
    $flightName = $_POST["flightName"];
    $source = $_POST["source"];
    $destination = $_POST["destination"];
    $date = $_POST["date"];
    $time = $_POST["time"];
    $B_seat = $_POST["B_seat"];
    $E_seat = $_POST["E_seat"];
    $bag_num = $_POST["Bag_num"];
    $valid = true;

    if (empty($id)) {
        $errors_msg["id"] = "*Enter id";
        $valid = false;
    }
    if (empty($flightName)) {
        $errors_msg["flightName"] = "*Enter flight name";
        $valid = false;
    }
    if (empty($source)) {
        $errors_msg["source"] = "*choose source";
        $valid = false;
    }
    if (empty($destination)) {
        $errors_msg["destination"] = "*choose destination";
        $valid = false;
    }
    if (empty($date)) {
        $errors_msg["date"] = "*Enter date";
        $valid = false;
    }
    if (empty($time)) {
        $errors_msg["time"] = "*Enter time";
        $valid = false;
    }
    if (empty($B_seat)) {
        $errors_msg["B_seat"] = "*Enter Business seat ";
        $valid = false;
    }
    if (empty($E_seat)) {
        $errors_msg["E_seat"] = "*Enter Economic seat ";
        $valid = false;
    }
    if (empty($bag_num)) {
        $errors_msg["Bag_num"] = "*Enter Bag numbers ";
        $valid = false;
    }
    if ($valid) {
        $sql = "UPDATE  flights SET 
        flight_name='$flightName',
        source = '$source',
        destination ='$destination',
        date='$date',
        time='$time',
        business_seat= $B_seat,
        economy_seat= $E_seat ,
        bag_number=$bag_num
        WHERE Id = $id ";
        $result = $conn->query($sql);
        if ($result == true) {
            echo "<script>
            alert('Flight updated successfully :)');
            window.location.href = 'manageFlight.php';
            </script>";
        } else {
            echo "error update flight :(";
        }
    } else {
        return;
    }
}

function deleteFlight(&$errors_msg)
{
    $id = $_POST["id"];
    $valid = 1;
    global $conn;
    if (empty($id)) {
        $errors_msg["id"] = "*Enter ID";
        $valid = 0;
        return;
    }

    if (!is_numeric($id)) {
        $errors_msg["id"] = "*Enter valid iD";
        $valid = 0;
        return;
    }

    if ($valid) {
        $sql1 = "DELETE FROM userflights WHERE flight_id = $id";
        $conn->query($sql1); // due to refrence so we had to delete from childe first   
        $sql = "DELETE FROM flights WHERE id = $id";
        $result = $conn->query($sql);  // returns true or false 
        if ($result == true) {
            if ($conn->affected_rows > 0) {
                echo "<script>
                    alert('Flight deleted successfully :)');
                     window.location.href = 'manageFlight.php';
                  </script>";
            } else {
                echo "<script>
                    alert('No Flight exists with this Id ');
                     window.location.href = 'manageFlight.php';
                  </script>";
            }
        } else {
            echo "<script>
                alert('Error deleting flight');
                window.location.href = 'manageFlight.php';
                </script>";
        }
    }

}



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["search"])) {
        searchFlight($errors_msg);
    }

    if (isset($_POST["add"])) {
        addFlight($errors_msg);
    }

    if (isset($_POST["update"])) {
        updateFlight($errors_msg);
    }

    if (isset($_POST["delete"])) {
        deleteFlight($errors_msg);
    }
}
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
        <h2 class="manage-h">Manage Flights</h2>
        <form method="POST" class="manage-content" id="manageFlights">
            <div class="content-l">
                <div>
                    <label for="id">ID:</label>
                    <input type="text" name="id" value="<?php echo isset($id) ? $id : ''; ?>">
                    <span class="error-msg"><?php if (isset($errors_msg["id"]))
                        echo $errors_msg["id"] ?>
                        </span>
                        <input type="submit" value="Search" class="manage-search" name="search">
                    </div>

                    <div>
                        <label for=" fName">Flight Name:</label>
                        <input type="text" name="flightName" value="<?php echo isset($flightName) ? $flightName : ''; ?>">
                    <span class="error-msg"><?php if (isset($errors_msg["flightName"]))
                        echo $errors_msg["flightName"] ?>
                        </span>
                    </div>

                    <div>
                        <label for="source">Source:</label>
                        <select name="source" class="sd-select">
                            <option value="">--Select Source--</option>
                        <?php foreach ($airports as $airport): ?>
                            <option value="<?php echo $airport; ?>" <?php echo (isset($source) && $source == $airport) ? 'selected' : ''; ?>>
                                <?php echo $airport; ?>
                            </option>
                        <?php endforeach; ?>

                    </select>
                    <span class="error-msg"><?php if (isset($errors_msg["source"]))
                        echo $errors_msg["source"] ?>
                        </span>
                    </div>

                    <div>
                        <label>destination:</label>
                        <select name="destination" class="sd-select">
                            <option value="">--Select destination--</option>
                        <?php foreach ($airports as $airport): ?>
                            <option value="<?php echo $airport; ?>" <?php echo (isset($destination) && $destination == $airport) ? 'selected' : ''; ?>>
                                <?php echo $airport; ?>
                            </option>
                        <?php endforeach; ?>

                    </select>
                    <span class="error-msg"><?php if (isset($errors_msg["destination"]))
                        echo $errors_msg["destination"] ?>
                        </span>
                    </div>

                    <div>
                        <label for="date">date:</label>
                        <input type="text" name="date" value="<?php echo isset($date) ? $date : ''; ?>">
                    <span class="error-msg"><?php if (isset($errors_msg["date"]))
                        echo $errors_msg["date"] ?>
                        </span>
                    </div>

                    <div>
                        <label for="time">time:</label>
                        <input type="text" name="time" value="<?php echo isset($time) ? $time : ''; ?>">
                    <span class="error-msg"><?php if (isset($errors_msg["time"]))
                        echo $errors_msg["time"] ?>
                        </span>
                    </div>
                </div>

                <div class="content-R">
                    <div>
                        <label for="B-seat">Business seat:</label>
                        <input type="text" name="B_seat" value="<?php echo isset($B_seat) ? $B_seat : ''; ?>"> <br>
                    <span class="error-msg"><?php if (isset($errors_msg["B_seat"]))
                        echo $errors_msg["B_seat"] ?>
                        </span>
                    </div>

                    <div>
                        <label for="E-seat">Economy seat:</label>
                        <input type="text" name="E_seat" value="<?php echo isset($E_seat) ? $E_seat : ''; ?>"> <br>
                    <span class="error-msg"><?php if (isset($errors_msg["E_seat"]))
                        echo $errors_msg["E_seat"] ?>
                        </span>
                    </div>

                    <div>
                        <label for="Bag-num">Bag numbers:</label>
                        <input type="text" name="Bag_num" value="<?php echo isset($bag_num) ? $bag_num : ''; ?>"> <br>
                    <span class="error-msg"><?php if (isset($errors_msg["Bag_num"]))
                        echo $errors_msg["Bag_num"] ?>
                        </span>
                    </div>

                    <div>
                        <label for="price"> Price:</label>
                        <input type="text" name="price" value="<?php echo isset($price) ? $price : ''; ?>" disabled> <br>
                    <span class="error-msg"><?php if (isset($errors_msg["price"]))
                        echo $errors_msg["price"] ?>
                        </span>
                    </div>
                </div>
            </form>

            <div class="to-center">
                <button type="submit" class="manage-btn" form="manageFlights" name="add">Add</button>
                <button type="submit" class="manage-btn" form="manageFlights" name="update">Update</button>
                <button type="submit" class="manage-btn" form="manageFlights" name="delete">Delete</button>
            </div>
        </div>

    </body>

    </html>