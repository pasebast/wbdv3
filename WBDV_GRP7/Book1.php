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
	$firstname = $_POST['firstname'];
	$lastname = $_POST['lastname'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $gender = $_POST['gender'];
    $birthdate = $_POST['birthdate'];
    $phone_number = $_POST['phone_number'];

    // Retrieve user from the database
  $sql = "SELECT id, firstname, username, password FROM users WHERE username = '$username'";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // User found, verify password
        $row = $result->fetch_assoc();

        // Verify password (assuming md5 is used)
        if (md5($password) === $row['password']) {
            // Password is correct, store user information in session
           $_SESSION['firstname'] = $row['firstname']; // Store username in session
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
    header("Location: Book1.php");
    exit();
}

if (isset($_POST['query'])) {
    $search = strtolower($_POST['query']); // Convert query to lowercase for case-insensitive matching
    $search = mysqli_real_escape_string($conn, $search); // Sanitize input to prevent SQL injection

    // SQL query to search for books by title or author
    $sql = "SELECT book_title, book_author, book_price, book_image FROM books WHERE LOWER(book_title) LIKE '%$search%' OR LOWER(book_author) LIKE '%$search%'";

    // Execute the query
    $result = mysqli_query($conn, $sql);

    // Check if any results were returned
    if (mysqli_num_rows($result) > 0) {
        echo '<div class="search-results">';
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<div class="search-result">';
            echo '<img src="' . $row['book_image'] . '" alt="' . $row['book_title'] . '" class="result-image" style="height: 200px;">'; // Display book image
            echo '<div class="result-info">';
            echo '<strong class="result-title">' . $row['book_title'] . '</strong><br>';
            echo '<em class="result-author">' . $row['book_author'] . '</em><br>';
            echo '<span class="result-price">Price: ₱' . $row['book_price'] . '</span>';
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';
    } else {
        echo '<p>No results found</p>';
    }
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Things You Can See Only When You Slow Down | Uplift Bookstore</title>
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
			max-height: 400px;
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


.search-results-container {
    max-height: 300px; /* Adjust as needed */

    position: relative;
    z-index: 9999; /* High enough to appear on top of other elements */
    background-color: white;
    border: 1px solid #ddd;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
}


.search-result {
    padding: 5px 10px;
    background-color: #fff;
    border-bottom: 1px solid #ddd;
    text-align: left;
    height: 40px; /* Set a fixed height for uniformity */
    display: flex;
    align-items: center; /* Center content vertically */
}


.search-result a {
    color: #333;
    text-decoration: none;
    display: inline-block;
}

.search-result a:hover {
    color: #007bff;
}
.search-result:hover {
    transform: translateY(-2px); /* Slight lift effect on hover */
}

.result-title a {
    font-size: 12px;
    font-weight: 600;
    color: #333;
    text-decoration: none;
}

.result-title a:hover {
    color: #007bff;
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
           <!-- Search Bar -->
<!-- Search Bar -->
<form action="fetch_search_results.php" method="GET" class="search-bar" id="searchForm">
    <input type="text" name="query" id="searchInput" placeholder="Search for books...">
</form>

<!-- Search Results Container -->
<div id="searchResults" style="position: absolute; background-color: white; margin-top: 35px; max-height: 300px; overflow-y: auto; z-index: 9999; border: 1px solid #ddd; box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);">
    <!-- Results will appear here -->
</div>

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
	
    <div class="book-detail-container">
        <img src="https://raw.githubusercontent.com/SkyPrapai/oopr/main/book1.jpg" alt="Book Cover">
        <div class="book-detail">
            <h1 class="book-title">The Things You Can See Only When You Slow Down</h1>
            <p class="book-author">By Haemin Sunim</p>
            <p class="book-price">₱659</p>
            <p class="book-description">
               <strong> Is it the world that's busy, or is it my mind?</strong><br>
</br>
The world moves fast, but that doesn't mean we have to. This bestselling mindfulness guide by Haemin Sunim (which means "spontaneous wisdom"), a renowned Buddhist meditation teacher born in Korea and educated in the United States, illuminates a path to inner peace and balance amid the overwhelming demands of everyday life.

By offering guideposts to well-being and happiness in eight areas--including relationships, love, and spirituality--Haemin Sunim emphasizes the importance of forging a deeper connection with others and being compassionate and forgiving toward ourselves. The more than twenty full-color illustrations accompanying his teachings serve as calming visual interludes, encouraging us to notice that when you slow down, the world slows down.

This book is what you reach for when you're feeling low, it's a hug in book format. It's best to read when someone needs a little inspiration.

            </p>
           <div class="book-actions">
    <form action="Book1.php" method="POST">
        <input type="hidden" name="book_title" value="The Things You Can See Only When You Slow Down">
        <input type="hidden" name="book_author" value="Haemin Sunim">
        <input type="hidden" name="book_price" value="659">
        <button type="submit" name="add_to_cart" class="add-to-cart">Add to Cart</button>
    </form>
</div>

        </div>
    </div>
<script>

searchInput.addEventListener('input', function () {
    const formData = new FormData(searchForm);

    fetch('fetch_search_results.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        searchResults.innerHTML = data; // Display the results
    });
});

$(document).on('click', '.search-result a', function() {
    // Your click action here
});

</script>
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
