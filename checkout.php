<?php
session_start();
include('db_connection.php');

// Check if the user is logged in and is a buyer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'buyer') {
    header('Location: login.php');
    exit;
}

$buyer_id = $_SESSION['user_id'];

// Fetch buyer's cart items
$query = "SELECT bc.cart_id, pm.product_image, pm.product_name, pm.price, bc.quantity, (pm.price * bc.quantity) AS total_price
          FROM buyer_cart bc
          JOIN product_management pm ON bc.product_id = pm.product_id
          WHERE bc.buyer_id = $buyer_id";
$result = mysqli_query($conn, $query);

$total_amount = 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        img {
            max-width: 100px;
            max-height: 100px;
        }

        input[type="number"] {
            width: 50px;
            padding: 5px;
        }
    </style>

</head>

<body>
    <div class="container">
        <h1>Checkout</h1>

        <?php if (mysqli_num_rows($result) > 0): ?>
        <table>
            <tr>
                <th>Product Image</th>
                <th>Product Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total Price</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><img src="<?php echo $row['product_image']; ?>" alt="Product Image"
                        style="width: 100px; height: 100px;"></td>
                <td>
                    <?php echo $row['product_name']; ?>
                </td>
                <td>
                    <?php echo $row['price']; ?>
                </td>
                <td>
                    <?php echo $row['quantity']; ?>
                </td>
                <td>
                    <?php echo $row['total_price']; ?>
                </td>
            </tr>
            <?php $total_amount += $row['total_price']; ?>
            <?php endwhile; ?>
            <tr>
                <td colspan="4"><strong>Total Amount</strong></td>
                <td><strong>
                        <?php echo $total_amount; ?>
                    </strong></td>
            </tr>
        </table>
        <?php else: ?>
        <p>Your cart is empty.</p>
        <?php endif; ?>


        <div style="background-color:rgb(190, 235, 184); padding: 20px; margin-top: 20px; width:50%">

            <h2>Payment Details</h2>
        

        <table>
            <form id="payment_form" action="process_payment.php" method="POST">
                <tr rowspan="2">


                </tr>
                <tr>
                    <td><label for="payment_method">Payment Method:</label></td>
                    <td> <select name="payment_method" id="payment_method" required>
                            <option value="credit_card">Credit Card</option>
                            <option value="debit_card">Debit Card</option>
                            <option value="paypal">PayPal</option>
                            <option value="cod">Cash on Delivery</option>
                        </select></td>
                </tr>
                <tr>
                    <td> <label for="delivery_address">Delivery Address:</label>
                    </td>
                    <td> <input type="text" name="delivery_address" id="delivery_address" required>
                    </td>
                </tr>
                <tr>
                    <td> <button type="submit" class="btn btn-success">Proceed to Payment</button>
                    </td>
                    <td> <a href="buyer_dashboard.php" class="btn btn-danger">Back to Home</a>
                    </td>
                </tr>
            </form>

        </table>
        </div>
    </div>
</body>

</html>

<?php mysqli_close($conn); ?>