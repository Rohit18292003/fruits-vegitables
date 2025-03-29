<?php
session_start();
include('db_connection.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'buyer') {
    header('Location: login.php');
    exit;
}

$buyer_id = $_SESSION['user_id'];

// Fetch products from buyer_cart for the logged-in buyer, including the product image
$query = "SELECT bc.cart_id, pm.product_image, pm.product_name, pm.price, bc.quantity, (pm.price * bc.quantity) AS total_price
          FROM buyer_cart bc
          JOIN product_management pm ON bc.product_id = pm.product_id
          WHERE bc.buyer_id = $buyer_id";
$result = mysqli_query($conn, $query);

$total_amount = 0;

// Handle delete and update actions
if (isset($_GET['action'])) {
    $cart_id = $_GET['cart_id'];

    if ($_GET['action'] == 'delete') {
        // Delete product from cart
        $delete_query = "DELETE FROM buyer_cart WHERE cart_id = $cart_id";
        mysqli_query($conn, $delete_query);
        header('Location: view_cart.php');
        exit;
    }

    if ($_GET['action'] == 'update' && isset($_POST['quantity'])) {
        // Update product quantity in cart
        $quantity = $_POST['quantity'];
        $update_query = "UPDATE buyer_cart SET quantity = $quantity WHERE cart_id = $cart_id";
        mysqli_query($conn, $update_query);
        header('Location: view_cart.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Cart</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
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

    
    <h1>Your Cart</h1>

    <?php if (mysqli_num_rows($result) > 0): ?>
    <table style="margin: 30px 20px 30px 20px;">
        <tr>
            <th>Product Image</th>
            <th>Product Name</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Total Price</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><img src="<?php echo $row['product_image']; ?>" alt="Product Image"></td>
            <td>
                <?php echo $row['product_name']; ?>
            </td>
            <td>
                <?php echo $row['price']; ?>
            </td>
            <td>
                <!-- Edit Quantity Form -->
                <form method="POST" action="?action=update&cart_id=<?php echo $row['cart_id']; ?>"
                    style="display: inline;">
                    <input type="number" name="quantity" value="<?php echo $row['quantity']; ?>" min="1" required>
                    <button type="submit" class="btn btn-warning">Update</button>
                </form>
            </td>
            <td>
                <?php echo $row['total_price']; ?>
            </td>
            <td>
                <!-- Delete Product Link -->
                <a class="btn btn-danger" href="?action=delete&cart_id=<?php echo $row['cart_id']; ?>">Delete</a>
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
    <a href="checkout.php" class="btn btn-success">Proceed to Checkout</a>

    <?php else: ?>
    <p>Your cart is empty.</p>
    <?php endif; ?>

    <a href="buyer_dashboard.php" class="btn btn-danger">Back to Home</a>
    </div>
</body>

</html>

<?php mysqli_close($conn); ?>