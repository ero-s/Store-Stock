<?php
session_start(); // Start the session at the beginning

// Database connection details
$host = "localhost"; // Replace with your database host
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$database = "dbstorestock"; // Replace with your database name

// Establish database connection
$conn = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Initialize variables to hold form data and error messages
$firstname = "";
$lastname = "";
$email = "";
$password = "";
$contactNumber = "";
$address = "";
$firstname_error = "";
$lastname_error = "";
$email_error = "";
$password_error = "";
$contactNumber_error = "";
$address_error = "";
$register_error = ""; // General registration error

// Function to sanitize user input (preventing SQL injection and XSS)
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Process the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate First Name
    if (empty($_POST["firstname"])) {
        $firstname_error = "First Name is required";
    } else {
        $firstname = sanitize_input($_POST["firstname"]);
         if (!preg_match("/^[a-zA-Z ]*$/",$firstname)) {
            $firstname_error = "Only letters and white space allowed";
        }
    }

    // Validate Last Name
    if (empty($_POST["lastname"])) {
        $lastname_error = "Last Name is required";
    } else {
        $lastname = sanitize_input($_POST["lastname"]);
        if (!preg_match("/^[a-zA-Z ]*$/",$lastname)) {
            $lastname_error = "Only letters and white space allowed";
        }
    }

    // Validate Email
    if (empty($_POST["email"])) {
        $email_error = "Email is required";
    } else {
        $email = sanitize_input($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email_error = "Invalid email format";
        }
    }

    // Validate Password
    if (empty($_POST["password"])) {
        $password_error = "Password is required";
    } else {
        $password = sanitize_input($_POST["password"]);
        // Password must be at least 8 characters long and contain at least one number, one uppercase letter, and one lowercase letter
        if (strlen($password) < 8 || !preg_match("/[0-9]/", $password) || !preg_match("/[A-Z]/", $password) || !preg_match("/[a-z]/", $password)) {
            $password_error = "Password must be at least 8 characters long and contain at least one number, one uppercase letter, and one lowercase letter";
        }
    }

    // Validate Contact Number
    if (empty($_POST["contactNumber"])) {
        $contactNumber_error = "Contact Number is required";
    } else {
        $contactNumber = sanitize_input($_POST["contactNumber"]);
        if (!preg_match("/^[0-9]{10}$/", $contactNumber)) { //assumed 10 digit number
            $contactNumber_error = "Invalid contact number format.  Must be 10 digits.";
        }
    }

    // Validate Address
    if (empty($_POST["address"])) {
        $address_error = "Address is required";
    } else {
        $address = sanitize_input($_POST["address"]);
    }

    // If there are no errors, proceed with registration
    if (empty($firstname_error) && empty($lastname_error) && empty($email_error) && empty($password_error) && empty($contactNumber_error) && empty($address_error)) {
        // Use prepared statements to prevent SQL injection
        $sql = "INSERT INTO user (firstname, lastname, email, password, contactNum, address) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            // Hash the password before storing it in the database
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            mysqli_stmt_bind_param($stmt, "ssssss", $firstname, $lastname, $email, $hashed_password, $contactNumber, $address);
            if (mysqli_stmt_execute($stmt)) {
                // Registration successful, redirect to login with email and password prefilled
                // Store email and password in session
                $_SESSION['email'] = $email;
                $_SESSION['password'] = $password;
                header("Location: login.php");
                exit();
            } else {
                $register_error = "Registration failed: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        } else {
            // Handle database query error
            $register_error = "Database error: " . mysqli_error($conn);
        }
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@tailwindcss/browser@latest"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <h2 class="text-2xl font-semibold mb-6 text-center text-gray-800">Register</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="mb-4">
                <label for="firstname" class="block text-gray-700 text-sm font-bold mb-2">First Name:</label>
                <input type="text" name="firstname" id="firstname" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?php echo $firstname; ?>">
                <span class="text-red-500 text-xs italic"><?php echo $firstname_error; ?></span>
            </div>
            <div class="mb-4">
                <label for="lastname" class="block text-gray-700 text-sm font-bold mb-2">Last Name:</label>
                <input type="text" name="lastname" id="lastname" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?php echo $lastname; ?>">
                <span class="text-red-500 text-xs italic"><?php echo $lastname_error; ?></span>
            </div>
            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
                <input type="email" name="email" id="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?php echo $email; ?>">
                <span class="text-red-500 text-xs italic"><?php echo $email_error; ?></span>
            </div>
            <div class="mb-4">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password:</label>
                <input type="password" name="password" id="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?php echo $password; ?>">
                <span class="text-red-500 text-xs italic"><?php echo $password_error; ?></span>
            </div>
            <div class="mb-4">
                <label for="contactNumber" class="block text-gray-700 text-sm font-bold mb-2">Contact Number:</label>
                <input type="text" name="contactNumber" id="contactNumber" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?php echo $contactNumber; ?>">
                <span class="text-red-500 text-xs italic"><?php echo $contactNumber_error; ?></span>
            </div>
            <div class="mb-6">
                <label for="address" class="block text-gray-700 text-sm font-bold mb-2">Address:</label>
                <input type="text" name="address" id="address" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?php echo $address; ?>">
                <span class="text-red-500 text-xs italic"><?php echo $address_error; ?></span>
            </div>
            <?php if (!empty($register_error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Error:</strong>
                    <span class="block sm:inline"><?php echo $register_error; ?></span>
                </div>
            <?php endif; ?>
            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full">Register</button>
        </form>
        <p class="text-center mt-4 text-gray-600 text-sm">
            Already have an account? <a href="login.php" class="text-blue-500 hover:text-blue-700 font-semibold">Login</a>
        </p>
    </div>
</body>
</html>
