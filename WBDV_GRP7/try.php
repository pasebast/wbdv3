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
	$email = $_POST['email'];
    $confirm_password = $_POST['confirm_password'];
    $gender = $_POST['gender'];
    $birthdate = $_POST['birthdate'];
    $phone_number = $_POST['phone_number'];
$email = $_POST['username'];
    // Retrieve user from the database
 $sql = "SELECT id, firstname, lastname, email, phone_number, password,address FROM users WHERE username = '$username'";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // User found, verify password
        $row = $result->fetch_assoc();

        // Verify password (assuming md5 is used)
          if (md5($password) === $row['password']) {
            $_SESSION['username'] = $row['username']; 
			$_SESSION['firstname'] = $row['firstname'];
            $_SESSION['lastname'] = $row['lastname'];
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['phone_number'] = $row['phone_number'];
            $_SESSION['address'] = $row['address'];
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
    header("Location: homepage.php");
    exit();
	
}
// Check if a search query exists
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
    <title>Homepage | Uplift Bookstore</title>
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
			 z-index: 10; /* Brings search bar and results above other elements */
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
			  position: relative; /* Ensures the results are positioned properly */
   
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
			 z-index: 10; /* Brings search bar and results above other elements */
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
.book-banner,
.book-container {
    margin-top: 30px; /* Adjusts spacing to prevent overlap */
	z-index: 10;
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
    overflow-y: auto; /* Allows scrolling if results exceed max-height */
    position: relative;
    z-index: 9999; /* High enough to appear on top of other elements */
    background-color: white;
    border: 1px solid #ddd;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
}


.search-result {
    padding: 5px 0.10px;
    background-color: #fff;
    border-bottom: 1px solid #ddd;
    text-align: left;
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

    <div class="book-section">
    <div class="book-banner">Explore Our Collection</div>
    <div class="book-container">
        <!-- Book 1 -->
        <div class="book">
		
            <a href="Book1.php">
                <img src="https://m.media-amazon.com/images/I/51qXi-sZYrL._SY780_.jpg" alt="Book 1" height="200px">
            </a>
            <div class="book-info">
                <div class="book-title">The Things You Can See Only When You Slow Down</div>
                <div class="book-author">By Haemin Sunim</div>
                <div class="book-price">₱659</div>
                <form method="POST" action="homepage.php">
                    <input type="hidden" name="book_title" value="The Things You Can See Only When You Slow Down">
                    <input type="hidden" name="book_price" value="659">
                    <input type="hidden" name="book_author" value="Haemin Sunim">
                    <button type="submit" name="add_to_cart" class="add-to-cart">Add to Cart</button>
                </form>
            </div>
        </div>
        
        <!-- Book 2 -->
        <div class="book">
            <a href="Book2.php">
                <img src="https://cdn.kobo.com/book-images/24463cb4-28ad-48cb-807f-158cf6d11a92/1200/1200/False/atomic-habits-tiny-changes-remarkable-results.jpg" alt="Book 2" height="200px">
            </a>
            <div class="book-info">
                <div class="book-title">Atomic Habits</div>
                <div class="book-author">By James Clear</div>
                <div class="book-price">₱1,199</div>
                <form method="POST" action="homepage.php">
                    <input type="hidden" name="book_title" value="Atomic Habits">
                    <input type="hidden" name="book_price" value="1199">
                    <input type="hidden" name="book_author" value="James Clear">
                    <button type="submit" name="add_to_cart" class="add-to-cart">Add to Cart</button>
                </form>
            </div>
        </div>

        <!-- Book 3 -->
        <div class="book">
            <a href="Book3.php">
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQzzZW-gz_vtgxuN0f2w_HwDXjbifEdCFxhwg&s" alt="Book 3" height="200px">
            </a>
            <div class="book-info">
                <div class="book-title">The Subtle Art of Not Giving a F*ck</div>
                <div class="book-author">By Mark Manson</div>
                <div class="book-price">₱845</div>
                <form method="POST" action="homepage.php">
                    <input type="hidden" name="book_title" value="The Subtle Art of Not Giving a F*ck">
                    <input type="hidden" name="book_price" value="845">
                    <input type="hidden" name="book_author" value="Mark Manson">
                    <button type="submit" name="add_to_cart" class="add-to-cart">Add to Cart</button>
                </form>
            </div>
        </div>

        <!-- Book 4 -->
        <div class="book">
            <a href="Book4.php">
                <img src="https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1590806892i/53642699.jpg" alt="Book 4" height="200px">
            </a>
            <div class="book-info">
                <div class="book-title">The Mountain Is You</div>
                <div class="book-author">By Brianna Wiest</div>
                <div class="book-price">₱1,080</div>
                <form method="POST" action="homepage.php">
                    <input type="hidden" name="book_title" value="The Mountain Is You">
                    <input type="hidden" name="book_price" value="1080">
                    <input type="hidden" name="book_author" value="Brianna Wiest">
                    <button type="submit" name="add_to_cart" class="add-to-cart">Add to Cart</button>
                </form>
            </div>
        </div>

        <!-- Book 5 -->
        <div class="book">
            <a href="Book5.php">
                <img src="https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1615620038i/57393737.jpg" alt="Book 5" height="200px">
            </a>
            <div class="book-info">
                <div class="book-title">A Gentle Reminder</div>
                <div class="book-author">By Bianca Sparacino</div>
                <div class="book-price">₱1,029</div>
                <form method="POST" action="homepage.php">
                    <input type="hidden" name="book_title" value="A Gentle Reminder">
                    <input type="hidden" name="book_price" value="1029">
                    <input type="hidden" name="book_author" value="Bianca Sparacino">
                    <button type="submit" name="add_to_cart" class="add-to-cart">Add to Cart</button>
                </form>
            </div>
        </div>

        <!-- Book 6 -->
        <div class="book">
            <a href="Book6.php">
                <img src="https://assets.literal.club/2/ckrt59p0c2243131esqaoo45u7t.jpg?size=200" alt="Book 6" height="200px">
            </a>
            <div class="book-info">
                <div class="book-title">The Strength In Our Scars</div>
                <div class="book-author">By Bianca Sparacino</div>
                <div class="book-price">₱1,050</div>
                <form method="POST" action="homepage.php">
                    <input type="hidden" name="book_title" value="The Strength In Our Scars">
                    <input type="hidden" name="book_price" value="1050">
                    <input type="hidden" name="book_author" value="Bianca Sparacino">
                    <button type="submit" name="add_to_cart" class="add-to-cart">Add to Cart</button>
                </form>
            </div>
        </div>

        <!-- Book 7 -->
        <div class="book">
            <a href="Book7.php">
                <img src="https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1565249516l/51039323.jpg" alt="Book 7" height="200px">
            </a>
            <div class="book-info">
                <div class="book-title">You're Not Enough (and That's Okay)</div>
                <div class="book-author">By Allie Beth Stuckey</div>
                <div class="book-price">₱1,450</div>
                <form method="POST" action="homepage.php">
                    <input type="hidden" name="book_title" value="You're Not Enough (and That's Okay)">
                    <input type="hidden" name="book_price" value="1450">
                    <input type="hidden" name="book_author" value="Allie Beth Stuckey">
                    <button type="submit" name="add_to_cart" class="add-to-cart">Add to Cart</button>
                </form>
            </div>
        </div>

        <!-- Book 8 -->
        <div class="book">
            <a href="Book8.php">
                <img src="https://i.gr-assets.com/images/S/compressed.photo.goodreads.com/books/1650470724l/59366200.jpg" alt="Book 8" height="200px">
            </a>
            <div class="book-info">
                <div class="book-title">How to Win Friends & Influence People</div>
                <div class="book-author">By Dale Carnegie</div>
                <div class="book-price">₱599</div>
                <form method="POST" action="homepage.php">
                    <input type="hidden" name="book_title" value="How to Win Friends & Influence People">
                    <input type="hidden" name="book_price" value="599">
                    <input type="hidden" name="book_author" value="Dale Carnegie">
                    <button type="submit" name="add_to_cart" class="add-to-cart">Add to Cart</button>
                </form>
            </div>
        </div>

        <!-- Book 9 -->
        <div class="book">
            <a href="Book9.php">
                <img src="https://dynamic.indigoimages.ca/v1/books/books/194975944X/1.jpg?width=810&maxHeight=810&quality=85" alt="Book 9" height="200px">
            </a>
            <div class="book-info">
                <div class="book-title">When You're Ready, This Is How You Heal</div>
                <div class="book-author">By Brianna Wiest</div>
                <div class="book-price">₱1,125</div>
                <form method="POST" action="homepage.php">
                    <input type="hidden" name="book_title" value="When You're Ready, This Is How You Heal">
                    <input type="hidden" name="book_price" value="1125">
                    <input type="hidden" name="book_author" value="Brianna Wiest">
                    <button type="submit" name="add_to_cart" class="add-to-cart">Add to Cart</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#searchInput').on('keyup', function() {
        var query = $(this).val();
        if (query != '') {
            $.ajax({
                url: "fetch_search_results.php", // This is the PHP file we will create next
                method: "POST",
                data: { query: query },
                success: function(data) {
                    $('#searchResults').fadeIn();
                    $('#searchResults').html(data);
                }
            });
        } else {
            $('#searchResults').fadeOut();
        }
    });

    // Hide the results when the user clicks outside the search box
    $(document).on('click', function(event) {
        if (!$(event.target).closest('#searchForm, #searchResults').length) {
          
        }
    });
});

const searchForm = document.getElementById('searchForm');
const searchInput = document.getElementById('searchInput');
const searchResults = document.getElementById('searchResults');

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
        <p>&copy; 2024 UPLIFT BOOKSTORE. All Rights Reserved.</p>
    </footer>
</body>
</html>
