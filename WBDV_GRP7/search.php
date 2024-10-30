<?php
// Database connection settings
$host = 'localhost';
$dbname = 'bookstore_db';
$username = 'root';  // replace with your actual DB username
$password = '';      // replace with your actual DB password

// Connect to the database
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Check if there's a search query
if (isset($_GET['query'])) {
    $search = $_GET['query'];
    
    // SQL to fetch books based on the search term
    $stmt = $conn->prepare("SELECT * FROM books WHERE title LIKE :search");
    
    // Execute the query with a bound parameter
    $stmt->bindParam(':search', $searchTerm, PDO::PARAM_STR);
    $searchTerm = '%' . $search . '%';
    $stmt->execute();
    
    // Fetch the results
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Display the results
    if ($books) {
        foreach ($books as $book) {
            echo '<p>' . htmlspecialchars($book['book_title']) . '</p>';
        }
    } else {
        echo '<p>No results found.</p>';
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Search</title>
    <script>
        function searchBooks() {
            const query = document.getElementById('searchInput').value;
            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'search.php?query=' + query, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    document.getElementById('results').innerHTML = xhr.responseText;
                }
            };
            xhr.send();
        }
    </script>
</head>
<body>
    <h1>Search Books</h1>
    <input type="text" id="searchInput" onkeyup="searchBooks()" placeholder="Search for books...">
    <div id="results"></div>
</body>
</html>
