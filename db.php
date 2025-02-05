<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
 
$host = "localhost";  // The server where MySQL is running
$user = "root";       // Default username for phpMyAdmin
$password = "";       // No password for MySQL
$dbname = "example"; // The name of your database
 
// Create a connection
$conn = new mysqli($host, $user, $password, $dbname);
 
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// } else {
//     echo "Database connected successfully!";
// }
?>