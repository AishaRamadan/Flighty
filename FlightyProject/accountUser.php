<?php
session_start();
include "./connection/db-connection.php";

$user_id = $_SESSION['user_id'];

// $id = intval($_GET['id']);
$user = $conn->query("SELECT * FROM users WHERE id = $user_id")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'logout') {
    
        session_unset();
        session_destroy();

        header("Location: login.php");
        exit;
    }

if ($action === 'update') {
$firstName = $_POST['FName'];
$lastName = $_POST['LName'];
$Email = $_POST['Email'];
$Phone =$_POST['Phone'];
$Pass = $_POST['Pass'];
$Address = $_POST['Address'];
$Zip =$_POST['Zip'];
$City = $_POST['City'];
$State = $_POST['state'];
$Country =$_POST['Country'];
$Gender = $_POST['Gender'];
$Age = $_POST['Age'];

    $sql = "UPDATE users SET 
                first_name ='$firstName',
                last_name ='$lastName',
                email ='$Email',
                phone ='$Phone',
                password ='$Pass',
                address = '$Address',
                zip = '$Zip',
                city = '$City',
                state = '$State',
                country = '$Country',
                gender ='$Gender',
                age = '$Age'

            WHERE id = $user_id";

    $conn->query($sql);
    header("Location: MainPageUser.php");
    exit;
    }

    if ($action === 'delete') {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
           
            session_unset();
            session_destroy();

            header("Location: login.php"); 
            exit;
        } else {
            echo "Error deleting account: " . $conn->error;
    }
  }

    
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
     <meta charset="UTF-8">
    <title>Flighty - User Account</title>
     <link rel="stylesheet" href="./style/account.css">
</head>
<body>
     
    <div class="navbar">
    <div class="logo">Flighty</div>
    <div class="links">
        <a href="mainPageUser.php">Flights</a>
         <a href="booked.php">Booked</a>
         <button class="account-btn">Account</button>
    </div>
</div>
<div class="containerBackground">
   <div class="container">
    <h2 class="title">My Account</h2>
    <form method="post" action="">
      <div class="formImage">
       <div class="SignUpForm">
      <label>Enter Your Name</label>
      <input type="text" placeholder="First Name" name="FName" value="<?= htmlspecialchars($user['first_name']) ?>" required>
      <input type="text" placeholder="Last Name" name="LName" value="<?= htmlspecialchars($user['last_name']) ?>" required>
      <label>Enter Your Email & Phone</label>
      <input type="email" placeholder="Email" name="Email" value="<?= htmlspecialchars($user['email']) ?>" required>
      <input type="tel" placeholder="Phone" pattern="01[0-2,5][0-9]{8}" name="Phone" value="<?= htmlspecialchars($user['phone']) ?>" required>
      <label>Enter Your Password</label>
      <input type="password" placeholder="Password" name="Pass" value="<?= htmlspecialchars($user['password']) ?>" required>
    </div>
    
      <div class="SignUpForm2">
        <label>Enter Your Address</label>
      <input type="text" placeholder="Address" name="Address" value="<?= htmlspecialchars($user['address']) ?>" required>
      <input type="text" placeholder="Zip/Postal" name="Zip" value="<?= htmlspecialchars($user['zip']) ?>" required>
      <input type="text" placeholder="City" name="City" value="<?= htmlspecialchars($user['city']) ?>" required>
      <input type="text" placeholder="state" name="state" value="<?= htmlspecialchars($user['state']) ?>" required>
      <input type="text" placeholder="Country" name="Country" value="<?= htmlspecialchars($user['country']) ?>" required>
      <label>Enter Your Age</label>
      <input type="number" placeholder="Age" name="Age" value="<?= htmlspecialchars($user['age']) ?>" required>
      <label>Select Your Gender</label>
      <div class="gender">
        <label>Male</label>
      <input type="radio" name="Gender" id="male" value="<?= htmlspecialchars($user['gender']) ?>" <?= $user['gender'] === 'Male' ? 'checked' : '' ?>>
       <label>Female</label>
      <input type="radio" name="Gender" id="Female" value="<?= htmlspecialchars($user['gender']) ?>" <?= $user['gender'] === 'Female' ? 'checked' : '' ?>>
      </div>
      </div>
      
      
    
    <div class="image2">
      <img src="./assets/MyAccount.png" alt="Update">
    </div>
    </div>
    <div class="formButton">
       <button type="submit" name="action" value="update" class="Update_btn">Update</button>
       <button type="submit" name="action" value="logout" class="Logout_btn">Logout</button>
       <button type="submit" name="action" value="delete" class="Delete_btn">Delete</button>
    </div>
   
    </form>
    
   
   </div>

    
 
  </div >
 
</div>
 

</body>  
</html>  

<?php
$conn->close();
?>