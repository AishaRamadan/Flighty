<!-- <?php
echo "Helooooo from login.php :)";
?>
 -->

<?php
session_start();
include "./Connection/db-connection.php";

$errors_msg = [];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {

    $email = trim($_POST["Email"]);
    $password = trim($_POST["Password"]);

    if (!$email)
        $errors_msg["Email"] = "Email is required.";
    if (!$password)
        $errors_msg["Password"] = "Password is required.";

    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors_msg["Email"] = "Invalid email format.";
    }

    if ($password && strlen($password) < 8) {
        $errors_msg["Password"] = "Password must be at least 6 characters.";
    }

    if (empty($errors_msg)) {
        $stmt = $conn->prepare("SELECT * FROM Users WHERE email=? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();

            if ($user['password'] === $password) {

                $_SESSION['user_id'] = $user['id'];
                $role = $user['role'];
                $user_name = $user['first_name'] . " " . $user['last_name'];
                if ($role == 'admin') {
                    //     echo "<script>
                    // alert('Login successfully, welcome $user_name :)' );
                    // window.location.href = 'MainPageAdmin.php';
                    // </script>";
                    echo "<script>
                            alert('Login Successfully :)');
                         </script>";
                    header("Location: MainPageAdmin.php");
                    exit;

                } else {
                    //     echo "<script>
                    // alert('Login successfully :) ');
                    // window.location.href = 'MainPageUser.php';
                    // </script>";
                    echo "<script>
                            alert('Login Successfully :)');
                         </script>";
                    header("Location: MainPageUser.php");
                    exit;

                }
            } else {
                echo "<script>
                alert('Incorrect Password');
                </script>";
            }
        } else {
            $errors_msg["Email"] = "User not found!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Flighty - Login</title>
    <link rel="stylesheet" href="./style/login.css">
</head>

<body>
    <div class="navbar">
        <div class="logo">Flighty</div>
        <div class="links">
            <a href="mainPage.php">Flights</a>
            <a href="login.php">Sign in</a>
            <button class="SignUp_btn" onclick="window.location.href='signUp.php'">Sign Up</button>
        </div>
    </div>
    <div class="containerBackground">
        <h2 class="login_title">Login</h2>

        <!-- <?php if (!empty($success_msg)): ?>
                <p class="success-msg"><?php echo $success_msg; ?></p>
            <?php endif; ?> -->

        <form action="" method="post">
            <div class="formImage">
                <div class="LoginForm">
                    <label>Enter Your Email</label>
                    <input type="text" placeholder="Email" name="Email"
                        value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
                    <span class="error-msg"><?php if (isset($errors_msg["Email"]))
                        echo $errors_msg["Email"]; ?></span>

                    <label>Enter Your password </label>
                    <input type="password" placeholder="Password" name="Password"
                        value="<?php echo isset($password) ? htmlspecialchars($password) : ''; ?>">
                    <span class="error-msg"><?php if (isset($errors_msg["Password"]))
                        echo $errors_msg["Password"]; ?></span>

                    <div class="formButton">
                        <button type="submit" class="log_btn" name="login">Login</button>
                    </div>

                </div>

                <div class="image">
                    <img src="./assets/login.png" alt="login">
                </div>
            </div>

        </form>

    </div>


</body>

</html>
<?php
$conn->close();
?>