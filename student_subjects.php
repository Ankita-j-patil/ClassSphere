<?php
include('userFunctions.php');

// Fetch all student-subject assignments
$studentsSubjects = getAllStudentsSubjects();

// Fetch all students and subjects for the dropdown in the popup
$allStudents = getAllUsers(); // Assuming this function exists in `userFunctions.php`
$allSubjects = getAllSubjects(); // Assuming this function exists in `userFunctions.php`

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student-Subjects</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script>
        // JavaScript for opening and closing the modal
        function showAssignPopup(event) {
            event.preventDefault(); // Prevent default action
            document.getElementById('assignModal').style.display = 'block';
        }

        function hideAssignPopup() {
            document.getElementById('assignModal').style.display = 'none';
        }
    </script>
</head>
<body>
    <div class="container">
        <h1 id="registerHeading">Student-Subject Assignments</h1>

        <!-- Table to display student-subject assignments -->
        <table id="subjectTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Student Name</th>
                    <th>Subject Name</th>
                </tr>
            </thead>
            <tbody>
    <?php 
    if ($studentsSubjects && $studentsSubjects->num_rows > 0) {
        while ($row = $studentsSubjects->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['student_id']); ?></td>
                <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                <td><?php echo htmlspecialchars($row['subjects']); ?></td>
            </tr>
        <?php } 
    } else { 
        echo "<tr><td colspan='3'>No data found.</td></tr>";
    } ?>
</tbody>

        </table>

        <br><br>

        <!-- Button to open the Assign Subject popup -->
        <button onclick="showAssignPopup(event)" id="assignButton">Assign Subject</button>
        <button onclick="window.location.href='index.php'" id="studentDetailsButton">Student Details</button>
    </div>

    <!-- Modal for assigning subjects -->
    <div id="assignModal" class="modal" style="display: none;">
        <div class="modal-content">
            <h2>Assign Subject to Student</h2>
            <form method="POST" action="assignSubjects.php" onsubmit="return validateAssignForm()">
    <label for="student_id">Select Students:</label>
    <select name="student_id[]" id="student_id" multiple class="select2" required>
        <?php while ($student = $allStudents->fetch_assoc()) { ?>
            <option value="<?php echo $student['id']; ?>">
                <?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?>
            </option>
        <?php } ?>
    </select>

    <label for="subject_id">Select Subjects:</label>
    <select name="subject_id[]" id="subject_id" multiple class="select2" required>
        <?php while ($subject = $allSubjects->fetch_assoc()) { ?>
            <option value="<?php echo $subject['id']; ?>">
                <?php echo htmlspecialchars($subject['subject_name']); ?>
            </option>
        <?php } ?>
    </select>

    <div class="modal-buttons">
        <button type="submit" class="confirm-btn">Assign</button>
        <button type="button" class="cancel-btn" onclick="hideAssignPopup()">Cancel</button>
    </div>
</form>

<!-- Initialize Select2 -->
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Select options",
            allowClear: true,
            width: '100%'
        });
    });
</script>

<!-- CSS for better styling -->
<style>
    .select2-container {
        width: 100% !important;
    }
</style>
        </div>
    </div>
</body>
</html>
