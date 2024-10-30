<?php
session_start();
include 'db_connection.php';

// Check if the user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $new_status = $_POST['new_status'];
    
    // Update user account status
    $sql_update = "UPDATE users SET account_status = ? WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    if ($stmt_update === false) {
        die("Failed to prepare statement: " . htmlspecialchars($conn->error));
    }
    $stmt_update->bind_param("si", $new_status, $user_id);
    $stmt_update->execute();
    $stmt_update->close();
}

// Redirect back to the admin users page
header("Location: adminaccess_users.php");
exit();
?>
