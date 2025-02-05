<?php
include('db.php');
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    die("Access denied.");
}

// Fetch monitors (role_id = 2)
$sql=  "SELECT id from roles where role_name='monitor' " ;
$result1=$conn->query($sql);
$monitor1 = $result1->fetch_assoc();
$roleId=$monitor1['id'];
$query = $conn->prepare("SELECT id, username, first_name, last_name, email FROM students WHERE role_id = ? ");
$query->bind_param("i",$monitor1['id'] );
$query->execute();
$result = $query->get_result();
?>
 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitor List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            background: url('background1.webp') no-repeat center center fixed;
            background-size: cover;
        }
        .container {
            background: rgba(255, 255, 255, 0.8); 
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 400px;
        }
        h2 {
            color: #333;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #5cb85c;
            color: white;
        }
        .back-link {
            display: inline-block;
            margin-top: 15px;
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Assigned Monitors</h2>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Name</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($monitor = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($monitor['id']); ?></td>
                        <td><?php echo htmlspecialchars($monitor['username']); ?></td>
                        <td><?php echo htmlspecialchars($monitor['first_name'] . " " . $monitor['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($monitor['email']); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <br>
        <a href="index.php" class="back-link">Back to Dashboard</a>
    </div>
</body>
</html>
