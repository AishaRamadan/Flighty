<?php
// Edit_booked.php 
session_start();
include("./Connection/db-connection.php");
$user_id = $_SESSION['user_id'];

$booking_id = isset($_GET['booking_id']) ? (int) $_GET['booking_id'] : null;
if (!$booking_id)
  exit("No booking selected.");

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

// select all booked flight's details
$sql_booking = "
    SELECT uf.id as booking_id, uf.business_seat, uf.economy_seat, uf.bag_number, uf.price,
          f.id as flight_id, f.flight_name, f.source, f.destination, f.date, f.time
    FROM UserFlights uf
    JOIN Flights f ON uf.flight_id = f.id
    WHERE uf.user_id = ? AND uf.id = ?
";
$stmt_booking = $conn->prepare($sql_booking);
$stmt_booking->bind_param('ii', $user_id, $booking_id);
$stmt_booking->execute();
$result_booking = $stmt_booking->get_result();
$booking = $result_booking->fetch_assoc();
$stmt_booking->close();

// if booking not found --> redirect with error
if (!$booking) {
  set_flash_message("Booking not found or you don't have permission.", "error");
  header("Location: Booked.php");
  exit;
}

// get airport prices (source and destination)
$srcPrice = getAirportPrice($conn, $booking['source']);
$dstPrice = getAirportPrice($conn, $booking['destination']);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $new_business = (int) ($_POST['business_seats'] ?? 0);
  $new_economic = (int) ($_POST['economic_seats'] ?? 0);
  $new_bags = (int) ($_POST['bag_number'] ?? 0);

  // check if flight date/time has passed
  $flightDate = strtotime($booking['date'] . ' ' . $booking['time']);

  if (time() > $flightDate) {
    // date/time has passed
    set_flash_message("Cannot edit after flight time.", "error");
    header("Location: Edit_booked.php?booking_id=$booking_id");
    exit;
  } else {
    // get current available seats and bags from Flights table
    $sql_flight = "SELECT business_seat, economy_seat, bag_number FROM Flights WHERE id = ?";
    $stmt_flight = $conn->prepare($sql_flight);
    $stmt_flight->bind_param("i", $booking['flight_id']);
    $stmt_flight->execute();
    $flight_data = $stmt_flight->get_result()->fetch_assoc();
    $stmt_flight->close();

    // avalaible seats and bags after returning old booking
    $available_business = $flight_data['business_seat'] + $booking['business_seat'];
    $available_economic = $flight_data['economy_seat'] + $booking['economy_seat'];
    $available_bags = $flight_data['bag_number'] + $booking['bag_number'];


    // check available seats and bags
    if ($new_business > $available_business || $new_economic > $available_economic || $new_bags > $available_bags) {
      // not enough seats or bags --> show error
      set_flash_message("Not enough seats or bags available!", "warning");
      header("Location: Edit_booked.php?booking_id=$booking_id");
      exit;
    } else {
      // calculate new price and update booking
      if ($new_business === 0 && $new_economic === 0 && $new_bags === 0) {
        $price = $srcPrice + $dstPrice + 100;
      } else {
        $price = $srcPrice + $dstPrice + (100 * $new_economic) + (200 * $new_business) + (50 * $new_bags);
      }

      // update booking in UserFlights
      $upd_sql = "UPDATE UserFlights
                              SET business_seat = ?, economy_seat = ?, bag_number = ?, price = ?
                              WHERE id = ? AND user_id = ?";
      $upd_stmt = $conn->prepare($upd_sql);

      $upd_stmt->bind_param('iiidii', $new_business, $new_economic, $new_bags, $price, $booking_id, $user_id);
      if ($upd_stmt->execute()) {
        // update available seats and bags in Flights table after editing
        $upd_flight = "UPDATE Flights
                          SET business_seat = business_seat + ? - ?,
                              economy_seat = economy_seat + ? - ?,
                              bag_number = bag_number + ? - ?
                          WHERE id = ?";
        $upd_stmt2 = $conn->prepare($upd_flight);
        $upd_stmt2->bind_param(
          'iiiiiii',
          $booking['business_seat'],
          $new_business,
          $booking['economy_seat'],
          $new_economic,
          $booking['bag_number'],
          $new_bags,
          $booking['flight_id']
        );
        $upd_stmt2->execute();
        $upd_stmt2->close();

        // success message and redirect to booked page
        set_flash_message("Booking updated successfully!", "success");
        header("Location: Booked.php");
        exit;
      } else {
        // failed to update booking --> show error
        set_flash_message("Failed to update booking.", "error");
        header("Location: Edit_booked.php?booking_id=$booking_id");
        exit;
      }
      $upd_stmt->close();

    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Edit Booking - Flighty</title>
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
      <h2 class="title">Edit Booked Flight</h2>

      <div class="form">
        <div>
          <label>Flight Name:</label>
          <input type="text" value="<?= htmlspecialchars($booking['flight_name']) ?>" readonly>

          <label>Source:</label>
          <input type="text" value="<?= htmlspecialchars($booking['source']) ?>" readonly>

          <label>Destination:</label>
          <input type="text" value="<?= htmlspecialchars($booking['destination']) ?>" readonly>

          <label>Date:</label>
          <input type="text" value="<?= htmlspecialchars($booking['date']) ?>" readonly>

          <label>Time:</label>
          <input type="text" value="<?= htmlspecialchars($booking['time']) ?>" readonly>
        </div>

        <div>
          <label>Business seat:</label>
          <input id="business" name="business_seats" type="number" min="0"
            value="<?= (int) $booking['business_seat'] ?>">

          <label>Economy seat:</label>
          <input id="economic" name="economic_seats" type="number" min="0"
            value="<?= (int) $booking['economy_seat'] ?>">

          <label>Bag number:</label>
          <input id="bags" name="bag_number" type="number" min="0" value="<?= (int) $booking['bag_number'] ?>">

          <label>Price:</label>
          <input id="price" type="text" value="<?= (int) $booking['price'] ?>" readonly>
        </div>
      </div>

      <form method="POST" id="editForm">
        <input type="hidden" name="business_seats" id="h_business" value="<?= (int) $booking['business_seat'] ?>">
        <input type="hidden" name="economic_seats" id="h_economic" value="<?= (int) $booking['economy_seat'] ?>">
        <input type="hidden" name="bag_number" id="h_bags" value="<?= (int) $booking['bag_number'] ?>">
        <button class="book-btn" type="submit">Edit Flight</button>
      </form>

    </div>
  </div>

  <script>
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
      h_business.value = b;
      h_economic.value = e;
      h_bags.value = bag;
    }

    [business, economic, bags].forEach(el => el.addEventListener('input', computePrice));
    computePrice();
  </script>
</body>

</html>