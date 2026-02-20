<!-- <?php
echo "Helooooo from signUp.php :)";
?> -->

<?php
session_start();
include "./Connection/db-connection.php";

$errors = [];
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = trim($_POST['FName']);
    $lastName = trim($_POST['LName']);
    $Email = trim($_POST['Email']);
    $Phone = trim($_POST['Phone']);
    $Pass = trim($_POST['Pass']);
    $ConfirmPass = trim($_POST['ConfirmPass']);
    $Address = trim($_POST['Address']);
    $Zip = trim($_POST['Zip']);
    $City = trim($_POST['City']);
    $State = trim($_POST['state']);
    $Country = trim($_POST['Country']);
    $Gender = trim($_POST['gender']);
    $Age = trim($_POST['Age']);


    if (!$firstName)
        $errors[] = "First name is required.";
    if (!$lastName)
        $errors[] = "Last name is required.";
    if (!$Email)
        $errors[] = "Email is required.";
    if (!$Phone)
        $errors[] = "Phone is required.";
    if (!$Pass)
        $errors[] = "Password is required.";
    if (!$ConfirmPass)
        $errors[] = "Confirm Password is required.";
    if (!$Address)
        $errors[] = "Address is required.";
    if (!$Zip)
        $errors[] = "Zip code is required.";
    if (!$City)
        $errors[] = "City is required.";
    if (!$State)
        $errors[] = "State is required.";
    if (!$Country)
        $errors[] = "Country is required.";
    if (!$Age)
        $errors[] = "Age is required.";
    if (!$Gender)
        $errors[] = "Gender is required.";


    if ($Email && !filter_var($Email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if ($Phone && !preg_match("/^01[0-2,5][0-9]{8}$/", $Phone)) {
        $errors[] = "Phone number must start with 01 and be 10 digits .";
    }

    if (strlen($firstName) > 255)
        $errors[] = "First name is too long.";
    if (strlen($lastName) > 255)
        $errors[] = "Last name is too long.";
    if (strlen($Email) > 255)
        $errors[] = "Email is too long.";
    if (strlen($Address) > 255)
        $errors[] = "Address name is too long.";
    if (strlen($Zip) > 255)
        $errors[] = "Zip name is too long.";
    if (strlen($City) > 255)
        $errors[] = "City name is too long.";
    if (strlen($State) > 255)
        $errors[] = "State name is too long.";
    if (strlen($Country) > 255)
        $errors[] = "Country name is too long.";
    if (strlen($Pass) < 8)
        $errors[] = "Password must be at least 8 characters.";

    if ($Pass !== $ConfirmPass) {
        $errors[] = "Passwords don't match";
    }


    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $Email);
    $check->execute();
    $check_result = $check->get_result();

    if ($check_result->num_rows > 0) {
        $errors[] = "This email is already registered.";
    }


    if (empty($errors)) {
        $stmt = $conn->prepare("
            INSERT INTO users (first_name, last_name, phone, address, city, state, zip, email, country, password, gender, age)
            VALUES (?, ?, ?, ?, ?, ?, ? , ?, ? , ? , ?, ?)
        ");
        $stmt->bind_param("sssssssssssi", $firstName, $lastName, $Phone, $Address, $City, $State, $Zip, $Email, $Country, $Pass, $Gender, $Age);

        if ($stmt->execute()) {
            $success = "Account created successfully!";
            $_SESSION['user_id'] = $conn->insert_id; 
            header("Location: MainPageUser.php");
            exit;

        } else {
            $errors[] = "Database error: " . $conn->error;
        }
    }
}
?>
<?php if (!empty($errors)): ?>
    <div style="background:#ffd5d5;padding:12px;border-radius:4px;margin-bottom:15px;color:#900;">
        <strong>Please fix the following errors:</strong>
        <ul>
            <?php foreach ($errors as $e): ?>
                <li><?= $e ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div style="background:#d5ffd5;padding:12px;border-radius:4px;margin-bottom:15px;color:#060;">
        <?= $success ?>
    </div>
<?php endif; ?>





<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Flighty - Sign Up</title>
    <link rel="stylesheet" href="./style/styleSignUp.css">
</head>

<body>
    <div class="navbar">
        <div class="logo">Flighty</div>
        <div class="links">
            <a href="mainPage.php">Flights</a>
            <a href="login.php">Sign in</a>
            <button class="SignUp_btn">Sign Up</button>
        </div>

    </div>
    <div class="containerBackground">
        <div class="container">
            <h2 class="title">Sign Up</h2>
            <form method="post" action="">
                <div class="formImage">
                    <div class="SignUpForm">
                        <label>Enter Your Name</label>
                        <input type="text" placeholder="First Name" name="FName" required
                            value="<?php echo isset($firstName) ? htmlspecialchars($firstName) : ''; ?>">
                        <input type="text" placeholder="Last Name" name="LName" required
                            value="<?php echo isset($lastName) ? htmlspecialchars($lastName) : ''; ?>">
                        <label>Enter Your Email & Phone </label>
                        <input type="email" placeholder="Email" name="Email" required
                            value="<?php echo isset($Email) ? htmlspecialchars($Email) : ''; ?>">
                        <input type="tel" placeholder="Phone" pattern="01[0-2,5][0-9]{8}" name="Phone" required value="<?php echo isset($Phone)? $Phone:'' ?>">
                        <label>Enter Your Password</label>
                        <input type="password" placeholder="Password" name="Pass" required
                            value="<?php echo isset($Pass) ? htmlspecialchars($Pass) : ''; ?>">
                        <input type="password" placeholder="Confirm Password" name="ConfirmPass" required
                            value="<?php echo isset($ConfirmPass) ? htmlspecialchars($ConfirmPass) : ''; ?>">
                    </div>

                    <div class="SignUpForm2">
                        <label>Enter Your Address</label>
                        <input type="text" placeholder="* Address" name="Address" required
                            value="<?php echo isset($Address) ? htmlspecialchars($Address) : ''; ?>">
                        <input type="text" placeholder="* Zip/Postal" name="Zip" required
                            value="<?php echo isset($Zip) ? htmlspecialchars($Zip) : ''; ?>">
                        <input type="text" placeholder="* City" name="City" required
                            value="<?php echo isset($City) ? htmlspecialchars($City) : ''; ?>">
                        <input type="text" placeholder="* state" name="state" required
                            value="<?php echo isset($State) ? htmlspecialchars($State) : ''; ?>">
                        <input type="text" placeholder="* Country" name="Country" required
                            value="<?php echo isset($Country) ? htmlspecialchars($Country) : ''; ?>">
                        <label>Enter Your Age</label>
                        <input type="number" placeholder="Age" name="Age" required
                            value="<?php echo isset($Age) ? htmlspecialchars($Age) : ''; ?>">
                        <label>Select Your Gender</label>
                        <div class="gender">
                            <label for="male">Male</label>
                            <input type="radio" name="gender" id="male" value="Male"
                               <?php echo (isset($Gender) && $Gender == "Male") ? 'checked' : ''; ?>>
                            <label for="Female">Female</label>
                            <input type="radio" name="gender" id="Female" value="Female"
                               <?php echo (isset($Gender) && $Gender == "Female") ? 'checked' : ''; ?>>
                        </div>
                    </div>



                    <div class="image2">
                        <img src="./assets/SignUp.png" alt="sign up">
                    </div>
                </div>
                <div class="formButton">
                    <button type="submit" name="submit" value="submit" class="Sign_btn">Sign Up</button>
                </div>

            </form>


        </div>


    </div>

    </div>


</body>

</html>


<?php
$conn->close();
?>