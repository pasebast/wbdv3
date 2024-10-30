<?php
session_start();
include 'db_connection.php';

// Check if the user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch all orders
$sql_orders = "SELECT orders.order_number, orders.total_price, orders.address, orders.payment_method, orders.order_date,
               users.firstname, users.lastname, users.username
               FROM orders
               JOIN users ON orders.user_id = users.id";
$stmt_orders = $conn->prepare($sql_orders);
if ($stmt_orders === false) {
    die("Failed to prepare statement: " . htmlspecialchars($conn->error));
}
$stmt_orders->execute();
$stmt_orders->store_result();
$stmt_orders->bind_result($order_number, $total_price, $address, $payment_method, $order_date, $firstname, $lastname, $username);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Access - All Orders</title>
    <link rel="stylesheet" href="admin_styles.css"> <!-- Link to your CSS -->
</head>
<body>
<!-- pol -->
<body class="<?php echo (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') ? 'admin' : ''; ?>">
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <div class="admin-banner">
            Restricted Access - Admin Only
        </div>
    <?php endif; ?>
<!-- pol end -->
    <div class="admin-container">
        <h1>All Orders</h1>
        <?php if ($stmt_orders->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Order Number</th>
                        <th>Total Amount (₱)</th>
                        <th>Customer Name</th>
                        <th>Username</th>
                        <th>Shipping Address</th>
                        <th>Payment Method</th>
                        <th>Order Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($stmt_orders->fetch()): ?>
                        <tr>
                            <td><a href="javascript:void(0);" onclick="showOrderDetails('<?php echo $order_number; ?>')"><?php echo htmlspecialchars($order_number); ?></a></td>
                            <td>₱<?php echo htmlspecialchars(number_format($total_price, 2)); ?></td>
                            <td><?php echo htmlspecialchars($firstname . ' ' . $lastname); ?></td>
                            <td><?php echo htmlspecialchars($username); ?></td>
                            <td><?php echo htmlspecialchars($address); ?></td>
                            <td><?php echo htmlspecialchars($payment_method); ?></td>
                            <td><?php echo htmlspecialchars($order_date); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No orders found.</p>
        <?php endif; ?>
        <?php $stmt_orders->close(); ?>
    </div>
<div class="buttons">
        <a href="homepage.php" class="button">Back to Homepage</a>
    </div>
    <!-- Modal structure -->
    <div id="orderModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Order Details</h2>
            <div id="orderDetails"></div>
        </div>
    </div>

    <script>
        function showOrderDetails(orderNumber) {
            // Make an AJAX call to fetch order details
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'fetch_order_details.php?order_number=' + orderNumber, true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    // Update the modal content
                    document.getElementById('orderDetails').innerHTML = xhr.responseText;
                    // Display the modal
                    document.getElementById('orderModal').style.display = 'block';
                }
            };
            xhr.send();
        }

        function closeModal() {
            document.getElementById('orderModal').style.display = 'none';
        }
    </script>
</body>
</html>
