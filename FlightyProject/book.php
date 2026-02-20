<!-- <?php
echo "Helooooo from Book.php :)";
?>
 -->

<?php
// Book.php 
session_start();
include("./Connection/db-connection.php");
$user_id = $_SESSION['user_id'];

$flight_id = isset($_GET['flight_id']) ? (int) $_GET['flight_id'] : null;


if (!$flight_id) {
  exit("No flight selected. Go back and choose a flight.");
}

// function to get airport price
function getAirportPrice($conn, $airportName)
{
  $sql_airport = "SELECT price FROM Airport WHERE airport_name = ? LIMIT 1";
  $stmt_airport = $conn->prepare($sql_airport);
  $stmt_airport->bind_param("s", $airportName);
  $stmt_airport->execute();
  $result_airport = $stmt_airport->get_result();
  $row = $result_airport->fetch_assoc();
  $stmt_airport->close();
  return $row ? (int) $row['price'] : 0;
}

// select flight details
$sql_flight = "SELECT * FROM Flights WHERE id = ?";
$stmt_flight = $conn->prepare($sql_flight);
$stmt_flight->bind_param("i", $flight_id);
$stmt_flight->execute();
$result_flight = $stmt_flight->get_result();
$flight = $result_flight->fetch_assoc();
$stmt_flight->close();

if (!$flight)
  exit("Flight not found.");

// get airport prices (source and destination)
$srcPrice = getAirportPrice($conn, $flight['source']);
$dstPrice = getAirportPrice($conn, $flight['destination']);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $business_seats = (int) ($_POST['business_seats'] ?? 0);
  $economic_seats = (int) ($_POST['economic_seats'] ?? 0);
  $bag_number = (int) ($_POST['bag_number'] ?? 0);

  // check if user already booked this flight
  $check_sql = "SELECT id FROM UserFlights WHERE user_id = ? AND flight_id = ?";
  $check_stmt = $conn->prepare($check_sql);
  $check_stmt->bind_param('ii', $user_id, $flight_id);
  $check_stmt->execute();
  $check_result = $check_stmt->get_result();
  if ($check_result->num_rows > 0) {
    set_flash_message("You have already booked this flight!", "warning");
    header("Location: booked.php");
    exit;
    $check_stmt->close();
  } else {
    // check seats and bag availability
    if ($business_seats > $flight['business_seat'] || $economic_seats > $flight['economy_seat'] || $bag_number > $flight['bag_number']) {
      set_flash_message("Not enough seats or bags available!", "warning");
      header("Location: book.php?flight_id=$flight_id");
      exit;
    } else {
      // calculate price of booking
      if ($business_seats === 0 && $economic_seats === 0 && $bag_number === 0) {
        $price = $srcPrice + $dstPrice + 100;
      } else {
        $price = $srcPrice + $dstPrice + (100 * $economic_seats) + (200 * $business_seats) + (50 * $bag_number);
      }

      // insert booking into UserFlights
      $insert_sql = "INSERT INTO UserFlights (user_id, flight_id, business_seat, economy_seat, bag_number, price)
                  VALUES (?, ?, ?, ?, ?, ?)";
      $insert_stmt = $conn->prepare($insert_sql);

      $insert_stmt->bind_param('iiiiid', $user_id, $flight_id, $business_seats, $economic_seats, $bag_number, $price);

      $insert_stmt->execute();
      $insert_stmt->close();

      // update available seats and bags in Flights table after booking
      $upd_seats = "UPDATE Flights 
                          SET business_seat = business_seat - ?, economy_seat = economy_seat - ?, bag_number = bag_number - ?
                          WHERE id = ?";
      $upd_stmt = $conn->prepare($upd_seats);
      $upd_stmt->bind_param('iiii', $business_seats, $economic_seats, $bag_number, $flight_id);
      $upd_stmt->execute();
      $upd_stmt->close();


      // set success message and redirect to booked page
      set_flash_message("Booking successful!", "success");
      header("Location: Booked.php");
      exit;
    }

  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Book Flight - Flighty</title>
  <link rel="stylesheet" href="./style/booking_style.css">
</head>

<body>
  <div class="navbar">
    <div class="logo">Flighty</div>
    <div class="links">
      <a href="mainPageUser.php">Flights</a>
      <a href="booked.php">Booked</a>
      <button class="account-btn" onclick="window.location.href = 'accountUser.php' ">Account</button>
    </div>
  </div>

  <div class="containerBackground">

      <!-- Display flash message if exists -->
  <?php
  if (isset($_SESSION['flash'])) {
    $message = htmlspecialchars($_SESSION['flash']);
    $type = htmlspecialchars($_SESSION['flash_type'] ?? 'success');

    echo "<div class='flash flash-{$type}'>{$message}</div>";

    // Clear flash message after displaying
    unset($_SESSION['flash'], $_SESSION['flash_type']);
  }
  ?>

    <div class="container">
      <h2 class="title">Book Flight</h2>

      <div class="form">
        <div>
          <label>Flight Name:</label>
          <input type="text" value="<?= htmlspecialchars($flight['flight_name']) ?>" readonly>

          <label>Source:</label>
          <input type="text" value="<?= htmlspecialchars($flight['source']) ?>" readonly>

          <label>Destination:</label>
          <input type="text" value="<?= htmlspecialchars($flight['destination']) ?>" readonly>

          <label>Date:</label>
          <input type="text" value="<?= htmlspecialchars($flight['date']) ?>" readonly>

          <label>Time:</label>
          <input type="text" value="<?= htmlspecialchars($flight['time']) ?>" readonly>
        </div>

        <div>
          <label>Business seat:</label>
          <input id="business" name="business_seats" type="number" min="0" max="<?php echo $flight['business_seat'] ?>"
            value="0">

          <label>Economy seat:</label>
          <input id="economic" name="economic_seats" type="number" min="0" max="<?php echo $flight['economy_seat'] ?>"
            value="1">

          <label>Bag number:</label>
          <input id="bags" name="bag_number" type="number" min="0" max="<?php echo $flight['bag_number'] ?>" value="0">

          <label>Price:</label>
          <input id="price" type="text" value="" readonly disabled>
        </div>
      </div>

      <form method="POST" id="bookForm">
        <input type="hidden" name="business_seats" id="h_business" value="0">
        <input type="hidden" name="economic_seats" id="h_economic" value="0">
        <input type="hidden" name="bag_number" id="h_bags" value="0">
        <button class="book-btn" type="submit">Book</button>
      </form>

    </div>
  </div>

  <script>
    // JavaScript to compute price dynamically
    const srcPrice = <?= json_encode($srcPrice) ?>;
    const dstPrice = <?= json_encode($dstPrice) ?>;

    const business = document.getElementById('business');
    const economic = document.getElementById('economic');
    const bags = document.getElementById('bags');
    const priceInput = document.getElementById('price');
    const h_business = document.getElementById('h_business');
    const h_economic = document.getElementById('h_economic');
    const h_bags = document.getElementById('h_bags');

    function computePrice() {
      const b = parseInt(business.value || 0, 10);
      const e = parseInt(economic.value || 0, 10);
      const bag = parseInt(bags.value || 0, 10);

      let price;
      if (b === 0 && e === 0 && bag === 0) {
        price = srcPrice + dstPrice + 100;
      } else {
        price = srcPrice + dstPrice + (100 * e) + (200 * b) + (50 * bag);
      }
      priceInput.value = price;
      // update hidden inputs for form submission
      h_business.value = b;
      h_economic.value = e;
      h_bags.value = bag;
    }

    // event listeners
    [business, economic, bags].forEach(el => {
      el.addEventListener('input', computePrice);
    });

    // initial price computation
    computePrice();
  </script>
</body>

</html>