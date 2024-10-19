<?php
session_start();

$host = "localhost";
$username = "root";
$password = "";
$dbname = "bookstore_db";

// Create connection using old mysql functions
$conn = mysql_connect($host, $username, $password);
if (!$conn) {
    die("Connection failed: " . mysql_error());
}

mysql_select_db($dbname, $conn);

// Initialize variables
$firstname = isset($_SESSION['firstname']) ? $_SESSION['firstname'] : '';
$lastname = isset($_SESSION['lastname']) ? $_SESSION['lastname'] : '';
$gender = isset($_SESSION['gender']) ? $_SESSION['gender'] : '';
$phone_number = isset($_SESSION['phone_number']) ? $_SESSION['phone_number'] : '';
$address = isset($_SESSION['address']) ? $_SESSION['address'] : '';
$profile_picture = isset($_SESSION['profile_picture']) ? $_SESSION['profile_picture'] : '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = mysql_real_escape_string($_POST['firstname']);
    $lastname = mysql_real_escape_string($_POST['lastname']);
    $gender = mysql_real_escape_string($_POST['gender']);
    $phone_number = mysql_real_escape_string($_POST['phone_number']);
    $address = mysql_real_escape_string($_POST['address']);
    
    // File upload logic for PHP 5.2.1
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
            $profile_picture = mysql_real_escape_string($_FILES["profile_picture"]["name"]);
        } else {
            echo "Failed to upload file.";
        }
    }

    // Remove profile picture if the checkbox is selected
    if (isset($_POST['remove_picture']) && $_POST['remove_picture'] == 'on') {
        $profile_picture = '';  // Set profile picture to empty string
    }

    // Update user details in the database
    $user_id = $_SESSION['user_id'];
    
    if ($profile_picture !== '') {
        // If there is a profile picture (including if it's been updated)
        $sql = "UPDATE users SET firstname='$firstname', lastname='$lastname', gender='$gender', phone_number='$phone_number', address='$address', profile_picture='$profile_picture' WHERE id='$user_id'";
    } else {
        // If no profile picture or it's been removed
        $sql = "UPDATE users SET firstname='$firstname', lastname='$lastname', gender='$gender', phone_number='$phone_number', address='$address', profile_picture=NULL WHERE id='$user_id'";
    }

    $result = mysql_query($sql, $conn);
    
    if ($result) {
        // Update session variables
        $_SESSION['firstname'] = $firstname;
        $_SESSION['lastname'] = $lastname;
        $_SESSION['gender'] = $gender;
        $_SESSION['phone_number'] = $phone_number;
        $_SESSION['address'] = $address;
        $_SESSION['profile_picture'] = $profile_picture;  // Set profile picture in session

        // If picture removed, unset session variable
        if ($profile_picture === '') {
            unset($_SESSION['profile_picture']);
        }
        
        // Redirect to the profile page after successful update
        header("Location: profile.php");
        exit;
    } else {
        echo "Error updating record: " . mysql_error();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
       <title>Edit Profile | Uplift Bookstore</title>
    <style>
        body {
            font-family: 'Georgia', serif;
            background-color: #f4f0e6;
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
            background-color: rgba(74, 60, 49, 0.7);
            backdrop-filter: blur(10px);
            color: white;
            padding: 10px 0;
            display: flex;
            align-items: center;
            height: 80px;
            width: 100%;
            box-sizing: border-box;
        }
        header h3 {
            font-family: 'Georgia', serif;
            color: white;
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            flex-grow: 0.155;
            letter-spacing: 0.5px;
        }
        .profile-container {
            background-color: rgba(255, 255, 255, 0.8);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 400px;
            max-width: 100%;
            text-align: center;
            margin: 20px auto;
        }
        .profile-container h2 {
            font-size: 28px;
            margin-bottom: 20px;
            color: #4a3c31;
        }
        .profile-container form {
            display: flex;
            flex-direction: column;
        }
        .profile-container input, .profile-container select, .profile-container textarea {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
        }
        .edit-button {
            background-color: #6b5446;
            color: white;
            padding: 10px 20px;
            text-align: center;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 16px;
            margin-top: 20px;
        }
        .edit-button:hover {
            background-color: #4a3c31;
        }
      
		.add-to-cart:hover {
			background-color: #6b5446;
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
			transform: scale(1.2); /* Increase size on hover */
		
		}
       
        @media (max-width: 768px) {
            header h1 {
                font-size: 20px;
            }

            footer {
                padding: 1px 15px;
                font-size: 14px;
            }
        }

        @media (max-width: 480px) {
            header {
                height: 60px;
            }

            header img.logo {
                max-height: 50px;
            }  
        }

        .main-content {
            padding: 20px;
            text-align: center;
			 display: flex;
			align-items: center;
        }


.username-message {
    font-size: 16px;
    color: white;
    margin-right: 20px; /* Space between username and logout button */

}

.logout-button {
    font-size: 14px;
    color: white;
    background-color: #4a3c31;
    border: none;
    padding: 6px 12px;  /* Adjust padding for a better look */
    cursor: pointer;
    border-radius: 5px;

}

.logout-button:hover {
    background-color: #6b5446;
}

 .search-bar-container {
            display: flex;
            justify-content: center;
            flex-grow: 1;
            padding-right: 15px;
        }

        .search-bar {
            width: 100%;
            max-width: 600px;
            display: flex;
            align-items: center;
        }

        .search-bar input[type="text"] {
            width: 100%;
            padding: 10px;
            border-radius: 5px 0 0 5px;
            border: 1px solid #ccc;
            font-size: 14px;
            border-right: none;
        }

        .search-bar button {
            background-color: white;
            border: 1px solid #ccc;
            padding: 6.5px;
            cursor: pointer;
            border-radius: 0 5px 5px 0;
        }

        .search-bar button img {
            width: 20px;
            height: 20px;
        }
		
		.header-buttons {
            display: flex;
            align-items: center;
			vertical-align: middle;
        }

        .header-buttons img {
            width: 50px;
             margin-left: 20px;
			margin-right: 20px;
            cursor: pointer;
        }
		
		.headerlogo{
            display: flex;
            align-items: center;
        }
		
		.headerlogo img{
            margin-left: 10px;
			margin-right: 20px;
            cursor: pointer;
        }
		
		.categories-container {
            display: flex;
            justify-content: center;
            padding: 10px 0;
            background-color: rgb(245, 245, 220);
            border-bottom: 2px solid #4a3c31;
            margin: 0;
            width: 100%;
            box-sizing: border-box;
            text-align: center;
            flex-wrap: wrap;
        }

        .category-link {
            margin: 5px 15px;
            font-size: 16px;
            color: #4a3c31;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        .category-link:hover {
            color: #6b5446;
		}
		.remove-picture-container {
			display: flex;
			align-items: center;
			margin-top: 10px;
		}

		.remove-picture-container input[type="checkbox"] {
			margin-right: 10px;
		}
	
    </style>
</head>
<body>
      <header>
        <div class="headerlogo">
           <img src="460509624_1463840257655227_6223856608048021337_n.png" alt="Search" height="108px">
        </div>
        <h3>UPLIFT PAGE BOOKSTORE</h3>
		<div class="search-bar-container">
            <form action="search.php" method="GET" class="search-bar">
                <input type="text" name="query" placeholder="Search for books...">
                <button type="submit">
                    <img src="https://icons.veryicon.com/png/o/miscellaneous/prototyping-tool/search-bar-01.png" alt="Search">
                </button>
            </form>
			</div>
        <div class="main-content">
        <?php if (isset($_SESSION['firstname'])): ?>
            <!-- Show this when the user is logged in -->
            <span class="username-message">Hello, <?php echo htmlspecialchars($_SESSION['firstname']); ?>!</span>
            <a href="homepage.php?logout=true"><button class="logout-button">Logout</button></a>

         <!-- Profile Icon -->
            <a href="profile.php">
                <img src="https://raw.githubusercontent.com/SkyPrapai/oopr/main/login-removebg-preview.png" alt="Profile" height="58px">
            </a>
			
        <?php else: ?>
            <!-- Show this when no user is logged in -->
            <span class="username-message">Hello, Guest!</span>
            <a href="login.php"><button class="logout-button">Login</button></a>
        <?php endif; ?>
    </div>
        <div class="header-buttons">
            <a href="payment.php"><img src="https://raw.githubusercontent.com/SkyPrapai/oopr/main/cart-removebg-preview.png" alt="Cart" height="51px"></a>
        </div>
    </header>

    <div class="categories-container">
        <a href="homepage.php" class="category-link">Home</a>
        <a href="new.php" class="category-link">New Arrivals</a>
        <a href="sale.php" class="category-link">Sale!</a>
        <a href="best.php" class="category-link">Best Seller</a>
        <a href="faq.php" class="category-link">FAQs</a>
    </div>

    <div class="profile-container">
        <h2>Edit Your Profile</h2>
        <form action="edit_profile.php" method="post" enctype="multipart/form-data">
            <label for="firstname">First Name:</label>
            <input type="text" id="firstname" name="firstname" value="<?php echo htmlspecialchars($firstname); ?>">

            <label for="lastname">Last Name:</label>
            <input type="text" id="lastname" name="lastname" value="<?php echo htmlspecialchars($lastname); ?>">

            <label for="gender">Gender:</label>
            <select id="gender" name="gender">
                <option value="Male" <?php if ($gender == 'Male') echo 'selected'; ?>>Male</option>
                <option value="Female" <?php if ($gender == 'Female') echo 'selected'; ?>>Female</option>
                <option value="Other" <?php if ($gender == 'Other') echo 'selected'; ?>>Other</option>
            </select>

            <label for="phone_number">Contact Number:</label>
            <input type="text" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($phone_number); ?>">

            <label for="address">Address:</label>
            <textarea id="address" name="address"><?php echo htmlspecialchars($address); ?></textarea>

            <label for="profile_picture">Profile Picture:</label>
            <input type="file" id="profile_picture" name="profile_picture">

           <div class="remove-picture-container">
			<input type="checkbox" id="remove_picture" name="remove_picture">
			<label for="remove_picture">Remove Profile Picture</label>
			</div>

            <input type="submit" value="Save Changes" class="edit-button">
        </form>
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
        <p>&copy; 2024 UPLIFT BOOKSTORE. All Rights Reserved.</p>
    </footer>
</body>
</html>
