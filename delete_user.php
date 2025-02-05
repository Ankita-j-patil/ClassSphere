<?php
include_once 'userFunctions.php';
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Call deleteUser function
    if (deleteUser($id)) {
        header("Location: index.php?message=User+deleted+successfully");
    } else {
        header("Location: index.php?message=Error+deleting+user");
    }
    exit();
}
?>
