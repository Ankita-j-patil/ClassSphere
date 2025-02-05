<?php
include('db.php'); // Ensure the correct DB connection

$subjectExist = false; // Flag to check if subject exists

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subjectName = $_POST['subject_name'];

    // Check if the subject already exists
    $stmt = $conn->prepare("SELECT id FROM subjects WHERE subject_name = ?");
    $stmt->bind_param("s", $subjectName);
    $stmt->execute();
    $result = $stmt->get_result();

    // If the subject exists, set the flag to true
    if ($result->num_rows > 0) {
        $subjectExist = true;
    } else {
        // Subject does not exist, insert it
        $stmt = $conn->prepare("INSERT INTO subjects (subject_name) VALUES (?)");
        $stmt->bind_param("s", $subjectName);
        
        if ($stmt->execute()) {
            header("Location: subjects.php"); // Redirect back to the subjects page
            exit();
        } else {
            echo "Error executing statement: " . $stmt->error;
        }
    }

    $stmt->close(); // Close the prepared statement
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Subject</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1 id="registerHeading">Add Subject</h1>
        <form method="POST" class="registerForm">
            <input type="text" name="subject_name" placeholder="Subject Name" required>
            <button type="submit">Add Subject</button>
        </form>

        <!-- Modal for subject already added -->
        <div id="subjectModal" class="modal" style="display: <?php echo $subjectExist ? 'block' : 'none'; ?>;">
            <div class="modal-content">
                
                <h2>This subject has already been added!</h2>
                <button onclick="window.location.href='subjects.php'">Close</button>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>




