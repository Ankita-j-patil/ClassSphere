<?php
session_start();

// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'example';

$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    die("Access denied. Please log in.");
}

// Fetch the logged-in user's username from the session
$username = $_SESSION['username'];

// Initialize user array
$user = [
    'username' => '',
    'email' => '',
    'first_name' => '',
    'last_name' => '',
    'address' => '',
    'profile_photo' => '',
    'role_id' => ''
];

// Fetch user details including role_id
$query = "SELECT username, email, first_name, last_name, address, profile_photo, role_id FROM students WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    die("Error: User not found.");
}
$stmt->close();

// Fetch the role of the user
$roleQuery = "SELECT role_name FROM roles WHERE id = ?";
$roleStmt = $conn->prepare($roleQuery);
$roleStmt->bind_param("i", $user['role_id']);
$roleStmt->execute();
$roleResult = $roleStmt->get_result();
$role = $roleResult->fetch_assoc();
$roleStmt->close();

// Fetch subjects assigned to the student
$subjectsQuery = "
    SELECT subject_name
    FROM student_subjects
    JOIN subjects ON student_subjects.subject_id = subjects.id
    WHERE student_subjects.student_id = (SELECT id FROM students WHERE username = ?)
";

$subjectStmt = $conn->prepare($subjectsQuery);
$subjectStmt->bind_param("s", $username);
$subjectStmt->execute();
$subjectResult = $subjectStmt->get_result();
$subjects = [];

while ($subject = $subjectResult->fetch_assoc()) {
    $subjects[] = $subject['subject_name'];
}

$subjectStmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Profile</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: url('background1.webp') no-repeat center center fixed;
            background-size: cover;
        }
        .container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            width: 400px;
            text-align: center;
        }
        h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .profile-photo img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 15px;
        }
        .profile-info {
            line-height: 1.6;
            color: #555;
            text-align: left;
            margin-left: 100px;
        }
        .profile-info p {
            margin: 5px 0;
        }
        .monitor-badge {
            font-weight: bold;
            color: green;
            margin-left: 20px;
        }
        .button-container {
            margin-top: 20px;
        }
        .button-container button {
            background-color: #5cb85c;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px;
            font-size: 14px;
        }
        .button-container button:hover {
            background-color: #0056b3;
        }
        .subjects-list {
            margin-top: 20px;
            text-align: left;
            color: #333;
        }
        .subjects-list ul {
            padding: 0;
            list-style-type: bullets;
            margin-left : 70px;
        }
        .subjects-list li {
            padding: 5px 0;
        }
    </style>

</head>
<body>
    <div class="container">
        <h2>User Profile</h2>

        <div class="profile-photo">
            <?php if (!empty($user['profile_photo'])): ?>
                <img src="<?= htmlspecialchars($user['profile_photo']) ?>" alt="Profile Photo">
            <?php else: ?>
                <img src="student.png" alt="Default Profile Photo">
            <?php endif; ?>
        </div>

        <div class="profile-info">
            <p><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
            <p><strong>First Name:</strong> <?= htmlspecialchars($user['first_name']) ?></p>
            <p><strong>Last Name:</strong> <?= htmlspecialchars($user['last_name']) ?></p>
            <p><strong>Address:</strong> <?= !empty($user['address']) ? htmlspecialchars($user['address']) : 'Not provided' ?></p>
            
            <!-- Display 'Assigned as a Monitor' if user has 'monitor' role -->
            <?php if (isset($role['role_name']) && $role['role_name'] === 'monitor'): ?>
                <p class="monitor-badge">Assigned as a Monitor</p>
            <?php endif; ?>
        </div>

        <!-- Form for 'View Student Details' button -->
        <?php if (isset($role['role_name']) && $role['role_name'] === 'monitor') : ?>
            <form action="viewSubjects.php" method="get">
                <div class="button-container">
                    <button type="submit">View Student Details</button>
                </div>
            </form>
        <?php endif; ?>

        <div class="button-container">
            <button onclick="window.location.href='edit_user1.php'">Edit Profile</button>
            <button onclick="window.location.href='reset_password.php'">Reset Password</button>
            <button onclick="window.location.href='login.php'">Logout</button>
        </div>

        <div class="subjects-list">
            <h3>Assigned Subjects:</h3>
            <?php if (count($subjects) > 0): ?>
                <ul>
                    <?php foreach ($subjects as $subject): ?>
                        <li><?= htmlspecialchars($subject) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No subjects assigned.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
