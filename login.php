<!-- Improved Login Form -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet" />
    <!-- MDB -->
    <link href="https://cdn.jsdelivr.net/npm/mdb-ui-kit@8.2.0/css/mdb.min.css" rel="stylesheet" />


    <!-- Include SweetAlert CSS and JS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>



    <script>
        function showError(message) {
            swal("Error!", message, "error");
        }

        // Example usage:  

    </script>
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
    <?php
session_start(); // Start the session

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get user input
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role']; // 'farmer' or 'buyer'

    include('db_connection.php'); // Include your database connection

    // Farmer login check
    if ($role == 'farmer') {
        $query = "SELECT * FROM farmer_registration WHERE email = '$email'";
        $result = mysqli_query($conn, $query);
        
        if (!$result) {
            // If the query fails, show an error
            die('Query failed: ' . mysqli_error($conn));
        }

        $user = mysqli_fetch_assoc($result);

        // Verify password and login
        if ($user && password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['farmer_id'];
            $_SESSION['role'] = 'farmer'; // Store role in session

            // Redirect to farmer dashboard
            header('Location: farmer_dashboard.php');
            exit; // Make sure no further code is executed after redirect
        } else {
  
        

            echo" <script> swal({ 
            title: 'Error!',
            text: 'Invalid email or password!',
            icon: 'error',
            button: 'Ok'
          });
            </script> ";
     
        }
    } elseif ($role == 'buyer') {
        // Buyer login check
        $query = "SELECT * FROM buyer_registration WHERE email = '$email'";
        $result = mysqli_query($conn, $query);
        
        if (!$result) {
            // If the query fails, show an error
            die('Query failed: ' . mysqli_error($conn));
        }

        $user = mysqli_fetch_assoc($result);

        // Verify password and login
        if ($user && password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['buyer_id'];
            $_SESSION['role'] = 'buyer'; // Store role in session

            // Redirect to buyer dashboard
            header('Location: buyer_dashboard.php');
            exit; // Make sure no further code is executed after redirect
        } else {
         
        
                    echo" <script> swal({ 
                    title: 'Error!',
                    text: 'Invalid email or password!',
                    icon: 'error',
                    button: 'Ok'
                  });
                    </script> ";
        }
    } else {
        echo "Please select a role!";
    }
}
?>
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
                        <a class="nav-link" href="buyer_dashboard.php">Order now</a>
                    </li>



                    <li class="nav-item">
                        <a class="nav-link " aria-current="page" href="about.html">About us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link " aria-current="page" href="contact.html">Contact
                            us</a>
                    </li>

                    <li class="nav-item">
                        <!-- Separate registration options -->
                        <a class="nav-link " aria-current="page" href="register.php">Sign up
                        </a>
                    </li>


                </ul>
            </div>
        </div>
    </nav>
    <section class="vh-100">
        <div class="container-fluid h-custom">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-md-9 col-lg-6 col-xl-5">
                    <img src="https://static.vecteezy.com/system/resources/thumbnails/040/519/847/small_2x/indian-farmer-growth-concept-vector.jpg"
                        class="img-fluid" alt="Sample image">
                </div>
                <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">



                    <div class="divider d-flex align-items-center my-4">
                        <p class="text-center fw-bold mx-3 mb-0">Log in </p>
                    </div>
                    <form method="POST" action="login.php">
                        <div class="role-selection">
                            <label>
                                <input type="radio" name="role" value="farmer" required> Seller
                            </label>
                            <label>
                                <input type="radio" name="role" value="buyer" required> Customer
                            </label>
                        </div>
                        <!-- Email input -->
                        <div data-mdb-input-init class="form-outline mb-4">

                            <input type="email" id="form3Example3" placeholder="Enter a valid email address"
                                name="email" class="form-control form-control-lg" required />
                            <label class="form-label" for="form3Example4">Email</label>
                        </div>

                        <!-- Password input -->
                        <div data-mdb-input-init class="form-outline mb-3">

                            <input type="password" id="form3Example4" placeholder="Enter password" name="password"
                                class="form-control form-control-lg" required />
                            <label class="form-label" for="form3Example4">Password</label>
                        </div>



                        <div class="d-flex justify-content-between align-items-center">
                            <!-- Checkbox -->


                            <a href="forget_password.php" class="text-body">Forgot password?</a>
                        </div>

                        <div class="text-center text-lg-start mt-4 pt-2">
                            <button class="btn btn-primary btn-lg" type="submit">Login</button>


                            <button type="reset" class="btn btn-light btn-lg">Reset all</button>

                            <p class="small fw-bold mt-2 pt-1 mb-0">Don't have an account? <a href="register.php"
                                    class="link-danger">Register</a></p>
                        </div>

                    </form>
                </div>
            </div>
        </div>

    </section>
    <div
        class="d-flex flex-column flex-md-row text-center text-md-start justify-content-between py-4 px-4 px-xl-5 bg-primary">
        <!-- Copyright -->
        <div class="text-white mb-3 mb-md-0">
            Get connected with us on social networks:
        </div>
        <!-- Copyright -->

        <!-- Right -->
        <div>
            <a href="dashboard.php" class="text-white me-4">
                Home
            </a>

        </div>
        <!-- Right -->
    </div>


</body>

</html>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/mdb-ui-kit@8.2.0/js/mdb.umd.min.js"></script>