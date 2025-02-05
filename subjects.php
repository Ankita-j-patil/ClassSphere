<?php
include('userFunctions.php');

// Fetch all subjects from the database
$subjects = getAllSubjects();

// Check if a subject was added successfully and display the success message
$subjectAdded = isset($_GET['added']) && $_GET['added'] == 'true';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Subjects</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1 id="registerHeading">Subjects</h1>

        <!-- Display Success Message if a Subject was Added -->
        <?php if ($subjectAdded): ?>
            <div class="success-message">
                <h2>Subject Added Successfully!</h2>
                <a href="index.php" class="student-details-btn">Student Details</a>
            </div>
        <?php endif; ?>

        <!-- Table to display the subjects -->
        <table id="subjectTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Subject Name</th>
                </tr>
            </thead>
            <tbody id="indexBody">
                <?php while($subject = $subjects->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($subject['id']); ?></td>
                        <td><?php echo htmlspecialchars($subject['subject_name']); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <!-- Button for adding subjects (triggers the modal) -->
        <button id="addSubjectBtn">Add Subject</button>
        <a href="index.php"><button id="studentDetails">Student Details</button></a>

        <!-- Modal for adding a new subject -->
        <div id="addSubjectModal" class="modal">
            <div class="modal-content">
                <span class="close-btn">&times;</span>
                <h2>Add New Subject</h2>
                <form id="addSubjectForm" action="add_subject.php" method="POST">
                    <label for="subject_name">Subject Name:</label>
                    <input type="text" id="subject_name" name="subject_name" required>
                    <button type="submit">Add Subject</button>
                </form>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
