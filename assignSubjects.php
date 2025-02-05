<?php
include_once 'userFunctions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_ids = $_POST['student_id']; // Array of selected students
    $subject_ids = $_POST['subject_id']; // Array of selected subjects

    if (!is_array($student_ids) || empty($student_ids) || !is_array($subject_ids) || empty($subject_ids)) {
        echo "<script>alert('Please select at least one student and one subject.'); window.location.href='student_subjects.php';</script>";
        exit;
    }

    $assigned = [];
    $failed = [];

    foreach ($student_ids as $student_id) {
        foreach ($subject_ids as $subject_id) {
            $student_id = intval($student_id);
            $subject_id = intval($subject_id);

            // Check if the student-subject combination already exists
            if (!checkStudentSubjectExists($student_id, $subject_id)) {
                $sql = "INSERT INTO student_subjects (student_id, subject_id, created_at) VALUES (?, ?, NOW())";
                $stmt = $conn->prepare($sql);

                if ($stmt) {
                    $stmt->bind_param("ii", $student_id, $subject_id);
                    if ($stmt->execute()) {
                        $assigned[] = "$student_id-$subject_id";
                    } else {
                        $failed[] = "$student_id-$subject_id";
                    }
                    $stmt->close();
                } else {
                    $failed[] = "$student_id-$subject_id";
                }
            }
        }
    }

    // Show appropriate messages
    if (!empty($assigned)) {
        echo "<script>alert('Subjects assigned successfully to selected students!'); window.location.href='student_subjects.php';</script>";
    } elseif (!empty($failed)) {
        echo "<script>alert('Some assignments failed. Please try again.'); window.location.href='student_subjects.php';</script>";
    } else {
        echo "<script>alert('All selected subjects were already assigned to the selected students.'); window.location.href='student_subjects.php';</script>";
    }
}
?>
