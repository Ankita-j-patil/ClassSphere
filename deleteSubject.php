<?php
include('userFunctions.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['subject_id'])) {
    $subject_id = intval($_POST['subject_id']);

    if (deleteSubject($subject_id)) {
        echo "Subject deleted successfully!";
    } else {
        echo "Error deleting subject. It may be linked to other data.";
    }
} else {
    echo "Invalid request.";
}
?>
