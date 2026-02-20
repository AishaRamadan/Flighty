<!-- <?php
echo "Helooooo from Booked.php :)";
?> -->

<?php
// Booked.php 
session_start();
include("./Connection/db-connection.php");
$user_id = $_SESSION['user_id'];


// handle delete booking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_booking'])) {
  $booking_id = (int) $_POST['booking_id'];
  $flight_id = (int) $_POST['flight_id'];

  // get booked seats and bags before deletion
  $seat_sql = "SELECT business_seat, economy_seat, bag_number FROM UserFlights WHERE id = ? AND user_id = ?";
  $seat_stmt = $conn->prepare($seat_sql);
  $seat_stmt->bind_param('ii', $booking_id, $user_id);
  $seat_stmt->execute();
  $seat_res = $seat_stmt->get_result();
  $seat_data = $seat_res->fetch_assoc();
  $seat_stmt->close();

  // delete booked flight from UserFlights
  $del_sql = "DELETE FROM UserFlights WHERE user_id = ? AND flight_id = ? AND id = ?";
  $del_stmt = $conn->prepare($del_sql);
  $del_stmt->bind_param('iii', $user_id, $flight_id, $booking_id);
  $del_stmt->execute();
  $del_stmt->close();

  // restore seats and bags back to Flights table and update available seats and bags in after deletion
  if ($seat_data) {
    $upd_seats = "UPDATE Flights 
                      SET business_seat = business_seat + ?, economy_seat = economy_seat + ?, bag_number = bag_number + ?
                      WHERE id = ?";
    $upd_stmt = $conn->prepare($upd_seats);
    $upd_stmt->bind_param('iiii', $seat_data['business_seat'], $seat_data['economy_seat'], $seat_data['bag_number'], $flight_id);
    $upd_stmt->execute();
    $upd_stmt->close();
  }

  set_flash_message("Booking deleted successfully!", "success");
  header("Location: Booked.php");
  exit;
}

// select all booked flight for the user
$sql_bookings = "
    SELECT uf.id as booking_id, uf.business_seat, uf.economy_seat, uf.bag_number, uf.price,
          f.id as flight_id, f.flight_name, f.source, f.destination, f.date, f.time
    FROM UserFlights uf
    JOIN Flights f ON uf.flight_id = f.id
    WHERE uf.user_id = ?
";
$stmt_bookings = $conn->prepare($sql_bookings);
$stmt_bookings->bind_param('i', $user_id);
$stmt_bookings->execute();

$result_bookings = $stmt_bookings->get_result();
$bookings = $result_bookings->fetch_all(MYSQLI_ASSOC);
$stmt_bookings->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Booked Flights - Flighty</title>
  <link rel="stylesheet" href="./style/booking_style.css">
</head>

<body>
  <div class="navbar">
    <div class="logo">Flighty</div>
    <div class="links">
      <a href="mainPageUser.php">Flights</a>
      <a href="Booked.php">Booked</a>
      <button class="account-btn"
        onclick="window.location.href = 'accountUser.php' ">Account</button>
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
      <table class="flight-table">
        <tr>
          <th>flight name</th>
          <th>source</th>
          <th>destination</th>
          <th>price</th>
          <th>date</th>
          <th>time</th>
          <th>Economic seats</th>
          <th>Business seats</th>
          <th>Actions</th>
        </tr>

        <?php if (count($bookings) === 0): ?>
          <tr>
            <td colspan="9">No bookings yet.</td>
          </tr>
        <?php else: ?>
          <?php foreach ($bookings as $b): ?>
            <tr>
              <td><?= htmlspecialchars($b['flight_name']) ?></td>
              <td><?= htmlspecialchars($b['source']) ?></td>
              <td><?= htmlspecialchars($b['destination']) ?></td>
              <td><?= htmlspecialchars($b['price']) ?></td>
              <td><?= htmlspecialchars($b['date']) ?></td>
              <td><?= htmlspecialchars($b['time']) ?></td>
              <td><?= (int) $b['economy_seat'] ?></td>
              <td><?= (int) $b['business_seat'] ?></td>
              <td>
                <a class="update-btn" href="Edit_booked.php?booking_id=<?= (int) $b['booking_id'] ?>">Update</a>

                <form style="display:inline;" method="POST" onsubmit="return confirm('Delete this booking?');">
                  <input type="hidden" name="booking_id" value="<?= (int) $b['booking_id'] ?>">
                  <input type="hidden" name="flight_id" value="<?= (int) $b['flight_id'] ?>">
                  <button class="delete-btn" name="delete_booking" type="submit">Delete</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </table>
    </div>
  </div>
</body>

</html>