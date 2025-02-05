<?php
$message = ""; // Initialize an empty message variable
$showPopup = false; // Boolean to control popup visibility
$popupType = ""; // Type of popup (error or success)

// Initialize form field values
$username = $first_name = $last_name = $email = $address = $role = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include_once 'userFunctions.php'; // Include your database functions
    include_once 'db.php'; // Ensure the database connection is included

    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $role = $_POST['role']; // Capture the selected role (student/admin)

    // Validate email format strictly for "@gmail.com"
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match("/^[a-zA-Z0-9._%+-]+@gmail\.com$/", $email)) {
        $message = "Invalid email format. Please use a valid @gmail.com email.";
        $popupType = "error"; // Error popup
        $showPopup = true;
        $email = ""; // Clear only email field
    }
    // Validate that passwords match
    elseif ($password !== $confirm_password) {
        $message = "Passwords do not match. Please try again.";
        $popupType = "error"; // Error popup
        $showPopup = true;
    }
    // Validate password strength (alphanumeric and special characters)
    elseif (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
        $message = "Password must be at least 8 characters long and include at least one letter, one number, and one special character.";
        $popupType = "error"; // Error popup
        $showPopup = true;
    }
    // Check if username already exists in either table
    elseif (checkUsernameExists($username)) {
        $message = "Username already exists. Please choose a different one.";
        $popupType = "error"; // Error popup
        $showPopup = true;
    }
    else {
        // Fetch the actual role_id from the roles table
        global $conn;
        $role_id = NULL;

        $query = "SELECT id FROM roles WHERE role_name = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $role);
        $stmt->execute();
        $stmt->bind_result($role_id);
        $stmt->fetch();
        $stmt->close();

        if ($role_id === NULL) {
            $message = "Error: Role not found in database.";
            $popupType = "error";
            $showPopup = true;
        } else {
            // Register the user
            if (registerUser($username, $password, $first_name, $last_name, $email, $address, $role)) {
                $message = "Registration successful! You can now log in.";
                $popupType = "success";
                $showPopup = true;
            } else {
                $message = "Error during registration. Please try again.";
                $popupType = "error";
                $showPopup = true;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .error-message {
            color: red;
            font-weight: bold;
            margin-top: 10px;
        }

        .success-message {
            color: green;
            font-weight: bold;
            margin-top: 10px;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            width: 300px;
        }

        .modal-content h2 {
            font-size: 22px;
        }

        .modal-content p {
            font-size: 16px;
        }

        .modal-success h2,
        .modal-success p {
            color: green;
        }

        .modal-error h2,
        .modal-error p {
            color: red;
        }

        .modal-content button {
            margin-top: 10px;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .modal-content button:hover {
            background-color: #ddd;
        }
    </style>
</head>
<body>
    <div class="container">
        <form action="register.php" method="POST" class="registerForm">
            <h1 id="registerHeading">Register New User</h1>
            <input type="text" id="username" name="username" placeholder="Username" required value="<?php echo htmlspecialchars($username); ?>">
            <input type="password" id="password" name="password" placeholder="Password" required>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm Password" required>
            <input type="text" id="first_name" name="first_name" placeholder="First Name" required value="<?php echo htmlspecialchars($first_name); ?>">
            <input type="text" id="last_name" name="last_name" placeholder="Last Name" required value="<?php echo htmlspecialchars($last_name); ?>">
            <input type="email" id="email" name="email" placeholder="Email (Must be @gmail.com)" required value="<?php echo htmlspecialchars($email); ?>">
            <textarea id="address" name="address" placeholder="Address"><?php echo htmlspecialchars($address); ?></textarea>
            
            <!-- Role Dropdown -->
            <select name="role" required>
                <option value="student" <?php if ($role == "student") echo "selected"; ?>>Student</option>
                <option value="admin" <?php if ($role == "admin") echo "selected"; ?>>Admin</option>
            </select>
            
            <button type="submit">Register</button>
            <p>Already have an account? <a id="linkColor" href="login.php">Login</a></p>
        </form>
    </div>

    <!-- Popup Modal -->
    <?php if ($showPopup): ?>
        <div id="messagePopup" class="modal" style="display: flex;">
            <div class="modal-content <?php echo $popupType == 'success' ? 'modal-success' : 'modal-error'; ?>">
                <h2><?php echo $popupType == "success" ? "Success!" : "Error"; ?></h2>
                <p><?php echo htmlspecialchars($message); ?></p>
                <button onclick="closePopup()">OK</button>
            </div>
        </div>
    <?php endif; ?>

    <script>
        function closePopup() {
            document.getElementById('messagePopup').style.display = 'none';
            <?php if ($popupType == "success"): ?>
                window.location.href = "login.php"; // Redirect to login after successful registration
            <?php endif; ?>
        }

        <?php if ($showPopup): ?>
            document.getElementById('messagePopup').style.display = 'flex';
        <?php endif; ?>
    </script>
</body>
</html>
