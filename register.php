
<!DOCTYPE html>
<html>

<head>
    <title>Registration</title>
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet" />
    <!-- MDB -->
    <link href="https://cdn.jsdelivr.net/npm/mdb-ui-kit@8.2.0/css/mdb.min.css" rel="stylesheet" />

     <!-- Include SweetAlert CSS and JS -->
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>


</head>

<body>
<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if all fields are set
    if (isset($_POST['role'], $_POST['name'], $_POST['phone_number'], $_POST['address'], $_POST['email'], $_POST['password'])) {
        $role = $_POST['role']; // 'farmer' or 'buyer'
        $name = trim($_POST['name']);
        $phone_number = trim($_POST['phone_number']);
        $address = trim($_POST['address']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);

        // Validate phone number (ensure it's numeric and of correct length)
        if (!is_numeric($phone_number) || strlen($phone_number) != 10) {
            echo "Invalid phone number. It must be a 10-digit number.";
            exit;
        }

        // Include the database connection
        include('db_connection.php');

        // Hash the password before storing it in the database
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare the SQL query based on the role selected
        if ($role == 'farmer') {
            $query = "INSERT INTO farmer_registration (farmer_name, email, phone_number, address, password, created_at)
                      VALUES ('$name', '$email', '$phone_number', '$address', '$hashed_password', NOW())";
        } elseif ($role == 'buyer') {
            $query = "INSERT INTO buyer_registration (buyer_name, email, phone_number, shipping_address, password, created_at)
                      VALUES ('$name', '$email', '$phone_number', '$address', '$hashed_password', NOW())";
        } else {
            echo "Invalid role selected.";
            exit;
        }

        // Execute the query and check for errors
        if (mysqli_query($conn, $query)) {
          
            echo" <script> swal({ 
                title: 'Registration successful!',
                text: 'You can now login',
                icon: 'success',
                button: 'Ok'
                color: 'green',
              });
                </script> ";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    } else {
        echo "Please fill all the required fields.";
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
                        <a class="nav-link" href="buyer_dashboard.php">order now</a>
                    </li>



                    <li class="nav-item">
                        <a class="nav-link " aria-current="page" href="about.html">about us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link " aria-current="page" href="contact.html">contact
                            us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link " aria-current="page" href="login.php">login
                        </a>
                    </li>
                    <li class="nav-item">
                        <!-- Separate registration options -->
                        <a class="nav-link " aria-current="page" href="register.php">sign up
                        </a>
                    </li>


                </ul>
            </div>
        </div>
    </nav>

    <section class="h-100 bg-dark">
        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col">
                    <div class="card card-registration my-4">
                        <div class="row g-0">
                            <div class="col-xl-6 d-none d-xl-block">
                                <img src="https://img.freepik.com/free-vector/collection-farmer-people_23-2148535012.jpg"
                                    alt="Sample photo" class="img-fluid"
                                    style="border-top-left-radius: .25rem; border-bottom-left-radius: .25rem;" />
                            </div>
                            <div class="col-xl-6">
                                <div class="card-body p-md-5 text-black">
                                    <h3 class="mb-5 text-uppercase"> Registration form</h3>
                                    <form method="POST" action="register.php">
                                        <div class="row">
                                            <div class="col-md-6 mb-4">
                                                <div data-mdb-input-init class="form-outline">
                                                    <input type="text" id="form3Example1m"
                                                        class="form-control form-control-lg" name="name" required />
                                                    <label class="form-label" for="form3Example1n">Name</label>

                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-4">
                                                <div data-mdb-input-init class="form-outline">
                                                    <input type="text" id="form3Example1n"
                                                        class="form-control form-control-lg" name="phone_number" />
                                                    <label class="form-label" for="form3Example1n">Mobile number</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-4">
                                                <div data-mdb-input-init class="form-outline">
                                                    <input type="text" id="form3Example1m1"
                                                        class="form-control form-control-lg" name="address" required />
                                                    <label class="form-label" for="form3Example1m1">Address</label>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-4">
                                                <div data-mdb-input-init class="form-outline">
                                                    <input type="email" id="form3Example1n1"
                                                        class="form-control form-control-lg" name="email" />
                                                    <label class="form-label" for="form3Example1n1">Email</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div data-mdb-input-init class="form-outline mb-4">
                                            <input type="password" id="form3Example8"
                                                class="form-control form-control-lg" name="password" />
                                            <label class="form-label" for="form3Example8">Password</label>
                                        </div>

                                        <div class="d-md-flex justify-content-start align-items-center mb-4 py-2">

                                            <h6 class="mb-0 me-4">Role : </h6>

                                            <div class="form-check form-check-inline mb-0 me-4">
                                                <input class="form-check-input" type="radio" name="role" value="farmer"
                                                    required />
                                                <label class="form-check-label" for="femaleGender">Seller</label>
                                            </div>

                                            <div class="form-check form-check-inline mb-0 me-4">
                                                <input class="form-check-input" type="radio" name="role" value="buyer"
                                                    required />
                                                <label class="form-check-label" for="maleGender">Customer</label>
                                            </div>

                                        </div>

                                      

                                        <button type="reset" class="btn btn-light btn-lg">Reset all</button>
                                        <button type="submit" class="btn btn-primary btn-lg ms-2">Register</button>
                                    </form>
                                    <a href="login.php">Already have an account? Login here</a>

                                </div>







                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>
</body>

</html>
<!-- MDB -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/mdb-ui-kit@8.2.0/js/mdb.umd.min.js"></script>