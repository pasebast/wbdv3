<?php
session_start(); // Start session to track user login status

$host = "localhost";
$username = "root";
$password = "";
$dbname = "bookstore_db";

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Message variable to store login errors
$message = "";

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Retrieve user from the database
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // User found, verify password
        $row = $result->fetch_assoc();

        // Check account status
        if ($row['account_status'] === 'Pending') {
            $message = "Your account is currently pending approval. Please check back later. <br><a href='resend_activation.php?username=$username'>Click here to resend activation token.</a>";
        } elseif ($row['account_status'] === 'Deactivated') {
            $message = "Your account has been deactivated. Please contact support for assistance.";
        } else {
            // Verify password (assuming md5 is used)
            if (md5($password) === $row['password']) {
                // Set all session variables
                $_SESSION['user_id'] = $row['id']; // Important: Store user ID for session tracking
                $_SESSION['firstname'] = $row['firstname'];
                $_SESSION['lastname'] = $row['lastname'];
                $_SESSION['address'] = $row['address'];
                $_SESSION['phone_number'] = $row['phone_number'];
                $_SESSION['gender'] = $row['gender'];

                // Redirect to homepage after successful login
                header("Location: homepage.php");
                exit;
            } else {
                // Incorrect password
                $message = "Invalid password!";
            }
        }
    } else {
        // No user found
        $message = "No user found with that username!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Uplift Bookstore</title>
    <style>
        body {
            font-family: 'Georgia', serif;
            background-image: url('https://64.media.tumblr.com/c25d3b2f64c96184584b831fba6bb0e2/tumblr_oyfsbzUOey1r9co7bo1_1280.gifv');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            margin: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        header {
            backdrop-filter: blur(10px);
            background-color: #4a3c31;
            color: white;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            height: 80px;
            position: relative;
            z-index: 1;
            overflow: hidden;
        }

        header h1 {
            font-size: 24px;
            flex-grow: 2;
            text-align: center;
            line-height: 1;
            letter-spacing: 1px;
        }

        .main-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 50px auto;
            max-width: 1200px;
            width: 100%;
            padding: 0 20px;
        }

        .logo-container {
            width: 50%;
            text-align: left;
            padding-right: 20px;
        }

        .logo-container img {
            width: 380px;
            height: 350px;
        }

        .container {
            background-color: rgb(225, 193, 110);
            padding: 30px;
            border-radius: 10px;
            border: 2px solid rgba(139, 69, 19, 0.2);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: center;
            z-index: 2;
            position: relative;
            backdrop-filter: blur(5px);
        }

        h2 {
            margin-bottom: 25px;
            color: #4a3c31;
            font-size: 28px;
        }

        input[type="text"],
        input[type="password"] {
            width: 90%;
            padding: 15px;
            margin: 15px auto;
            display: block;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 16px;
            box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        input[type="submit"]:hover {
            background-color: #6a2e0f;
        }

        input[type="submit"] {
            width: 100%;
            padding: 15px;
            background-color: #8b4513;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 18px;
            transition: background-color 0.3s ease;
        }

        .register-link {
            margin-top: 20px;
            font-size: 16px;
            color: #8b4513;
        }

        .register-link a {
            color: #8b4513;
            text-decoration: none;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        .message {
            margin: 20px 0;
            color: red;
            font-weight: bold;
        }

        footer {
            background-color: #4a3c31;
            color: white;
            padding: 0.5px 15px;
            text-align: center;
            margin-top: 0.5;
        }
		
        .social-media {
            margin-top: 1px;
        }

        .social-media a {
            margin: 0 3px;
            display: inline-block;
        }

        footer p {
            margin-top: 5px;
        }

        .social-media img {
            width: 35px;
            height: 35px;
            transition: transform 0.3s ease;
        }

        .social-media img:hover {
            transform: scale(1.2); 
        }
    </style>
</head>
<body>
    <header>
        <h1>Welcome to the Uplift Page Bookstore</h1>
    </header>

    <div class="main-content">
        <div class="logo-container">
            <img src="460509624_1463840257655227_6223856608048021337_n.png" alt="Bookstore Logo"> 
        </div>

        <div class="container">
            <h2>Login</h2>
            <form method="post" action="">
                <input type="text" name="username" placeholder="Email Address" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="submit" name="login" value="Login">
            </form>

            <div class="register-link">
                <a href="register.php">Don't have an account? Register.</a>
            </div>

            <?php if ($message): ?>
                <div class="message"><?php echo $message; ?></div>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <div class="social-media">
            <a href="https://facebook.com" target="_blank">
                <img src="https://raw.githubusercontent.com/SkyPrapai/oopr/main/fb-removebg-preview.png" alt="Facebook">
            </a>
            <a href="https://twitter.com" target="_blank">
                <img src="https://raw.githubusercontent.com/SkyPrapai/oopr/main/5ec14aa8686c6761c75b20a164a8afc2-removebg-preview.png" alt="Twitter">
            </a>
            <a href="https://instagram.com" target="_blank">
                <img src="https://raw.githubusercontent.com/SkyPrapai/oopr/main/images-removebg-preview.png" alt="Instagram">
            </a>
        </div>
        <p>&copy; 2024 Online Bookstore. All rights reserved.</p>
    </footer>
</body>
</html>
