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
        'author' => $_POST['book_author'], // No escaping here
        'price' => floatval(str_replace(',', '', $_POST['book_price']))
    );

    // Initialize the cart if it's not set yet
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }

    // Add the book to the cart session
    $_SESSION['cart'][] = $book;

    // Redirect to homepage (or cart, depending on where you want them to go)
    header("Location: Book7.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>You're Not Enough (and That's Okay) | Uplift Bookstore</title>
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
			margin-top: 100px;
			max-height: 450px;
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
			margin-left: 250px;
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
        <img src="https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1565249516l/51039323.jpg" alt="Book Cover" >
        <div class="book-detail">
            <h1 class="book-title">You're Not Enough (and That's Okay) </h1>
            <p class="book-author">By  Allie Beth Stuckey</p>
            <p class="book-price">₱1,450</p>
            <p class="book-description">

From one of the sharpest Christian voices of her generation and host of the podcast. Relatable comes a framework for escaping our culture of trendy narcissism—and embracing God instead.
<br><br>
We're told that the key to happiness is self-love. Instagram influencers, mommy bloggers, self-help gurus, and even Christian teachers promise that if we learn to love ourselves, we'll be successful, secure, and complete. But the promise doesn't deliver. Instead of feeling fulfilled, our pursuit of self-love traps us in an exhausting cycle: as we strive for self-acceptance, we become addicted to self-improvement.
<br><br>
The truth is we can't find satisfaction inside ourselves because we are the problem. We struggle with feelings of inadequacy because we are inadequate. Alone, we are not good enough, smart enough, or beautiful enough. We're not enough--period. And that's okay because God is.
<br><br>
The answer to our insufficiency and insecurity isn't self-love, but God's love. In Jesus, we're offered a way out of our toxic culture of self-love and into a joyful life of relying on him for wisdom, satisfaction, and purpose. We don't have to wonder what it's all about anymore. This is it.
<br><br>
This book isn't about battling your not-enoughness; it's about embracing it. Allie Beth Stuckey, a Christian, conservative new mom, found herself at the dead end of self-love, and she wants to help you combat the false teachings and self-destructive mindsets that got her there. In this book, she uncovers the myths popularized by our self-obsessed culture, reveals where they manifest in politics and the church, and dismantles them with biblical truth and practical wisdom.


            </p>
            <div class="book-actions">
		<form action="Book7.php" method="POST">
        <input type='hidden' name='book_title' value="You're Not Enough (and That's Okay)">
        <input type="hidden" name="book_author" value="Allie Beth Stuckey">
        <input type="hidden" name="book_price" value="1450">
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
