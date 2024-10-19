<?php
session_start();
include('db_connection.php');

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Check if the token is valid
    $query = "SELECT user_id FROM email_verifications WHERE token = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Get user_id and update account status
        $stmt->bind_result($user_id);
        $stmt->fetch();
        $stmt->close();

        // Update the user's account status to Active
        $update_query = "UPDATE users SET account_status = 'Active' WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("i", $user_id);
        $update_stmt->execute();

        echo "Your account has been verified successfully! You can now <a href='homepage.php'>login</a>.";
    } else {
        echo "Invalid or expired token.";
    }
} else {
    echo "No token provided.";
}

$conn->close();
?>
