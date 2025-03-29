<?php
session_start();

// Check if the user is logged in and is a farmer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'farmer') {
    header('Location: login.php');
    exit;
}

include('db_connection.php');
$farmer_id = $_SESSION['user_id'];

// Debugging output to check the farmer ID
//echo "Farmer ID: " . $farmer_id . "<br>";

// Fetch products purchased by buyers from the logged-in farmer
$query = "
    SELECT p.product_id, p.product_name, b.buyer_name, b.shipping_address, od.quantity, od.price, od.total_price, o.order_status
    FROM product_management p
    JOIN order_details od ON p.product_id = od.product_id
    JOIN order_management o ON od.order_id = o.order_id
    JOIN buyer_registration b ON o.buyer_id = b.buyer_id
    WHERE p.farmer_id = '$farmer_id'
    ORDER BY o.created_at DESC";

// Debugging: print the SQL query to verify
//echo "SQL Query: " . $query . "<br>";

$result = mysqli_query($conn, $query);

// Check if the query was successful
if (!$result) {
    die("Query failed: " . mysqli_error($conn));  // Print error if query fails
}

// Display purchased products
if (mysqli_num_rows($result) > 0) {
    echo "<h1>Products Purchased by Buyers</h1>";
    echo "<table border='1'>
            <tr>
                <th>Product Name</th>
                <th>Buyer Name</th>
                <th>Shipping Address</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total Price</th>
                <th>Order Status</th>
            </tr>";

    while ($product = mysqli_fetch_assoc($result)) {
        echo "<tr>
                <td>{$product['product_name']}</td>
                <td>{$product['buyer_name']}</td>
                <td>{$product['shipping_address']}</td>
                <td>{$product['quantity']}</td>
                <td>{$product['price']}</td>
                <td>{$product['total_price']}</td>
                <td>{$product['order_status']}</td>
              </tr>";
    }

    echo "</table>";
} else {
    echo "<p>No products have been purchased from this farmer.</p>";
}

?>


<html>

<head>
    <title></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            margin: auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #4CAF50;
        }

        h2 {
            color: #333;
            margin-top: 30px;
        }

        p {
            font-size: 16px;
            color: #555;
        }

        nav {
            background-color: #333;
            padding: 15px;
            text-align: center;
        }

        nav a {
            text-decoration: none;
            color: white;
            margin: 0 15px;
            font-size: 18px;
        }

        nav a:hover {
            color: #4CAF50;
        }

        ul {
            list-style-type: none;
            padding-left: 0;
        }

        ul li {
            margin: 10px 0;
        }

        ul li a {
            text-decoration: none;
            color: #4CAF50;
            font-size: 18px;
        }

        ul li a:hover {
            color: #333;
        }

        .logout {
            margin-top: 30px;
            background-color: #f44336;
            color: white;
            padding: 10px 20px;
            text-align: center;
            border-radius: 5px;
            text-decoration: none;
        }

        .logout:hover {
            background-color: #d32f2f;
        }

        .profile {
            position: absolute;
            top: 20px;
            right: 20px;
            background-color: #fff;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        .profile a {
            text-decoration: none;
            color: #4CAF50;
        }

        .profile a:hover {
            color: #333;
        }
    </style>
</head>

<body>
    <nav>
        <a href="farmer_dashboard.php">Dashboard</a>
        <a href="add_product.php">Add Product</a>
        <a href="view_products_farmer.php">View Products</a>
        <a href="view_orders_farmer.php">View Orders</a>
        <a href="feedback_farmer.php">See Feedbacks</a>
        <a href="logout.php" class="logout">Logout</a>
    </nav>
    <nav>
        <ul>
            <li><a href="farmer_dashboard.php">Dashboard</a></li>
        </ul>
    </nav>
</body>

</html>

<!-- Dashboard Navigation (with Farmer Profile at the top right corner) -->