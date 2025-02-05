<?php
session_start(); // Start the session

$errorMessage = ""; // Variable to store error message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include_once('userFunctions.php'); // Include database functions
    
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Call the updated loginUser function
    if ($user = loginUser($username, $password)) {
        $_SESSION['username'] = $user['username'];
        $_SESSION['loggedin'] = true;
        $_SESSION['role_id'] = $user['role_id']; // Store role ID

        // Redirect based on role_id
        if ($user['role_id'] == 3) { 
            header("Location: index.php"); // Admin Dashboard
        } else { 
            header("Location: profile.php"); // Student Profile
        }
        exit;
    } else {
        $errorMessage = "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
    <style>
    .error-message {
    color: red;
    font-weight: bold;
    margin-top: 10px;
}

    </style>
</head>
<body>
    <div class="container">
        <form action="login.php" method="POST" class="registerForm">
            <h1 id="registerHeading">Login</h1>
            <input type="text" id="username" name="username" placeholder="Username" required>
            <input type="password" id="password" name="password" placeholder="Password" required>

            <div class="button-container">
                <button type="submit">Login</button>
                <a href="register.php" class="register-link">Register?</a>
            </div>

            <!-- Display error message below the buttons -->
            <?php if (!empty($errorMessage)) : ?>
                <p class="error-message"><?php echo htmlspecialchars($errorMessage); ?></p>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>
