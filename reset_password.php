<?php
session_start();
include_once('userFunctions.php'); // Include functions

if (!isset($_SESSION['username'])) {
    die("Access denied. Please log in.");
}

$errorMessage = "";
$successMessage = "";
$username = $_SESSION['username'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($newPassword !== $confirmPassword) {
        $errorMessage = "New passwords do not match!";
    } else {
        if (resetUserPassword($username, $currentPassword, $newPassword)) {
            $successMessage = "Password changed successfully!";
            echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        document.getElementById('successModal').style.display = 'flex';
                        setTimeout(function() {
                            window.location.href = 'profile.php';
                        }, 3000); // Redirect after 3 seconds
                    });
                  </script>";
        } else {
            $errorMessage = "Current password is incorrect!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    
    <style>
        body {
            font-family: Arial, sans-serif;
            background: rgba(255, 255, 255, 0.8); 
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: url('background1.webp') no-repeat center center fixed;
            background-size: cover;
        }
        .container {
            background: rgba(255, 255, 255, 0.8); 
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 350px;
            text-align: center;
        }
        h2 {
            margin-bottom: 20px;
            color: #333;
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background-color: #5cb85c;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }
        button:hover {
            background-color: #4cae4c;
        }
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
        /* Success Modal */
        .success-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Reset Password</h2>
        <form action="reset_password.php" method="POST">
            <input type="password" name="current_password" placeholder="Current Password" required>
            <input type="password" name="new_password" placeholder="New Password" required>
            <input type="password" name="confirm_password" placeholder="Confirm New Password" required>

            <button type="submit">Change Password</button>
            <button type="button" onclick="window.location.href='profile.php'">Cancel</button>

            <?php if (!empty($errorMessage)) : ?>
                <p class="error-message"><?php echo htmlspecialchars($errorMessage); ?></p>
            <?php endif; ?>
        </form>
    </div>

    <!-- Success Message Modal -->
    <div id="successModal" class="success-modal">
        <div class="modal-content">
            <h2>Password changed successfully!</h2>
            <p>Redirecting to profile...</p>
        </div>
    </div>
</body>
</html>
