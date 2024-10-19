<?php
session_start();
header('Content-Type: text/html; charset=UTF-8');

// Check for form submission to generate the receipt
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = htmlspecialchars(trim($_POST['firstname']));
    $lastname = htmlspecialchars(trim($_POST['lastname']));
    $address = htmlspecialchars(trim($_POST['address']));
    $phone_number = htmlspecialchars(trim($_POST['phone_number']));
    $email = htmlspecialchars(trim($_POST['email']));
    $payment_method = htmlspecialchars(trim($_POST['payment_method']));

    // Initialize total price
    $total_price = 0;

    // If the cart is not empty, calculate the total price
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $book) {
            $price = floatval($book['price']);
            $total_price += $price;
        }

        // If PayPal is selected as the payment method, redirect to PayPal
        if ($payment_method == "PayPal") {
            $business_email = 'upliftpagebookstore07@gmail.com'; // Your business PayPal email
            $return_url = 'http://localhost/IT2Y1_MADRID_JAMIERIVAN/WBDV_GRP7/process_payment.php'; // URL to return after successful payment
            $cancel_url = 'http://yourwebsite.com/payment.php'; // URL if payment is cancelled

            // PayPal Redirection Form
            echo '
            <form id="paypal_form" action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post">
                <input type="hidden" name="cmd" value="_xclick">
                <input type="hidden" name="business" value="' . $business_email . '">
                <input type="hidden" name="item_name" value="Purchase from Uplift Page Bookstore">
				<input type="hidden" name="amount" value="' . number_format($total_price, 2, '.', '') . '">
                <input type="hidden" name="currency_code" value="PHP">
                <input type="hidden" name="return" value="' . $return_url . '">
                <input type="hidden" name="cancel_return" value="' . $cancel_url . '">
            </form>
            <script type="text/javascript">
                document.getElementById("paypal_form").submit(); // Auto-submit the form
            </script>';
            exit;
        } else {
            // Proceed with generating receipt for non-PayPal payment methods
            echo "<div style='background-color: #f9f9f9; padding: 20px; width: 600px; margin: 20px auto; font-family: Arial, sans-serif; border: 1px solid #ccc;'>";
            echo "<h2 style='text-align: center; font-size: 24px; margin-bottom: 20px;'>Uplift Page Bookstore</h2>";
            echo "<p style='text-align: center; font-size: 16px;'>120 MacArthur Hwy, Valenzuela, 1440 Metro Manila</p>";
            echo "<p style='text-align: center; font-size: 14px; margin-bottom: 40px;'>Phone: (02) 1234 5678</p>";
            echo "<h3 style='font-size: 18px; text-align: left; margin-bottom: 10px;'>Customer Receipt</h3>";
            echo "<hr style='border: none; border-top: 1px solid #ccc;'>";

            echo "<p><strong>Customer Name:</strong> $firstname $lastname</p>";
            echo "<p><strong>Contact Number:</strong> $phone_number</p>";
            echo "<p><strong>Shipping Address:</strong> $address</p>";
            echo "<p><strong>Payment Method:</strong> $payment_method</p>";
            echo "<hr style='border: none; border-top: 1px solid #ccc; margin-bottom: 20px;'>";

            echo "<table style='width: 100%; border-collapse: collapse;'>";
            echo "<tr>";
            echo "<th style='border-bottom: 2px solid #333; padding: 10px; text-align: left;'>Book Title</th>";
            echo "<th style='border-bottom: 2px solid #333; padding: 10px; text-align: left;'>Author</th>";
            echo "<th style='border-bottom: 2px solid #333; padding: 10px; text-align: right;'>Price (₱)</th>";
            echo "</tr>";

            // Add each book from the cart to the receipt and display total price
            foreach ($_SESSION['cart'] as $book) {
                $title = htmlspecialchars($book['title']);
                $author = htmlspecialchars($book['author']);
                $price = floatval($book['price']);
                echo "<tr>";
                echo "<td style='padding: 10px 0;'>$title</td>";
                echo "<td style='padding: 10px 0;'>$author</td>";
                echo "<td style='padding: 10px 0; text-align: right;'>₱" . number_format($price, 2) . "</td>";
                echo "</tr>";
            }

            echo "<tr>";
            echo "<td colspan='2' style='padding: 10px 0; text-align: right; font-weight: bold;'>Total</td>";
            echo "<td style='padding: 10px 0; text-align: right; font-weight: bold;'>₱" . number_format($total_price, 2) . "</td>";
            echo "</tr>";

            echo "</table>";
            echo "<hr style='border: none; border-top: 1px solid #ccc; margin-top: 20px;'>";
            echo "<p style='text-align: center; font-size: 14px;'>Thank you for shopping with Uplift Page Bookstore!</p>";
            echo "<p style='text-align: center; font-size: 12px;'>This is a computer-generated receipt and does not require a signature.</p>";
            echo "</div>";

            unset($_SESSION['cart']); // Clear the cart after the process
        }
    } else {
        header("Location: payment.php");
        exit;
    }
}
?>
