<?php
session_start();
include('db_connection.php');

$message = "";
$reset_link_expiry = 900; // 15 minutes expiration for the token

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST['email'])) {
        $message = "Email is required.";
    } else {
        $email = mysqli_real_escape_string($conn, $_POST['email']);

        $query_farmer = "SELECT * FROM farmer_registration WHERE email = '$email' LIMIT 1";
        $result_farmer = mysqli_query($conn, $query_farmer);

        $query_buyer = "SELECT * FROM buyer_registration WHERE email = '$email' LIMIT 1";
        $result_buyer = mysqli_query($conn, $query_buyer);

        if (!$result_farmer && !$result_buyer) {
            $message = "Database error: " . mysqli_error($conn);
        } else {
            $user = null;
            $user_type = null;

            if ($user = mysqli_fetch_assoc($result_farmer)) {
                $user_type = 'farmer';
            } else if ($user = mysqli_fetch_assoc($result_buyer)) {
                $user_type = 'buyer';
            }

            if ($user) {
                // Generate the reset token
                $token = bin2hex(random_bytes(32));

                // Store token and user details in the session
                $_SESSION['reset_token'] = $token;
                $_SESSION['reset_email'] = $email;
                $_SESSION['user_type'] = $user_type;
                $_SESSION['token_timestamp'] = time() + $reset_link_expiry;

                // Redirect to the reset password page
                header("Location: reset_password.php");
                exit; // Stop further script execution after redirection
            } else {
                $message = "No account found with this email.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Forgot Password</title>


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body>


    <?php if ($message): ?>
    <p>
        <?php echo $message; ?>
    </p>
    <?php endif; ?>
    <div class="container">
        <!-- Content here -->
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <label for="email">Enter your email:</label>
            <input type="email" name="email" id="email" class="form-control form-control-lg" required><br><br>

            <button type="submit" class="btn btn-primary btn-lg ms-2">Reset password</button>
            <button type="" class="btn btn-danger btn-lg ms-2"><a href="login.php">Back</a></button>
        </form>
    </div>

</body>

</html>