<?php
include('db.php');
// Instead of 'include('userFunctions.php');'



// MD5 Hash for Password Encryption
if (!function_exists('encryptPassword')) {
    // MD5 Hash for Password Encryption
    function encryptPassword($password) {
        return md5($password); // Return MD5 hash of the password
    }
}

// User Registration Function
function registerUser($username, $password, $first_name, $last_name, $email, $address, $role) {
    global $conn; // Assuming $conn is the connection to your database

    // Hash the password before storing it using bcrypt
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Fetch role_id from the roles table based on the role name
    $role_id = NULL;

    $query = "SELECT id FROM roles WHERE role_name = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $role);
    $stmt->execute();
    $stmt->bind_result($role_id);
    $stmt->fetch();
    $stmt->close();

    // If role_id is NULL, return false to indicate an error
    if ($role_id === NULL) {
        return false;
    }

        $query = "INSERT INTO students (username, password, first_name, last_name, email, address, role_id) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
   

    // Prepare and execute the insert query
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssssi", $username, $hashed_password, $first_name, $last_name, $email, $address, $role_id);

    if ($stmt->execute()) {
        $stmt->close();
        return true;
    } else {
        $stmt->close();
        return false;
    }
}





function checkUsernameExists($username) {
    global $conn; // Database connection

    // Check if the username exists in either the admins or students table
    $query = "SELECT COUNT(*) FROM students WHERE username = ?";
              
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    return $count > 0; // If count is greater than 0, the username already exists
}




// Get All Users
function getAllUsers() {
    global $conn;

    // Fetch role IDs dynamically for students
    $roleQuery = "SELECT id FROM roles WHERE role_name = 'student' OR role_name = 'monitor'";
    $roleResult = $conn->query($roleQuery);

    $roleIds = [];
    while ($row = $roleResult->fetch_assoc()) {
        $roleIds[] = $row['id']; // Collect student role IDs
    }

    if (empty($roleIds)) {
        return false; // No student roles found
    }

    // Convert array to comma-separated values for SQL query
    $roleIdsPlaceholder = implode(",", array_fill(0, count($roleIds), "?"));

    // Fetch students with the dynamically retrieved role IDs
    $sql = "SELECT * FROM students WHERE role_id IN ($roleIdsPlaceholder)";
    $stmt = $conn->prepare($sql);

    // Bind role IDs dynamically
    $stmt->bind_param(str_repeat("i", count($roleIds)), ...$roleIds);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result;
}


// Get Single User by ID
function getUserById($id) {
    global $conn;
    $sql = "SELECT * FROM students WHERE id=$id";
    $result = $conn->query($sql);
    return $result->fetch_assoc();
}

// Edit User
function editUser($id, $username, $first_name, $last_name, $email, $address) {
    global $conn;
    $sql = "UPDATE students SET username='$username', first_name='$first_name', last_name='$last_name', 
            email='$email', address='$address', updated_at=NOW() WHERE id=$id";
    return $conn->query($sql);
}

// Delete User
function deleteUser($id) {
    global $conn;
    $sql = "DELETE FROM students WHERE id=$id";
    return $conn->query($sql);
}

function updateExistingUsersPasswordHash() {
    global $conn;

    // Update password hashes for all students
    $result = $conn->query("SELECT id, username, password FROM students");
    while ($row = $result->fetch_assoc()) {
        $userId = $row['id'];
        $username = $row['username'];
        $password = $row['password'];

        // Check if the password is an MD5 hash
        if (strlen($password) === 32) { // MD5 hash length is 32 characters
            // Generate new bcrypt hash
            $newPasswordHash = password_hash($password, PASSWORD_BCRYPT);

            // Update the password in the database
            $stmt = $conn->prepare("UPDATE students SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $newPasswordHash, $userId);
            $stmt->execute();
            $stmt->close();
        }
    }

}

function loginUser($username, $password) {
    global $conn;

    // Fetch user from students table along with role_id from roles table
    $query = "SELECT s.*, r.id FROM students s 
              JOIN roles r ON s.role_id = r.id 
              WHERE s.username = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc(); // Fetch user details

        // Verify password
        if (password_verify($password, $user['password'])) {
            return $user; // Return user data if login is successful
        }
    }
    return false; // Invalid login
}


//  Fetch Subjects
function getAllSubjects() {
    global $conn;
    $query = "SELECT * FROM subjects";
    return $conn->query($query);
}

// Fetch students_subjects
function getAllStudentsSubjects() {
    global $conn;

    // Fetch role IDs dynamically for students (student & monitor)
    $roleQuery = "SELECT id FROM roles WHERE role_name IN ('student', 'monitor')";
    $roleResult = $conn->query($roleQuery);

    $roleIds = [];
    while ($row = $roleResult->fetch_assoc()) {
        $roleIds[] = $row['id']; // Collect student role IDs
    }

    if (empty($roleIds)) {
        return false; // No student roles found
    }

    // Convert role IDs to a comma-separated list for SQL query
    $roleIdsPlaceholder = implode(",", array_fill(0, count($roleIds), "?"));

    // SQL query to fetch students with subjects based on dynamically retrieved role IDs
    $sql = "SELECT 
                s.id AS student_id, 
                s.first_name, 
                s.last_name, 
                GROUP_CONCAT(sub.subject_name ORDER BY sub.subject_name SEPARATOR ', ') AS subjects 
            FROM student_subjects ss
            JOIN students s ON ss.student_id = s.id
            JOIN subjects sub ON ss.subject_id = sub.id
            WHERE s.role_id IN ($roleIdsPlaceholder)
            GROUP BY s.id";

    $stmt = $conn->prepare($sql);

    // Bind role IDs dynamically
    $stmt->bind_param(str_repeat("i", count($roleIds)), ...$roleIds);
    $stmt->execute();
    $result = $stmt->get_result();

    return $result;
}




function resetUserPassword($username, $currentPassword, $newPassword) {
    global $conn;

    // Fetch the current password hash from the database
    $stmt = $conn->prepare("SELECT password FROM students WHERE username = ? ");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        return false; // User not found
    }

    $row = $result->fetch_assoc();
    $storedPasswordHash = $row['password'];

    // Verify the current password
    if (!password_verify($currentPassword, $storedPasswordHash)) {
        return false; // Incorrect current password
    }

    // Hash the new password
    $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    // Update the password in the database
    $updateStmt = $conn->prepare("UPDATE students SET password = ? WHERE username = ?");
    $updateStmt->bind_param("ss", $newHashedPassword, $username);
    $updateStmt->execute();

    

    return true; // Password updated successfully
}


function checkStudentSubjectExists($student_id, $subject_id) {
    global $conn; // Use the global database connection variable

    // Prepare the SQL query to check for the combination
    $sql = "SELECT id FROM student_subjects WHERE student_id = ? AND subject_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Error preparing query: " . $conn->error);
    }

    // Bind the parameters
    $stmt->bind_param("ii", $student_id, $subject_id);

    // Execute the query
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    // Check if any record exists
    $exists = $result->num_rows > 0;

    // Free result and close the statement
    $result->free();
    $stmt->close();

    return $exists;
}

function getUserByUsername($username) {
    global $conn;
    $query = "SELECT * FROM students WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    return $user;
}


function editUserProfile($username, $first_name, $last_name, $email, $address) {
    global $conn;
    $query = "
        UPDATE students 
        SET first_name = ?, last_name = ?, email = ?, address = ? 
        WHERE username = ?
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssss", $first_name, $last_name, $email, $address, $username);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

function getMonitors() {
    global $conn;
    $query = "SELECT id, first_name, last_name FROM students WHERE role_id = 2";
    return $conn->query($query);
}


?>




