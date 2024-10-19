<?php
// Start the session
session_start();
include('db_connection.php'); // Include your database connection file

// Check if the email is set in the query string
if (isset($_GET['username'])) {
    $username = $_GET['username']; // Changed to 'username' for consistency with login.php

    // Generate a new verification token
    $verification_token = md5(uniqid(rand(), true)); // Generate a unique token
    $expires = date("Y-m-d H:i:s", strtotime('+1 day')); // Set expiration date

    // Get the user ID based on the username (assuming username is an email)
    $query = "SELECT id FROM users WHERE username = ?"; // Assuming username is the email
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();

    if ($user_id) {
        // Insert the new token into the email_verifications table
        $insert_verification = "INSERT INTO email_verifications (user_id, token, expires_at) VALUES (?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_verification);
        $insert_stmt->bind_param("iss", $user_id, $verification_token, $expires);
        
        if ($insert_stmt->execute()) {
            // Prepare the verification email
            $verification_link = "http://localhost/WBDV_GRP7/verify_email.php?token=" . $verification_token;

            // Include PHPMailer classes
            require 'src/PHPMailerAutoload.php'; // Ensure this path is correct
            $mail = new PHPMailer();

            // Set up PHPMailer
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'upliftpagebookstore@gmail.com'; // Your email
            $mail->Password   = 'dbmvztvsdijirvpy'; // Your app password
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            // Set email format to HTML
            $mail->isHTML(true);
            $mail->setFrom('upliftpagebookstore@gmail.com', 'Uplift Page Bookstore Admin');
            $mail->addAddress($username); // Using the username (email)
            $mail->Subject = 'Email Verification';
            $mail->Body = "Please verify your email by clicking the following link: <a href='$verification_link'>Verify Email</a>";

            // Send the email
            if ($mail->send()) {
                echo "<script>alert('Activation link has been resent. Please check your email.'); window.location.href='homepage.php';</script>";
            } else {
                echo "<script>alert('Failed to resend activation link. Please try again.'); window.location.href='homepage.php';</script>";
            }
        } else {
            echo "<script>alert('Failed to insert verification token.'); window.location.href='homepage.php';</script>";
        }
        $insert_stmt->close();
    } else {
        echo "<script>alert('No user found with that username.'); window.location.href='homepage.php';</script>";
    }
} else {
    // If the username is not set in the query string
    echo "<script>alert('No username provided.'); window.location.href='homepage.php';</script>";
}
?>
