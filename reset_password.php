<?php
session_start();
include('db_connection.php');

$message = "";
$is_reset = isset($_SESSION['reset_token']) && isset($_SESSION['reset_email']) && isset($_SESSION['user_type']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST['new_password']) || empty($_POST['confirm_password'])) {
        $message = "New password and confirm password are required.";
    } elseif ($_POST['new_password'] !== $_POST['confirm_password']) {
        $message = "New passwords do not match.";
    } else {
        $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
        $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);

        if ($is_reset) {
            // Reset Password Logic
            $email = $_SESSION['reset_email'];
            $user_type = $_SESSION['user_type'];

            if ($user_type == 'buyer') {
                $table_name = "buyer_registration";
                $id_column = "buyer_id"; // Correct ID Column
                $email_column = "email"; // Correct Email Column
                $password_column = "password";
            } elseif ($user_type == 'farmer') {
                $table_name = "farmer_registration";
                $id_column = "farmer_id"; // Correct ID Column
                $email_column = "email"; // Correct Email Column
                $password_column = "password";
            } else {
                $message = "Invalid user type.";
                exit;
            }

            $check_query = "SELECT 1 FROM $table_name WHERE $email_column = '$email'";
            $check_result = mysqli_query($conn, $check_query);

            if (!$check_result) {
                die("Database error: " . mysqli_error($conn));
            }

            if (mysqli_num_rows($check_result) == 0) {
                $message = "Email not found in the database.";
            } else {
                $update_query = "UPDATE $table_name SET $password_column = '$hashed_new_password' WHERE $email_column = '$email'";

                if (mysqli_query($conn, $update_query)) {
                    unset($_SESSION['reset_token']);
                    unset($_SESSION['reset_email']);
                    unset($_SESSION['token_timestamp']);
                    unset($_SESSION['user_type']);
                    $message = "Your password has been successfully reset! You can now <a href='login.php'>login</a>.";
                } else {
                    $message = "Error updating password: " . mysqli_error($conn);
                }
            }
        } else {
            // Change Password Logic (User is logged in)
            if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
                $message = "User not logged in.";
                exit;
            }
            $user_id = $_SESSION['user_id'];
            $user_type = $_SESSION['role'];

            if ($user_type == 'buyer') {
                $table_name = "buyer_registration";
                $id_column = "buyer_id";
                $password_column = "password";
            } elseif ($user_type == 'farmer') {
                $table_name = "farmer_registration";
                $id_column = "farmer_id";
                $password_column = "password";
            } else {
                $message = "Invalid user type.";
                exit;
            }
            $current_password = mysqli_real_escape_string($conn, $_POST['current_password']);

            $verify_query = "SELECT $password_column FROM $table_name WHERE $id_column = '$user_id'";
            $verify_result = mysqli_query($conn, $verify_query);
            if (!$verify_result) {
                die("Database error: " . mysqli_error($conn));
            }
            $row = mysqli_fetch_assoc($verify_result);

            if (!password_verify($current_password, $row[$password_column])) {
                $message = "Incorrect current password.";
            } else {
                $update_query = "UPDATE $table_name SET $password_column = '$hashed_new_password' WHERE $id_column = '$user_id'";
                if (mysqli_query($conn, $update_query)) {
                    $message = "Password updated successfully!";
                } else {
                    $message = "Error updating password: " . mysqli_error($conn);
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>
        <?php echo $is_reset ? "Reset Password" : "Change Password"; ?>
    </title>
    <link rel='stylesheet'
        href='https://cdn-uicons.flaticon.com/2.6.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet" />
    <!-- MDB -->
    <link href="https://cdn.jsdelivr.net/npm/mdb-ui-kit@8.2.0/css/mdb.min.css" rel="stylesheet" />
</head>

<body>
    <div class="container">
        <h1>
            <?php echo $is_reset ? "Reset Password" : "Change Password"; ?>
        </h1>

        <?php if ($message): ?>
        <p>
            <?php echo $message; ?>
        </p>
        <?php endif; ?>

        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <?php if (!$is_reset): ?>
            <label for="current_password">Current Password:</label>
            <input type="password" name="current_password" id="current_password" class="form-control form-control-lg"
                required><br><br>
            <?php endif; ?>

            <label for="new_password">New Password:</label>
            <input type="password" name="new_password" id="new_password" class="form-control form-control-lg"
                required><br><br>

            <label for="confirm_password">Confirm New Password:</label>
            <input type="password" name="confirm_password" id="confirm_password" class="form-control form-control-lg"
                required><br><br>

            <button type="submit" class="btn btn-primary btn-lg ms-2">
                <?php echo $is_reset ? "Reset Password" : "Change Password"; ?>
            </button>
            <a href="buyer_dashboard.php">Back to Dashboard</a>

        </form>
    </div>
</body>

</html>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/mdb-ui-kit@8.2.0/js/mdb.umd.min.js"></script>