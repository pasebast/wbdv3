<?php
session_start();
header('Content-Type: text/html; charset=UTF-8');
include 'db_connection.php'; // Add your database connection details

date_default_timezone_set('Asia/Manila'); // Set to your local time zone

ob_start(); // Start output buffering

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = htmlspecialchars(trim($_POST['firstname']));
    $lastname = htmlspecialchars(trim($_POST['lastname']));
    $address = htmlspecialchars(trim($_POST['address']));
    $phone_number = htmlspecialchars(trim($_POST['phone_number']));
    $email = htmlspecialchars(trim($_POST['email']));
    $payment_method = htmlspecialchars(trim($_POST['payment_method']));

    // Set the payment method in session
    $_SESSION['payment_method'] = $payment_method;

    // Initialize total price
    $total_price = 0;
    
    // If the cart is not empty, calculate the total price
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $book) {
            $price = floatval($book['price']);
            $total_price += $price * $book['quantity']; // Calculate total based on quantity
        }
        
        // Generate a unique order number
        $order_number = uniqid();
        $order_date = date('Y-m-d H:i:s'); // Current date and time
        
        // Insert order into orders table
        $sql_order = "INSERT INTO orders (user_id, order_number, total_price, payment_method, address, order_date) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_order = $conn->prepare($sql_order);
        
        // Check if statement preparation was successful
        if ($stmt_order === false) {
            die("Failed to prepare statement: " . htmlspecialchars($conn->error));
        }
        
        $stmt_order->bind_param("isssss", $_SESSION['user_id'], $order_number, $total_price, $payment_method, $address, $order_date);
        
        if ($stmt_order->execute()) {
            $order_id = $stmt_order->insert_id;
            
            // Insert each cart item into order_items table
            foreach ($_SESSION['cart'] as $book) {
                // Fetch the book ID from the cart
                $book_id = $book['id'];
                
                // Verify the correct book ID is used
                if ($book_id) {
                    $sql_item = "INSERT INTO order_items (order_id, book_id, quantity, price) VALUES (?, ?, ?, ?)";
                    $stmt_item = $conn->prepare($sql_item);
                    $quantity = $book['quantity']; // Use the correct quantity
                    $price = $book['price'];
                    $stmt_item->bind_param("iiid", $order_id, $book_id, $quantity, $price);
                    $stmt_item->execute();
                    $stmt_item->close();
                } else {
                    echo "Error: Book ID not found for title " . $book['title'] . "<br>";
                }
            }
            
            // Clear the cart session after order is processed
            unset($_SESSION['cart']);
            
            // Redirect to a confirmation page
            header("Location: order_confirmation.php?order_number=$order_number");
            ob_end_flush(); // Flush the output buffer
            exit();
        } else {
            echo "Error: " . $sql_order . "<br>" . $conn->error;
        }
        
        $stmt_order->close();
        $conn->close();
    } else {
        header("Location: payment.php");
        ob_end_flush(); // Flush the output buffer
        exit();
    }
}
