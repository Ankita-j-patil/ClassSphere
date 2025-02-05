<?php
include('userFunctions.php'); // Ensure this file contains necessary DB connection and functions
session_start();

// Check if the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit;
}

// Validate if 'id' is present in the URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = intval($_GET['id']); // Convert to integer for safety
    $user = getUserById($id); // Function to fetch user details by ID

    if (!$user) {
        echo "User not found! Please provide a valid user ID.";
        exit; // Stop further execution if user is not found
    }
} else {
    echo "Invalid user ID!";
    exit; // Stop further execution if 'id' is not in the URL
}

// Initialize success message variable
$successMessage = "";

// Handle the form submission to update user details
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form inputs
    $id = intval($_POST['id']);
    $username = htmlspecialchars(trim($_POST['username']));
    $first_name = htmlspecialchars(trim($_POST['first_name']));
    $last_name = htmlspecialchars(trim($_POST['last_name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $address = htmlspecialchars(trim($_POST['address']));

    // Call the editUser function to update the user in the database
    if (editUser($id, $username, $first_name, $last_name, $email, $address)) {
        $successMessage = "User details updated successfully!";
        echo "<script>
                setTimeout(function() {
                    window.location.href = 'index.php';
                }, 1000); // Redirect after 3 seconds
              </script>";
    } else {
        echo "Error updating user details. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <link rel="stylesheet" href="style.css"> <!-- Ensure this path is correct -->
    <script src="script.js"></script>
    <style>
        .success-message {
            display: <?php echo ($successMessage ? 'block' : 'none'); ?>;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #4CAF50;
            color: white;
            padding: 20px;
            border-radius: 8px;
            font-size: 18px;
            text-align: center;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit User Details</h1>
        <form action="edit_user.php?id=<?php echo $user['id']; ?>" method="POST">
            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
            <label>Username:</label>
            <input type="text" name="username" value="<?php echo $user['username']; ?>" placeholder="Username" required>
            <label>First Name:</label>
            <input type="text" name="first_name" value="<?php echo $user['first_name']; ?>" placeholder="First Name" required>
            <label>Last Name:</label>
            <input type="text" name="last_name" value="<?php echo $user['last_name']; ?>" placeholder="Last Name" required>
            <label>Email:</label>
            <input type="email" name="email" value="<?php echo $user['email']; ?>" placeholder="Email" required>
            <label>Address:</label>
            <textarea name="address" placeholder="Address"><?php echo $user['address']; ?></textarea>
            <button type="submit">Update</button>
            <button type="button" onclick="window.location.href='index.php'">Cancel</button>
        </form>
    </div>

    <?php if ($successMessage): ?>
        <div class="success-message">
            <?php echo $successMessage; ?>
        </div>
    <?php endif; ?>

</body>
</html>
