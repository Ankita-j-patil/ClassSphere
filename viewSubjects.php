<?php
session_start();
include_once('db.php'); // Include database connection

if (!isset($_SESSION['username'])) {
    die("Access denied. Please log in.");
}

// Fetch the role of the logged-in user
$username = $_SESSION['username'];
$query = "SELECT role_id FROM students WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$role_id = $user['role_id'];

// Correct way: Fetch the monitor role ID separately
$query = "SELECT id FROM roles WHERE role_name = 'monitor'";
$result = $conn->query($query);
$monitorRole = $result->fetch_assoc();
$monitorRoleId = $monitorRole['id'];

// Now compare using PHP variables
if ($role_id != $monitorRoleId) {
    die("Access denied. You do not have permission to view this page.");
}

// Fetch all students' first and last names, and group their subjects into a comma-separated list
$query = "
    SELECT s.username, 
           CONCAT(s.first_name, ' ', s.last_name) AS student_name, 
           GROUP_CONCAT(subj.subject_name ORDER BY subj.subject_name) AS subjects
    FROM student_subjects ss
    JOIN students s ON ss.student_id = s.id
    JOIN subjects subj ON ss.subject_id = subj.id
    GROUP BY s.id
    ORDER BY s.username;
";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Subjects</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            padding: 20px;
            background: url('background1.webp') no-repeat center center fixed;
            background-size: cover;
        }
        .container {
            background: rgba(255, 255, 255, 0.8); 
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 600px;
            margin: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        th {
            background-color: #5cb85c;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Assigned Subjects to Students</h2>
        <table>
            <tr>
                <th>Student Name</th>
                <th>Subjects</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['student_name']) ?></td>
                    <td><?= htmlspecialchars($row['subjects']) ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
        <br>
        <button onclick="window.location.href='profile.php'">Go Back</button>
    </div>
</body>
</html>

<?php $stmt->close(); $conn->close(); ?>
