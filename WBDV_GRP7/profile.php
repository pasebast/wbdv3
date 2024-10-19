<?php
session_start();  // Start the session

// Ensure the session is active and the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page before any output is sent
    header('Location: login.php');
    exit();  // Ensure the script stops after redirection
}

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

$user_id = $_SESSION['user_id'];  // Get the user ID from the session

// Fetch user details, including profile picture, from the database
$sql = "SELECT firstname, lastname, gender, phone_number, address, profile_picture FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($firstname, $lastname, $gender, $phone_number, $address, $profile_picture);
$stmt->fetch();
$stmt->close();

// If no profile picture, use a default image
if (empty($profile_picture)) {
    $profilePicturePath = "images/default-profile.png";  // Default profile picture
} else {
    $profilePicturePath = "uploads/" . htmlspecialchars($profile_picture);  // Path to uploaded profile picture
}

// Logout logic
if (isset($_GET['logout'])) {
    session_destroy();  // End the session
    header("Location: homepage.php");
    exit();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
       <title>Profile | Uplift Bookstore</title>
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
            overflow x: hidden; /* Prevent vertical scrolling on the body */
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
		

     
        .book-section {
            position: relative;
            padding: 30px 10px;
        }

        .book-banner {
            font-size: 34px;
            font-weight: bold;
            color: #F8F8FF;
            text-align: center;
            margin-bottom: 20px;
		
        }

        .book-container {
            display: flex;
			overflow-x: auto; 
			overflow-y: hidden; 
			scroll-snap-type: x mandatory;
			gap: 10px;
			max-width: 1200px;
			margin: 0 auto;
			scroll-behavior: smooth;
			padding: 10px; 
			height: 350px;
        }

		.book {
			background-color: rgba(255, 255, 255, 0.9);
			border-radius: 5px;
			overflow: hidden;
			text-align: center;
			padding: 10px;
			flex-shrink: 0;
			width: 150px;
			scroll-snap-align: start;
			display: flex;
			flex-direction: column; 
			justify-content: space-between; 
			height: 320px; 
			transition: color 0.3s ease, background-color 0.3s ease;
		}

		.book:hover {
			color: rgba(255, 255, 255, 0.9);
			background-color:rgb(189, 183, 107); 
			padding: 2px 5px;
			border-radius: 3px;
		}
	
		.book-title{
			flex-grow: 1;
			font-weight: bold;
			font-size: 14px;
		}


		.book-author, .book-price {
			flex-grow: 1; 
		}

		.add-to-cart {
			background-color: #4a3c31;
			color: white;
			padding: 6px 10px;
			border: none;
			border-radius: 5px;
			cursor: pointer;
			font-size: 12px;
			transition: background-color 0.3s ease;
			margin-top: auto; 
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


.profile-page {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh; /* Full viewport height */
   
    padding: 20px;
}
.profile-container {
    background-color: rgba(255, 255, 255, 0.8);
    padding: 40px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    width: 400px;
    max-width: 100%; /* Ensure it is responsive */
    text-align: center;
}

.profile-container h2 {
    font-size: 28px;
    margin-bottom: 20px;
    color: #4a3c31;
}

.profile-details {
    margin-top: 20px;
    font-size: 18px;
    color: #333;
}
.profile-details p {
    margin: 10px 0;
    padding: 8px;
    background-color: #f8f4f0;
    border: 1px solid #ddd;
    border-radius: 5px;
}

.profile-details span {
    font-weight: bold;
    color: #4a3c31;
}


.edit-button {
    display: inline-block;
    margin-top: 20px;
    background-color: #6b5446;
    color: white;
    padding: 10px 20px;
    text-align: center;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    text-decoration: none;
    font-size: 16px;
}

.edit-button:hover {
    background-color: #4a3c31;
}

.profile-picture img {
    display: block;
    width: 150px;
    height: 150px;
    border-radius: 50%;
    margin: 0 auto;
    border: 3px solid #964B00;
    object-fit: cover;
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
	
 <div class="profile-page">
    <div class="profile-container">
        <h2>Your Personal Information</h2>
        
        <!-- Profile Picture -->
        <div class="profile-picture">
            <img src="<?php echo $profilePicturePath; ?>" alt="Profile Picture" id="profileImg" style="cursor: pointer;">
        </div>
        
        <!-- Profile Details -->
        <div class="profile-details">
            <p><span>First Name:</span> <?php echo htmlspecialchars($firstname); ?></p>
            <p><span>Last Name:</span> <?php echo htmlspecialchars($lastname); ?></p>
            <p><span>Gender:</span> <?php echo htmlspecialchars($gender); ?></p>
            <p><span>Contact Number:</span> <?php echo htmlspecialchars($phone_number); ?></p>
            <p><span>Address:</span> <?php echo htmlspecialchars($address); ?></p>
        </div>

        <a href="edit_profile.php" class="edit-button">Edit Profile</a>
        <a href="history.php" class="edit-button">Payment History</a>
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
        <p>&copy; 2024 UPLIFT BOOKSTORE. All Rights Reserved.</p>
    </footer>
	
	<script>
    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function(){
            var output = document.getElementById('profileImage');
            output.src = reader.result;
        }
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
</body>
</html>