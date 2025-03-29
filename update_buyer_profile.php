<?php
session_start();

// Check if the user is logged in and is a buyer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'buyer') {
    header('Location: login.php');
    exit;
}

include('db_connection.php'); // Make sure this path is correct

$buyer_id = $_SESSION['user_id'];

// Fetch current buyer information
$query = "SELECT * FROM buyer_registration WHERE buyer_id = ?"; // Use prepared statement
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $buyer_id); // "i" for integer
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$result) {
    die('Query failed: ' . mysqli_error($conn));
}

$buyer_data = mysqli_fetch_assoc($result);

if (!$buyer_data) {
    die("Buyer data not found.");
}

// Handle profile update form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $buyer_name = mysqli_real_escape_string($conn, $_POST['buyer_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);
    $shipping_address = mysqli_real_escape_string($conn, $_POST['shipping_address']);

    // Prepared statement for update
    $update_query = "UPDATE buyer_registration 
                     SET buyer_name = ?, 
                         email = ?, 
                         phone_number = ?, 
                         shipping_address = ? 
                     WHERE buyer_id = ?";
    $stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($stmt, "ssssi", $buyer_name, $email, $phone_number, $shipping_address, $buyer_id); // "ssssi" for strings and integer

    if (mysqli_stmt_execute($stmt)) {
        header("Location: buyer_dashboard.php?message=Profile updated successfully!");
        exit;
    } else {
        header("Location: update_buyer_profile.php?message=Error updating profile. Please try again.&error=" . mysqli_error($conn));
        exit;
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Update Profile</title>
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
        <h1>Update Profile</h1>

        <?php if (isset($_GET['message'])): ?>
        <p>
            <?php echo htmlspecialchars($_GET['message']); ?>
        </p>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
        <p style="color:red;">Error:
            <?php echo htmlspecialchars($_GET['error']); ?>
        </p>
        <?php endif; ?>

        <form action="update_buyer_profile.php" method="POST">
            <label for="buyer_name">Name:</label>
            <input type="text" id="buyer_name" name="buyer_name"
                value="<?php echo htmlspecialchars($buyer_data['buyer_name']); ?>" class="form-control form-control-lg"
                required><br><br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($buyer_data['email']); ?>"
                class="form-control form-control-lg" required><br><br>

            <label for="phone_number">Phone Number:</label>
            <input type="text" id="phone_number" name="phone_number"
                value="<?php echo htmlspecialchars($buyer_data['phone_number']); ?>"
                class="form-control form-control-lg" required><br><br>

            <label for="shipping_address">Shipping Address:</label>
            <textarea id="shipping_address" name="shipping_address" class="form-control form-control-lg"
                required><?php echo htmlspecialchars($buyer_data['shipping_address']); ?></textarea><br><br>

            <input type="submit" name="update_profile" value="Update Profile" class="btn btn-primary btn-lg ms-2">
        </form>
        <a href="buyer_dashboard.php">Back to Dashboard</a>
    </div>
</body>

</html>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/mdb-ui-kit@8.2.0/js/mdb.umd.min.js"></script>