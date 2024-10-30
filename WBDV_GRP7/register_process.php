<?php
// Database connection details
$servername = "localhost";
$dbusername = "root";  // Assuming you're using 'root' as the default username for MySQL
$dbpassword = "";      // Assuming there's no password set for your local MySQL setup
$dbname = "bookstore_db";

// Connect to the database
$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Process the registration form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $username = $_POST['username'];
    $address = $_POST['address'];
    $password = md5($_POST['password']);  // Use md5() for older PHP versions (less secure)
    $confirm_password = $_POST['confirm_password'];
    $gender = $_POST['gender'];
    $birthdate = $_POST['birthdate'];
    $phone_number = $_POST['phone_number'];
    $email = $username; // Assuming username is used as email

    // Check if the username already exists
    $checkUser = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($checkUser);
    if ($result->num_rows > 0) {
        echo "<script>alert('Username or Email already taken!'); window.location.href='register.php';</script>";
    } else if ($password !== md5($confirm_password)) {
        // Check if passwords match
        echo "<script>alert('Passwords do not match!'); window.location.href='register.php';</script>";
    } else {
        // Insert the user into the database
        $sql = "INSERT INTO users (firstname, lastname, username, address, gender, birthdate, phone_number, password, account_status, role)
                VALUES ('$firstname', '$lastname', '$username', '$address', '$gender', '$birthdate', '$phone_number', '$password', 'Pending', 'member')";
        
        // Execute the SQL statement and check for errors
        if ($conn->query($sql) === TRUE) {
            $user_id = $conn->insert_id; // Get the ID of the newly registered user
            $verification_token = md5(uniqid(rand(), true)); // Generate a unique token
            $expires = date("Y-m-d H:i:s", strtotime('+1 day')); // Set expiration date

            // Insert into email_verifications table
            $insert_verification = "INSERT INTO email_verifications (user_id, token, expires_at) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($insert_verification);
            if ($stmt === false) {
                die("Failed to prepare statement: " . htmlspecialchars($conn->error));
            }
            $stmt->bind_param("iss", $user_id, $verification_token, $expires);
            $stmt->execute();
            $stmt->close();

            // Prepare verification email
            $verification_link = "http://localhost/WBDV_GRP7/verify_email.php?token=" . $verification_token;

            // Include PHPMailer classes
            require 'src/PHPMailerAutoload.php'; // Ensure this path is correct
            $mail = new PHPMailer(); // Create a new PHPMailer instance
            
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
            
            // Set up PHPMailer to send the email
            $mail->setFrom('upliftpagebookstore@gmail.com', 'Uplift Page Bookstore Admin');
            $mail->addAddress($email); // Use the correct email variable
            $mail->Subject = 'Email Verification';
            $mail->Body = "Please verify your email by clicking the following link: <a href='$verification_link'>Verify Email</a>";
            
            if (!$mail->send()) {
                echo 'Email could not be sent. Mailer Error: ' . $mail->ErrorInfo;
                // Handle error accordingly
            } else {
                echo "<script>alert('Registration successful! Please check your email to verify your account.'); window.location.href='homepage.php';</script>";  // Redirect after success
            }
        } else {
            // Handle SQL error
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}

$conn->close();
