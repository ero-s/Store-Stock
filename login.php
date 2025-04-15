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
$email = "";
$password = "";
$email_error = "";
$password_error = "";
$login_error = ""; // General login error message

// Function to sanitize user input (preventing SQL injection and XSS)
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Process the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate email
    if (empty($_POST["email"])) {
        $email_error = "Email is required";
    } else {
        $email = sanitize_input($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email_error = "Invalid email format";
        }
    }

    // Validate password
    if (empty($_POST["password"])) {
        $password_error = "Password is required";
    } else {
        $password = sanitize_input($_POST["password"]);
        //  No format validation, but you might want a minimum length check
        if (strlen($password) < 6) { // Example: Minimum password length of 6
            $password_error = "Password must be at least 6 characters long";
        }
    }

    // If there are no errors, proceed with login
    if (empty($email_error) && empty($password_error)) {
      // Use prepared statements to prevent SQL injection
      $sql = "SELECT userID, password FROM user WHERE email = ?";
      $stmt = mysqli_prepare($conn, $sql);
  
      if ($stmt) {
          mysqli_stmt_bind_param($stmt, "s", $email);
          mysqli_stmt_execute($stmt);
          mysqli_stmt_store_result($stmt); // Store the result to check num_rows
  
          if (mysqli_stmt_num_rows($stmt) == 1) {
              mysqli_stmt_bind_result($stmt, $user_id, $hashed_password); // Bind after num_rows check
              mysqli_stmt_fetch($stmt); // Fetch the result
  
              // Verify the password
              if (password_verify($password, $hashed_password)) {
                  // Password is correct, set session variables and redirect
                  $_SESSION["user_id"] = $user_id;
                  $_SESSION["email"] = $email; // Store email in session if needed
                  // Important: Use a relative URL for redirection
                  header("Location: dashboard.php"); // Replace with your dashboard page
                  exit(); // Important: Always exit after a header redirect
              } else {
                  $login_error = "Invalid email or password";
              }
          } else {
              $login_error = "Invalid email or password";
          }
          mysqli_stmt_close($stmt);
      } else {
          // Handle database query error
          $login_error = "Database error: " . mysqli_error($conn);
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
    <title>Login</title>
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
        <h2 class="text-2xl font-semibold mb-6 text-center text-gray-800">Login</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
                <input type="email" name="email" id="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?php echo $email; ?>">
                <span class="text-red-500 text-xs italic"><?php echo $email_error; ?></span>
            </div>
            <div class="mb-6">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password:</label>
                <input type="password" name="password" id="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="<?php echo $password; ?>">
                <span class="text-red-500 text-xs italic"><?php echo $password_error; ?></span>
            </div>
            <?php if (!empty($login_error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Error:</strong>
                    <span class="block sm:inline"><?php echo $login_error; ?></span>
                </div>
            <?php endif; ?>
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full">Login</button>
        </form>
        <p class="text-center mt-4 text-gray-600 text-sm">
            Don't have an account? <a href="register.php" class="text-blue-500 hover:text-blue-700 font-semibold">Register</a>
        </p>
    </div>
</body>
</html>
