<?php
include('userFunctions.php');
session_start();

// Check if the user is logged in
$isLoggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;

// Fetch all users
$users = getAllUsers();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>User Management</title>
</head>
<body>
    <div class="container">
        

        <table>
            <thead>
                <tr>
                    <th colspan="6"><h1 id="registerHeading">Student Management System</h1></th>
                </tr>
            </thead>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="indexBody">
                <?php while ($user = $users->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['first_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td>
                            <button>
                                <a href="edit_user.php?id=<?php echo $user['id']; ?>">Edit</a>
                            </button>
                            <button onclick="showModal(<?php echo $user['id']; ?>)">Delete</button>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <br><br>
        <button onclick="window.location.href='register.php'" id="btn">Register New User</button>
        <button onclick="window.location.href='login.php'">Logout</button>
        <button onclick="window.location.href='subjects.php'">Add Subjects</button>
        <button onclick="window.location.href='student_subjects.php'">Assign Subjects</button>
        <button onclick="window.location.href='assignMonitor.php'">Assign Monitor</button> <!-- New Button -->
        <button onclick="window.location.href='monitorList.php'">View Monitors</button>


    </div>

    <!-- Modal for deletion confirmation -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <h2>Confirm Deletion</h2>
            <p>Are you sure you want to delete this user?</p>
            <button class="confirm-btn" onclick="confirmDelete()">Yes, Delete</button>
            <button class="cancel-btn" onclick="hideModal()">Cancel</button>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
