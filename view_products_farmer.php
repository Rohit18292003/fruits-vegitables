<?php
session_start();

// Check if the session is valid (i.e., logged in as a farmer)
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'farmer') {
    header('Location: login.php');
    exit;
}

include('db_connection.php');
$farmer_id = $_SESSION['user_id'];

// Handle delete action
if (isset($_GET['delete_id'])) {
    $delete_id = mysqli_real_escape_string($conn, $_GET['delete_id']);
    $delete_query = "DELETE FROM product_management WHERE product_id = '$delete_id' AND farmer_id = '$farmer_id'"; // Ensure farmer owns the product
    if (mysqli_query($conn, $delete_query)) {
        // Optionally add a success message
        header("Location: view_products.php"); // Redirect to refresh the page
        exit();
    } else {
        echo "Error deleting record: " . mysqli_error($conn); // Handle errors
    }
}

// Query to fetch all products
$query = "SELECT product_id, product_image, product_name, description, price, quantity_available FROM product_management WHERE farmer_id = '$farmer_id'";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>View All Products</title>
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
    <style>
        /* Styling for your page */
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
        }

        .container {
            width: 90%;
            max-width: 1500px;
            margin: 20px auto;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            /* margin-top: 20px; */
        }

        .product-card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 15px;
            text-align: center;
            transition: transform 0.2s;
            display: flex;
            flex-direction: column;
            min-height: 400px;
        }

        /* Styles for single product view */
        .product-grid:has(.product-card:only-child) {
            grid-template-columns: 1fr;
            /* Single column layout */
            max-width: 800px;
            /* Limit width of single product */
            margin: 20px auto;
        }

        .product-grid:has(.product-card:only-child) .product-card {
            text-align: left;
            /* Align text left in single product view */
            min-height: auto;
            /* Remove fixed height for single product */
        }

        .product-grid:has(.product-card:only-child) .product-image img {
            max-height: 400px;
            /* Adjust max height as needed */
            width: auto;
            display: block;
            margin: 0 auto;
        }

        .product-card:hover {
            transform: translateY(-5px);
        }

        .product-image img {
            width: 100%;
            height: auto;
            max-height: 250px;
            object-fit: contain;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 10px;
        }

        .product-info {
            text-align: left;
        }

        .product-info h3 {
            font-size: 1.2em;
            margin: 10px 0;
        }

        .product-info p {
            margin: 5px 0;
        }

        .product-info .price {
            font-size: 1.1em;
            font-weight: bold;
            color: #28a745;
        }
    </style>
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet" />
    <!-- MDB -->
    <link href="https://cdn.jsdelivr.net/npm/mdb-ui-kit@8.2.0/css/mdb.min.css" rel="stylesheet" />
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
    <div class="container">
        <h1>Your Products</h1>

        <?php if (mysqli_num_rows($result) > 0): ?>
        <div class="product-grid">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="product-card">
                <div class="product-image">
                    <?php if (!empty($row['product_image'])): ?>
                    <img src="<?php echo $row['product_image']; ?>" alt="Product Image">
                    <?php else: ?>
                    <img src="uploads/default.jpg" alt="No Image Available">
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h3>
                        <?php echo htmlspecialchars($row['product_name']); ?>
                    </h3>
                    <p>
                        <?php echo htmlspecialchars($row['description']); ?>
                    </p>
                    <p class="price">
                        <?php echo htmlspecialchars($row['price']); ?> / unit
                    </p>
                    <p>
                        <?php echo htmlspecialchars($row['quantity_available']); ?> available
                    </p>
                </div>
                <div>
                    <a href="edit_product.php?id=<?php echo $row['product_id']; ?>" class="edit-btn"
                        class="btn btn-primary btn-lg">Edit</a>
                    <a href="view_products.php?delete_id=<?php echo $row['product_id']; ?>" class="delete-btn"
                        onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php else: ?>
        <p>No products found.</p>
        <?php endif; ?>

        <a href="farmer_dashboard.php">Back to Dashboard</a>
    </div>
</body>

</html>

<?php mysqli_close($conn); ?>