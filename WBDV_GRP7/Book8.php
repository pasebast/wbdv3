<?php
session_start();
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

        // Verify password (assuming md5 is used)
        if (md5($password) === $row['password']) {
            // Password is correct, store user information in session
            $_SESSION['username'] = $row['username']; // Store username in session
            $_SESSION['user_id'] = $row['id']; // You can also store user id

            // Redirect to homepage
            header("Location: homepage.php");
            exit;
        } else {
            // Incorrect password
            $message = "Invalid password!";
        }
    } else {
        // No user found
        $message = "No user found with that username!";
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy(); // Destroy the session to log the user out
    header("Location: homepage.php"); // Redirect to homepage
    exit();
}

// Add a book to the cart when the form is submitted
if (isset($_POST['add_to_cart'])) {
    $book = array(
        'title' => $_POST['book_title'],
        'author' => $_POST['book_author'],
        'price' => floatval(str_replace(',', '', $_POST['book_price'])) // Remove commas before converting to float
    );

    // Initialize the cart if it's not set yet
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }

    // Add the book to the cart session
    $_SESSION['cart'][] = $book;

    // Redirect to homepage (or cart, depending on where you want them to go)
    header("Location: Book8.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>How to Win Friends & Influence People | Uplift Bookstore</title>
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
        }

        .header-buttons img {
          
             margin-left: 10px;
			margin-right: 20px;
            cursor: pointer;
        }
  .header-buttons img {
            width: 50px;
             margin-left: 10px;
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

        .book-detail-container {
            display: flex;
            justify-content: center;
            margin: 40px 20px;
            padding: 20px;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 1200px;
            margin: 40px auto;
        }

        .book-detail-container img {
            max-width: 500px;
            width: 100%;
            border-radius: 5px;
            margin-right: 40px;
			margin-top: 10px;
			max-height: 500px;
        }

        .book-detail {
            max-width: 7500px;
        }

        .book-title {
            font-size: 28px;
            font-weight: bold;
            color: #4a3c31;
        }

        .book-author {
            font-size: 18px;
            color: #777;
            margin: 10px 0;
        }

        .book-price {
            font-size: 24px;
            color: #4a3c31;
            font-weight: bold;
            margin: 15px 0;
        }

        .book-description {
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 20px;
            color: #333;
			text-align:justify;
        }

        .book-actions {
            display: flex;
            flex-direction: row;
            text-align:center;
        }

        .add-to-cart {
			background-color: #4a3c31;
            color: white;
            padding: 12px 150px;
            border: none;
            border-radius: 0px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
			margin-left: 150px;
        }


        .add-to-cart:hover, .add-to-wishlist:hover {
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
			.book-detail-container {
             flex-direction: column;
             align-items: center;
            }

        .book-detail-container img {
             margin-right: 0;
             margin-bottom: 20px;
            }

        .book-actions {
            flex-direction: column;
            }
        }
		
		    .main-content {
            padding: 20px;
            text-align: center;
        }


.username-message {
    font-size: 16px;
    color: white;
    margin-right: 10px; /* Space between username and logout button */
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
                <span class="username-message">Hello, <?php echo htmlspecialchars($_SESSION['firstname']); ?>!</span>
                <a href="homepage.php?logout=true"><button class="logout-button">Logout</button></a>
            <?php else: ?>
                <span class="username-message">Hello, Guest!</span>
				 <a href="login.php"><button class="logout-button">Login</button></a> <!-- Add this line for Login button -->
            <?php endif; ?>
        </div>
		 <div class="header-buttons">
            <a href="profile.php"><img src="https://raw.githubusercontent.com/SkyPrapai/oopr/main/login-removebg-preview.png" alt="Login" height="58px"></a>
         
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
	
	
    <!-- Book Detail Section -->
    <div class="book-detail-container">
        <img src="https://i.gr-assets.com/images/S/compressed.photo.goodreads.com/books/1650470724l/59366200.jpg" alt="Book Cover">
        <div class="book-detail">
            <h1 class="book-title">How to Win Friends & Influence People </h1>
            <p class="book-author">By  Dale Carnegie</p>
            <p class="book-price">₱599</p>
            <p class="book-description">

You can go after the job you want--and get it!<br>
You can take the job you have--and improve it!<br>
You can take any situation--and make it work for you!<br>
<br>
Dale Carnegie's rock-solid, time-tested advice has carried countless people up the ladder of success in their business and personal lives. One of the most groundbreaking and timeless bestsellers of all time, How to Win Friends & Influence People will teach you:
<br><br>
-Six ways to make people like you<br>
-Twelve ways to win people to your way of thinking<br>
-Nine ways to change people without arousing resentment<br>
<br>
And much more! Achieve your maximum potential--a must-read for the twenty-first century with more than 15 million copies sold!


            </p>
            <div class="book-actions">
       
		<form action="Book8.php" method="POST">
        <input type='hidden' name='book_title' value="How to Win Friends & Influence People">
        <input type="hidden" name="book_author" value="Dale Carnegie">
        <input type="hidden" name="book_price" value="599">
        <button type="submit" name="add_to_cart" class="add-to-cart">Add to Cart</button>
    </form>
            </div>
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
