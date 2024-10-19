<?php

$servername = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "bookstore";

// Create connection
$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the search query from the URL
$query = isset($_GET['query']) ? $_GET['query'] : '';

// Prepare the SQL query to search the database
$sql = "SELECT book_title, price FROM orders WHERE book_title LIKE ?";
$stmt = $conn->prepare($sql);

// Check if the statement was prepared successfully
if (!$stmt) {
    die("Error preparing the statement: " . $conn->error);
}

$searchTerm = "%" . $query . "%";
$stmt->bind_param("s", $searchTerm);

// Execute the query
$stmt->execute();

// Bind the result variables
$stmt->bind_result($book_title, $price);

$output = '';
if ($stmt->fetch()) {
    // Fetch and display results
    do {
        $output .= '<li><strong>' . htmlspecialchars($book_title) . '</strong> - $' . htmlspecialchars($price) . '</li>';
    } while ($stmt->fetch());
} else {
    $output = '<p>No results found for "' . htmlspecialchars($query) . '"</p>';
}

echo '<ul>' . $output . '</ul>';

// Close the statement and connection
$stmt->close();
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Books</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h1>Search for Books</h1>
    <input type="text" id="searchQuery" placeholder="Search for books..." autocomplete="off">
    <div id="searchResults"></div>

    <script>
        $(document).ready(function() {
            $('#searchQuery').on('input', function() {
                var query = $(this).val();
                if (query.length > 2) { // Start searching after 3 characters
                    $.ajax({
                        url: 'search.php',
                        method: 'GET',
                        data: { query: query },
                        success: function(response) {
                            $('#searchResults').html(response);
                        }
                    });
                } else {
                    $('#searchResults').html(''); // Clear results if input is less than 3 characters
                }
            });
        });
    </script>
</body>
</html>

