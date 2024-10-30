<?php
session_start();
include 'db_connection.php';

$order_number = $_GET['order_number'];

// Fetch order details
$sql_order_details = "SELECT orders.order_number, orders.total_price, orders.address, orders.payment_method, orders.order_date,
                      order_items.quantity, order_items.price, books.book_title, books.book_author
                      FROM orders
                      JOIN order_items ON orders.order_id = order_items.order_id
                      JOIN books ON order_items.book_id = books.id
                      WHERE orders.order_number = ?";
$stmt_order_details = $conn->prepare($sql_order_details);
if ($stmt_order_details === false) {
    die("Failed to prepare statement: " . htmlspecialchars($conn->error));
}
$stmt_order_details->bind_param("s", $order_number);
$stmt_order_details->execute();
$stmt_order_details->store_result();
$stmt_order_details->bind_result($order_number, $total_price, $address, $payment_method, $order_date, $quantity, $price, $book_title, $book_author);

$order_details = array();
while ($stmt_order_details->fetch()) {
    $order_details[] = array(
        'order_number' => $order_number,
        'total_price' => $total_price,
        'address' => $address,
        'payment_method' => $payment_method,
        'order_date' => $order_date,
        'quantity' => $quantity,
        'price' => $price,
        'book_title' => $book_title,
        'book_author' => $book_author
    );
}

$stmt_order_details->close();

if (!empty($order_details)): ?>
    <h3>Order Number: <?php echo htmlspecialchars($order_details[0]['order_number']); ?></h3>
    <p><strong>Order Date:</strong> <?php echo htmlspecialchars($order_details[0]['order_date']); ?></p> <!-- Display Order Date -->
    <p><strong>Shipping Address:</strong> <?php echo htmlspecialchars($order_details[0]['address']); ?></p>
    <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($order_details[0]['payment_method']); ?></p>
    <table>
        <thead>
            <tr>
                <th>Book Title</th>
                <th>Author</th>
                <th>Quantity</th>
                <th>Price (₱)</th>
                <th>Subtotal (₱)</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $subtotal = 0;
            foreach ($order_details as $item): 
                $item_subtotal = $item['price'] * $item['quantity'];
                $subtotal += $item_subtotal;
            ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['book_title']); ?></td>
                    <td><?php echo htmlspecialchars($item['book_author']); ?></td>
                    <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                    <td>₱<?php echo htmlspecialchars(number_format($item['price'], 2)); ?></td>
                    <td>₱<?php echo htmlspecialchars(number_format($item_subtotal, 2)); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <h4>Total Amount: ₱<?php echo htmlspecialchars(number_format($subtotal, 2)); ?></h4>
<?php else: ?>
    <p>No details found for this order.</p>
<?php endif; ?>
