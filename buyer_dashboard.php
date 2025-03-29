<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'buyer') {
    header('Location: login.php');
    exit;
}

include('db_connection.php');
$buyer_id = $_SESSION['user_id'];

$search_query = "";
if (isset($_GET['search'])) {
    $search_query = mysqli_real_escape_string($conn, $_GET['search']);
}

$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$query = "SELECT product_id, product_name, product_image, price 
          FROM product_management 
          WHERE product_name LIKE '%$search_query%' 
          LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

$count_query = "SELECT COUNT(*) AS total_products 
                FROM product_management 
                WHERE product_name LIKE '%$search_query%'";
$count_result = mysqli_query($conn, $count_query);
if (!$count_result) {
    die("Count query failed: " . mysqli_error($conn));
}
$count_data = mysqli_fetch_assoc($count_result);
$total_products = $count_data['total_products'];
$total_pages = ceil($total_products / $limit);

$cart_query = "SELECT SUM(quantity) AS total_items FROM buyer_cart WHERE buyer_id = $buyer_id";
$cart_result = mysqli_query($conn, $cart_query);
if (!$cart_result) {
    die("Cart query failed: " . mysqli_error($conn));
}
$cart_data = mysqli_fetch_assoc($cart_result);
$total_items_in_cart = $cart_data['total_items'] ? $cart_data['total_items'] : 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Buyer Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

    <link rel='stylesheet'
        href='https://cdn-uicons.flaticon.com/2.6.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>


    <style>
        /* Style for the navigation bar */
        .navbar {
            background-color: #fff;
            padding: 10px;
            position: sticky;
            top: 0;
            z-index: 1;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            display: flex;
        }

        .navbar ul li {
            margin-right: 20px;
        }

        .navbar ul li a {

            text-decoration: none;
            padding: 10px 15px;
            display: block;
        }


        .search-form {}

        .search-form input[type="text"] {
            padding: 5px;
        }

        .search-form input[type="submit"] {

            background-color: rgb(44, 154, 210);

        }

        /* Wrapper for profile, cart and logout buttons */
        .navbar-right {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 20px;
            /* Space between the buttons */
        }



        .cart-button {
            padding: 10px 15px;
            background-color: rgb(50, 106, 218);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            position: relative;
        }

        .cart-button .cart-count {
            background-color: red;
            color: white;
            border-radius: 50%;
            padding: 3px 7px;
            position: absolute;
            top: -10px;
            right: -10px;
            font-size: 12px;
        }


        .product-card {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 20px;



        }

        .quantity-controls {

            display: flex;
            align-items: center;
        }

        .quantity-controls button {
            padding: 5px 5px;
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
            text-align: left;
            transition: transform 0.2s;
            display: flex;
            flex-direction: column;
            min-height: 300px;
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


        /* Responsive design */
        @media (max-width: 768px) {
            .product-card {

                align-items: flex-start;
            }
        }
    </style>
    <script>
        function increaseQuantity(productId) {
            const quantityInput = document.getElementById('quantity_' + productId);
            let currentValue = parseInt(quantityInput.value);
            quantityInput.value = currentValue + 1;
        }

        function decreaseQuantity(productId) {
            const quantityInput = document.getElementById('quantity_' + productId);
            let currentValue = parseInt(quantityInput.value);
            if (currentValue > 1) {
                quantityInput.value = currentValue - 1;
            }
        }
    </script>





</head>

<body>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php" id="animate-charcter">
                <img src="assets/logo.jpg" alt="" width="80" height="58" class="d-inline-block">
                fruits & vegitables
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link " aria-current="page" href="dashboard.php"
                            style="color: black; text-decoration: solid;">Home</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="buyer_dashboard.php">order now</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link " aria-current="page" href="about.html">about us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link " aria-current="page" href="contact.html">contact
                            us</a>
                    </li>

                    <li>
                        <form class="search-form" method="GET" action="buyer_dashboard.php" style="display:flex; ">
                            <input type="text" name="search" placeholder="Search products..."
                                value="<?php echo htmlspecialchars($search_query); ?>">
                            <button type="submit" value="Search" class="btn btn-primary">Search</button>
                        </form>
                    </li>

                    <li class="nav-item">
                        <!-- Separate registration options -->
                        <a href="view_cart.php" class="cart-button">View Cart
                            <span class="cart-count">
                                <?php echo $total_items_in_cart; ?>
                            </span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link " aria-current="page" href="logout.php">logout
                        </a>
                    </li>


                </ul>




            </div>
        </div>
        </div>
    </nav>

    <div class="navbar">
        <ul>

            <li><a href="view_orders_buyer.php">View Orders</a></li>
            <li><a href="update_buyer_profile.php">Update Profile</a></li>
            <li><a href="feedback_buyer.php">FeedBack</a></li>
            <li><a href="reset_password.php">Change Password</a></li>
        </ul>



    </div>
    </div>

    <?php if (isset($_GET['message'])): ?>
    <p>
        <?php echo htmlspecialchars($_GET['message']); ?>
    </p>
    <?php endif; ?>

    <h1>Browse Products</h1>

    <?php if (mysqli_num_rows($result) > 0): ?>
    <?php while ($product = mysqli_fetch_assoc($result)): ?>


        <div class="container">
    <div class="product-grid" >
       
            <div class="product-card" >
                <div class="product-image">
                    <img src="<?php echo empty($product['product_image']) ? 'default_image.jpg' : $product['product_image']; ?>"
                        class="card-img-top" alt="...">
                </div>
                <div class="card-body">
                    <h3 class="card-title">
                        <?php echo $product['product_name']; ?>
                    </h3>
                    <p class="card-text">Price:
                        <?php echo $product['price']; ?>
                    </p>
                    <div class="quantity-controls">
                        <button class="btn btn-light"
                            onclick="decreaseQuantity(<?php echo $product['product_id']; ?>)">-</button>
                        <input type="text" id="quantity_<?php echo $product['product_id']; ?>" value="1" size="2"
                            readonly>
                        <button class="btn btn-light"
                            onclick="increaseQuantity(<?php echo $product['product_id']; ?>)">+</button>
                    </div>
                    <div>
                        <form action="add_to_cart.php" method="POST">
                            <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                            <input type="hidden" name="quantity"
                                id="hidden_quantity_<?php echo $product['product_id']; ?>" value="1">
                            <button type="submit" class="btn btn-success" style=" margin-top:20px; background-color: #386bc0;"
                                onclick="document.getElementById('hidden_quantity_<?php echo $product['product_id']; ?>').value = document.getElementById('quantity_<?php echo $product['product_id']; ?>').value;">
                                Add to Cart
                            </button>
                        </form>
                    </div>


                </div>
            </div>
        </div>
    </div>


    <?php endwhile; ?>

    <div class="pagination">
        <?php if ($page > 1): ?>
        <a href="?search=<?php echo htmlspecialchars($search_query); ?>&page=<?php echo $page - 1; ?>">Previous</a>
        <?php endif; ?>
        <?php if ($page < $total_pages): ?>
        <a href="?search=<?php echo htmlspecialchars($search_query); ?>&page=<?php echo $page + 1; ?>">Next</a>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <p>No products available
        <?php if (!empty($search_query)) echo " matching your search"; ?>.
    </p>
    <?php endif; ?>
    <!-- Footer -->
    <footer class="text-center text-lg-start text-white" style="background-color: #1c2331">
        <!-- Section: Social media -->
        <section class="d-flex justify-content-between p-4" style="background-color: #386bc0">
            <!-- Left -->
            <div class="me-5">
                <span>Get connected with us on social networks:</span>
            </div>
            <!-- Left -->

            <!-- Right -->
            <div>
                <a href="" class="text-white me-4">
                    <i class="fab fa-facebook-f"></i>
                </a>


                <a href="" class="text-white me-4">
                    <i class="fab fa-instagram"></i>
                </a>

            </div>
            <!-- Right -->
        </section>
        <!-- Section: Social media -->

        <!-- Section: Links  -->
        <section class="">
            <div class="container text-center text-md-start mt-5">
                <!-- Grid row -->
                <div class="row mt-3">
                    <!-- Grid column -->
                    <div class="col-md-3 col-lg-4 col-xl-3 mx-auto mb-4">
                        <!-- Content -->
                        <h6 class="text-uppercase fw-bold">Company name</h6>
                        <hr class="mb-4 mt-0 d-inline-block mx-auto"
                            style="width: 60px; background-color: #7c4dff; height: 2px" />
                        <p>
                            your premier online destination for the freshest fruits and vegetables! Founded in 2024, we
                            are passionate about providing our customers with high-quality, organic produce straight
                            from local farms.
                        </p>
                    </div>
                    <!-- Grid column -->

                    <!-- Grid column -->
                    <div class="col-md-2 col-lg-2 col-xl-2 mx-auto mb-4">
                        <!-- Links -->
                        <h6 class="text-uppercase fw-bold">Products</h6>
                        <hr class="mb-4 mt-0 d-inline-block mx-auto"
                            style="width: 60px; background-color: #7c4dff; height: 2px" />
                        <p>
                            <a href="buyer_dashboard.php" class="text-white">Fruits</a>
                        </p>
                        <p>
                            <a href="buyer_dashboard.php" class="text-white">Vegitables</a>
                        </p>
                        <p>
                            <a href="#!" class="text-white"></a>
                        </p>
                        <p>
                            <a href="#!" class="text-white"></a>
                        </p>
                    </div>
                    <!-- Grid column -->

                    <!-- Grid column -->
                    <div class="col-md-3 col-lg-2 col-xl-2 mx-auto mb-4">
                        <!-- Links -->
                        <h6 class="text-uppercase fw-bold">Useful links</h6>
                        <hr class="mb-4 mt-0 d-inline-block mx-auto"
                            style="width: 60px; background-color: #7c4dff; height: 2px" />
                        <p>
                            <a href="buyer_dashboard.php" class="text-white">Your Account</a>
                        </p>


                        <p>
                            <a href="contact.html" class="text-white">Help</a>
                        </p>
                    </div>
                    <!-- Grid column -->

                    <!-- Grid column -->
                    <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mb-md-0 mb-4">
                        <!-- Links -->
                        <h6 class="text-uppercase fw-bold">Contact</h6>
                        <hr class="mb-4 mt-0 d-inline-block mx-auto"
                            style="width: 60px; background-color: #7c4dff; height: 2px" />
                        <p><i class="fi fi-sr-home"></i> Near cocsit</p>
                        <p><i class="fi fi-sr-envelope"></i> fruits@.com</p>
                        <p><i class="fi fi-sr-phone-call"></i></i> +91 1234567</p>
                        <p><i class="fas fa-print mr-3"></i> + 0123456789</p>
                    </div>
                    <!-- Grid column -->
                </div>
                <!-- Grid row -->
            </div>
        </section>
        <!-- Section: Links  -->


        <!-- Copyright -->
    </footer>
    <!-- Footer -->


    <!-- End of .container -->


</body>

</html>