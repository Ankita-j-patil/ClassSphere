<?php
// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'example';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    die("Access denied. Please log in.");
}

// Fetch the logged-in user's username from the session
$loggedInUser = $_SESSION['username'];

// Fetch user details for pre-filling the form
$query = "
    SELECT email, first_name, last_name, address
    FROM students
    WHERE username = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $loggedInUser);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    die("No user found.");
}

// Check if profile update was successful
$updateSuccess = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $address = $_POST['address'];

    // Update the user's details in the database
    $updateQuery = "
        UPDATE students
        SET email = ?, first_name = ?, last_name = ?, address = ?
        WHERE username = ?
    ";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("sssss", $email, $first_name, $last_name, $address, $loggedInUser);

    if ($updateStmt->execute()) {
        $updateSuccess = true;
    }

    $updateStmt->close();
}

// Close database connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: url('background1.webp') no-repeat center center fixed;
            background-size: cover;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 400px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        form input, form textarea, form button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        form button {
            background-color: #28a745;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        form button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Edit Profile</h2>
        <form method="POST">
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" placeholder="Email" required>
            <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" placeholder="First Name" required>
            <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" placeholder="Last Name" required>
            <textarea name="address" placeholder="Address"><?= htmlspecialchars($user['address']) ?></textarea>
            <button type="submit">Save Changes</button>
            <button type="button" onclick="window.location.href='profile.php'">Cancel</button>

                
        </form>
    </div>

    <!-- Bootstrap Modal for Success Message -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Success</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    ✅ Profile updated successfully!
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" onclick="redirectToProfile()">OK</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap & JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Function to redirect after clicking OK
        function redirectToProfile() {
            window.location.href = "profile.php";
        }

        // Show the modal if profile update was successful
        <?php if ($updateSuccess): ?>
            var myModal = new bootstrap.Modal(document.getElementById('successModal'));
            myModal.show();
            setTimeout(redirectToProfile, 3000); // Auto redirect after 3 sec
        <?php endif; ?>
    </script>

</body>
</html>
