<?php
session_start(); // Ensure session is started

// Check if the farmer is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'farmer') {
    echo "You must be logged in as a farmer to add products.";
    exit;
}

$message = ''; // To store success or error messages

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $product_name = $_POST['product_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $quantity_available = $_POST['quantity_available'];
    $farmer_id = $_SESSION['user_id']; // farmer_id from session

    // Handle file upload
    $target_dir = "uploads/"; // Directory to store the images
    $target_file = $target_dir . basename($_FILES["product_image"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is an actual image or fake image
    $check = getimagesize($_FILES["product_image"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        $message = "File is not an image.";
        $uploadOk = 0;
    }

    // Check if file already exists
    if (file_exists($target_file)) {
        // Generate a unique name for the file if it already exists
        $target_file = $target_dir . time() . '_' . basename($_FILES["product_image"]["name"]);
    }

    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        $message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        $message = "Sorry, your file was not uploaded.";
    } else {
        // Try to upload file
        if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
            // Include the database connection
            include('db_connection.php');

            // Store the product details including the image path in the database
            $query = "INSERT INTO product_management (farmer_id,product_image, product_name, description, price, quantity_available)
            VALUES ('$farmer_id' , '$target_file', '$product_name', '$description', '$price', '$quantity_available')";

            if (mysqli_query($conn, $query)) {
                $message = "Product and image added successfully!";
            } else {
                $message = "Error: " . mysqli_error($conn);
            }
        } else {
            $message = "Sorry, there was an error uploading your file.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet" />
    <!-- MDB -->
    <link href="https://cdn.jsdelivr.net/npm/mdb-ui-kit@8.2.0/css/mdb.min.css" rel="stylesheet" />

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: rgb(255, 255, 255);
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
        .divider:after,
        .divider:before {
            content: "";
            flex: 1;
            height: 1px;
            background: #eee;
        }

        .h-custom {
            height: calc(100% - 73px);
        }

        @media (max-width: 450px) {
            .h-custom {
                height: 100%;
            }
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

    <?php if ($message != ''): ?>
    <p>
        <?php echo $message; ?>
    </p>
    <?php endif; ?>
    <section class="vh-100">
        <div class="container-fluid h-custom">
            <div class="row d-flex justify-content-center align-items-center h-100">

                <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">


                    <form method="POST" action="add_product.php" enctype="multipart/form-data">
                        <h1>Add New Product</h1>

                        <label for="product_name">Product Name:</label>
                        <input type="text" name="product_name" class="form-control form-control-lg" required><br>

                        <label for="description">Description:</label>
                        <textarea name="description" required class="form-control form-control-lg"></textarea><br>

                        <label for="price">Price:</label>
                        <input type="number" name="price" class="form-control form-control-lg" required><br>

                        <label for="quantity_available">Quantity Available:</label>
                        <input type="number" name="quantity_available" class="form-control form-control-lg"
                            required><br>

                        <label for="product_image">Product Image:</label>
                        <input type="file" name="product_image" class="form-control form-control-lg" required><br>

                        <button type="submit" class="btn btn-primary btn-lg">Add Product</button>
                    </form>
                    <a href="farmer_dashboard.php">Back to Dashboard</a>
                </div>
                <div class="col-md-9 col-lg-6 col-xl-5">
                    <img src="https://img.freepik.com/premium-vector/fruits-logo-design-vector-art-illustration_761413-30674.jpg"
                        class="img-fluid" alt="Sample image">
                </div>
            </div>
        </div>
        </div>

    </section>
</body>

</html>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/mdb-ui-kit@8.2.0/js/mdb.umd.min.js"></script>