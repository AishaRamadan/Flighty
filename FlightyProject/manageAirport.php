<!-- <?php
echo "Helooooooo from mangeAirport.php  :) ";
?> -->

<?php
include "./Connection/db-connection.php";

$errors_msg = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id = $_POST['id'];
    $airport_name = $_POST['airport_name'];
    $price = $_POST['price'];
    $valid = 1;
    // $action = $_POST['action'] ?? '';
    if (isset($_POST['update'])) {
        if (empty($id)) {
            $errors_msg["id"] = "ID is required";
            $valid = 0;
        }
        if (empty($airport_name)) {
            $errors_msg["airport_name"] = "Airport Name is required.";
            $valid = 0;
        }
        if (empty($price)) {
            $errors_msg['price'] = "Price is required";
            $valid = 0;
        }

        if ($valid == 1) {
            $sql = "UPDATE airport SET airport_name = '$airport_name', price = $price WHERE id = $id";
            $result = $conn->query($sql);
            if ($result == true) {
                if ($conn->affected_rows > 0) {
                    echo '<script>alert("Airport updated successfully!");</script>';
                } else {
                    echo '<script>alert("No changes were made when updating");</script>' . $conn->error;
                }
            } else {
                echo '<script>alert("Error updating airport: ' . $conn->error . '");</script>';
            }
        }
    }

    if (isset($_POST["delete"])) {
        if (empty($id)) {
            $errors_msg["id"] = "ID is required";
            $valid = 0;
        }

        if ($valid == 1) {
            $sql = "DELETE FROM airport WHERE id = $id";
            $result = $conn->query($sql);
            if ($result == true) {
                if ($conn->affected_rows > 0) {
                    echo '<script>alert("Airport deleted successfully!");</script>';
                } else {
                    echo '<script>alert("No changes were made when deleting.");</script>' . $conn->error;
                }
            } else {
                echo '<script>alert("Error deleting airport: ' . $conn->error . '");</script>';
            }
        }
    }


}

$airports = $conn->query("SELECT * FROM airport ORDER BY id");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Airport</title>
    <link rel="stylesheet" href="./style/airport.css">
</head>

<body>
    <div class="navbar">
        <div class="logo">Flighty</div>
        <div class="links">
            <a href="mainPageAdmin.php">Flights</a>
            <a href="manageFlights.php">Manage Flights</a>
            <a href="manageAirport.php" class="current_page">Manage Airports</a>
            <button class="account-btn" onclick="window.location.href='accountAdmin.php'">Account</button>
        </div>
    </div>

    <div class="Background">
        <div class="data">

            <form action="" method="post">
                <label for="userId">ID :</label>
                <input type="number" id="userId" name="id"
                    value="<?php echo isset($id) ? htmlspecialchars($id) : ''; ?>">
                <span class="error-msg"><?php if (isset($errors_msg['id']))
                    echo $errors_msg['id']; ?></span>
                <br>

                <label for="air_name">Airport Name :</label>
                <input type="text" id="air_name" name="airport_name"
                    value="<?php echo isset($airport_name) ? htmlspecialchars($airport_name) : ''; ?>">
                <span class="error-msg"><?php if (isset($errors_msg['airport_name']))
                    echo $errors_msg['airport_name']; ?></span>
                <br>

                <label for="price">Price :</label>
                <input type="text" id="price" name="price"
                    value="<?php echo isset($price) ? htmlspecialchars($price) : ''; ?>">
                <span class="error-msg"><?php if (isset($errors_msg['price']))
                    echo $errors_msg['price']; ?></span>
                <br>

                <div class="btn">
                    <input type="submit" class="update_btn" name="update" value="update">
                    <input type="submit" class="delete_btn" name="delete" value="delete">
                </div>
            </form>
        </div>



        <div class="tabl">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Airport Name</th>
                    <th>Price</th>
                </tr>
                <?php if ($airports->num_rows > 0): ?>
                    <?php while ($row = $airports->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['id']); ?></td>
                            <td><?php echo htmlspecialchars($row['airport_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['price']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3">No Airports Found</td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>

</body>

</html>

<?php
$conn->close();
?>