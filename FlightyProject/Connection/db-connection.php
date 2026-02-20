<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "airlinereservation";
$port = 3307;

$conn = new mysqli($servername, $username, $password, $database, $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to set flash message
function set_flash_message($message, $type = 'info') {
    $_SESSION['flash'] = $message;
    $_SESSION['flash_type'] = $type;   // success, error, info, warning
}

?>