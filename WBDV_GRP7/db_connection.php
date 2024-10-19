<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "bookstore_db";
// Create a connection
$conn = mysqli_connect($host, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Optional: Set the charset to utf8 to handle special characters
mysqli_set_charset($conn, 'utf8');

// Your database connection is now established, and you can use $conn in your queries
?>
