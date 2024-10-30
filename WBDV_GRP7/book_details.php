<?php
$host = "localhost";
$username = "root"; // Update if needed
$password = ""; // Update if needed
$dbname = "bookstore_db"; // Replace with your actual database name

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the book ID from the URL
if (isset($_GET['id'])) {
    $book_id = $_GET['id'];
    
    // Query to get details of the book
    $sql = "SELECT book_title, price FROM books WHERE id = $book_id"; // Adjusted column names
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $book = $result->fetch_assoc();
        echo "<h1>" . $book['book_title'] . "</h1>";
        echo "<p><strong>Price:</strong> ₱" . $book['price'] . "</p>";
    } else {
        echo "<p>Book not found.</p>";
    }
} else {
    echo "<p>No book ID provided.</p>";
}
?>
