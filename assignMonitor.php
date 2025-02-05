<?php
include('db.php');
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    die("Access denied.");
}

// Fetch students (excluding monitors/admins)
$query = "SELECT id, username, first_name, last_name FROM students WHERE role_id = 1";
$result = $conn->query($query);

$successMessage = "";
$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['student_id'])) {
    $student_id = $_POST['student_id'];

    // Update role to monitor
    $updateQuery = "UPDATE students SET role_id = 2 WHERE id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("i", $student_id);

    if ($stmt->execute()) {
        $successMessage = "Student assigned as monitor successfully!";
    } else {
        $errorMessage = "Failed to assign monitor.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Monitor</title>
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
            width: 350px;
        }
        h2 {
            color: #333;
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
            display: block;
            margin-bottom: 8px;
        }
        select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        button {
            background-color: #5cb85c;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }
        button:hover {
            background-color: #4cae4c;
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
        /* Modal Styles */
        .modal {
            display: none; /* Hide modal initially */
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            width: 300px;
        }
        .modal-content h3 {
            margin-bottom: 15px;
            color: #333;
        }
    </style>
    <script>
        function showSuccessPopup(message) {
            const modal = document.getElementById("successModal");
            const messageText = document.getElementById("successMessage");

            if (message) {
                messageText.innerText = message;
                modal.style.display = "flex";

                setTimeout(function () {
                    window.location.href = "index.php";
                }, 2000);
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Assign Monitor</h2>

        <form method="post" action="assignMonitor.php">
            <label for="student">Select Student:</label>
            <select name="student_id" id="student" required>
                <option value="">-- Select Student --</option>
                <?php while ($student = $result->fetch_assoc()) { ?>
                    <option value="<?php echo $student['id']; ?>">
                        <?php echo htmlspecialchars($student['first_name'] . " " . $student['last_name']); ?>
                    </option>
                <?php } ?>
            </select>
            <button type="submit">Assign</button>
        </form>

        <a href="index.php" class="back-link">Back to Dashboard</a>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="modal" style="display: none;">
        <div class="modal-content">
            <h3 id="successMessage"></h3>
        </div>
    </div>

    <?php if ($successMessage): ?>
        <script>
            showSuccessPopup("<?php echo $successMessage; ?>");
        </script>
    <?php elseif ($errorMessage): ?>
        <script>
            showSuccessPopup("<?php echo $errorMessage; ?>");
        </script>
    <?php endif; ?>
</body>
</html>
