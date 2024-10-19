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

$firstname = isset($_SESSION['firstname']) ? $_SESSION['firstname'] : '';
$lastname = isset($_SESSION['lastname']) ? $_SESSION['lastname'] : '';
$address = isset($_SESSION['address']) ? $_SESSION['address'] : '';
$phone_number = isset($_SESSION['phone_number']) ? $_SESSION['phone_number'] : '';
$gender = isset($_SESSION['gender']) ? $_SESSION['gender'] : '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $_SESSION['firstname'] = $_POST['firstname'];
    $_SESSION['lastname'] = $_POST['lastname'];
    $_SESSION['gender'] = $_POST['gender'];
    $_SESSION['address'] = $_POST['address'];
    $_SESSION['phone_number'] = $_POST['phone_number'];
	
    // Retrieve user from the database
    $sql = "SELECT id, firstname, lastname, email, gender, phone_number,address, password FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verify password (assuming md5 is used)
        if (md5($password) === $row['password']) {
            $_SESSION['firstname'] = $row['firstname'];
            $_SESSION['lastname'] = $row['lastname'];
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['gender'] = $row['gender'];
            $_SESSION['phone_number'] = $row['phone_number'];
			$_SESSION['address'] = $row['address'];
            header("Location: homepage.php");
            exit;
        } else {
            $message = "Invalid password!";
        }
    } else {
        $message = "No user found with that username!";
    }
}

if (isset($_SESSION['firstname'])) {
    $firstname = $_SESSION['firstname'];
    $lastname = isset($_SESSION['lastname']) ? $_SESSION['lastname'] : '';
    $email = isset($_SESSION['gender']) ? $_SESSION['gender'] : '';
    $phone_number = isset($_SESSION['phone_number']) ? $_SESSION['phone_number'] : '';
    $address = isset($_SESSION['address']) ? $_SESSION['address'] : ''; // Add this line
} else {
    header("Location: login.php");
    exit();
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: homepage.php");
    exit();
}

if (isset($_POST['add_to_cart'])) {
    $book = array(
        'title' => $_POST['book_title'],
        'author' => $_POST['book_author'],
        'price' => floatval(str_replace(',', '', $_POST['book_price']))
    );

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }

    $_SESSION['cart'][] = $book;

    header("Location: homepage.php");
    exit();
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | Uplift Bookstore</title>
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

        .cart-container {
            background-color: rgba(255, 255, 255, 0.9);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            border-radius: 10px;
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
        .cart-container {
    background-color: rgba(255, 255, 255, 0.9);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    width: 45%; /* Adjust cart container width */
    padding: 20px;
    border-radius: 10px;
}
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }
        table th {
            background-color: #f2f2f2;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .total {
            text-align: right;
            font-size: 18px;
            margin-top: 10px;
        }
        .buttons {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        .clear-cart-btn {
            background-color: #ff6f61;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .clear-cart-btn:hover {
            background-color: #ff4f41;
        }
        .checkout-form {
    background-color: rgba(255, 255, 255, 0.8);
    padding: 20px;
    border-radius: 8px;
    width: 45%; /* Adjust form width */
    flex-grow: 1;
}
        .checkout-form input, .checkout-form select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        .checkout-form button {
            width: 100%;
            background-color: #4CAF50;
            color: white;
            padding: 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .checkout-form button:hover {
            background-color: #45a049;
        }
		   footer {
            background-color: #4a3c31;
            color: white;
            padding: 0.5px 15px;
            text-align: center;
            margin-top: 280px;
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

    .content {
    display: flex;
    justify-content: space-between; /* Ensure elements are spaced out side by side */
    padding: 20px;
    max-width: 1200px;
    margin: 0 auto;
	gap: 20px;
}

 .total-container {
        
            padding: 0.00001px;

        }
		 .total-container h3 {
           text-align:right;
        }
    </style>
</head>
<body>
<header>
     <div class="headerlogo">
        <img src="460509624_1463840257655227_6223856608048021337_n.png" alt="Logo" height="108px">
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
        <a href="cart.html"><img src="https://raw.githubusercontent.com/SkyPrapai/oopr/main/cart-removebg-preview.png" alt="Cart" height="51px"></a>
    </div>
</header>
<div class="categories-container">
        <a href="homepage.php" class="category-link">Home</a>
        <a href="new.php" class="category-link">New Arrivals</a>
        <a href="sale.php" class="category-link">Sale!</a>
        <a href="best.php" class="category-link">Best Seller</a>
        <a href="faq.php" class="category-link">FAQs</a>
    </div>
	<div class="content">
<div class="cart-container">
    <h1>Your Cart</h1>
    <?php 
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
        <form method="post" action="payment.php">
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['cart'] as $book): ?>
                         <tr>
            <td><?php echo stripslashes(htmlspecialchars_decode($book['title'], ENT_QUOTES)); ?></td>
			<td><?php echo stripslashes(htmlspecialchars_decode($book['author'], ENT_QUOTES)); ?></td>
		<td>₱<?php echo htmlspecialchars($book['price']); ?></td>
	
        </tr>
                  
					 <?php 
     
            $total_price += $book['price']; 
            ?>
        <?php endforeach; ?>
                </tbody>
            </table>
			 <div class="total-container">
        <h3>Total Price: ₱<?php echo htmlspecialchars($total_price); ?></h3>
    </div>

            <div class="buttons">
                <!-- Clear Cart Button -->
                <button type="submit" name="clear_cart" class="clear-cart-btn">Clear Cart</button>
            </div>
        </form>
    <?php else: ?>
        <p>Your cart is empty.</p>
    <?php endif; ?>
</div>

<div class="checkout-form">
        <h2>Enter Shipping Details</h2>
        <form action="process_payment.php" method="POST">
         <label for="name">First Name:</label>
<input type="text" id="firstname" name="firstname" value="<?php echo htmlspecialchars($firstname); ?>" required readonly>

<label for="lastname">Last Name:</label>
<input type="text" id="lastname" name="lastname" value="<?php echo htmlspecialchars(!empty($_SESSION['lastname']) ? $_SESSION['lastname'] : ''); ?>" required readonly>

<label for="lastname">Email:</label>
<input type="text" id="email" name="email" value="" placeholder="Email" required>

<label for="address">Address:</label>
<input type="text" id="address" name="address" value="<?php echo htmlspecialchars($address); ?>" required readonly>

<label for="phone_number">Contact Number:</label>
<input type="text" id="phone_number" name="phone_number" value="<?php echo ($phone_number); ?>" required readonly>
			<laber for=payment_method">Payment Method:</label>
            <select name="payment_method" required>
                <option value="" disabled selected>Select Payment Method</option>  
                <option value="PayPal">PayPal</option>
				<option value="Gcash">Gcash</option>
				<option value="Cash on Delivery">Cash on Delivery</option>
            </select>
			 <input  type="hidden" name="total_price" value="<?php echo number_format($total_price, 2); ?>">
            <button type="submit">Proceed to Payment</button>
        </form>
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