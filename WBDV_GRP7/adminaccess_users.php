<?php
session_start();
include 'db_connection.php';

// Check if the user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch all users
$sql_users = "SELECT id, firstname, lastname, username, address, gender, birthdate, phone_number, account_status, role
              FROM users";
$stmt_users = $conn->prepare($sql_users);
if ($stmt_users === false) {
    die("Failed to prepare statement: " . htmlspecialchars($conn->error));
}
$stmt_users->execute();
$stmt_users->store_result();
$stmt_users->bind_result($id, $firstname, $lastname, $username, $address, $gender, $birthdate, $phone_number, $account_status, $role);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Access - All Users</title>
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
        <h1>All Registered Users</h1>
        <?php if ($stmt_users->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Username</th>
                        <th>Address</th>
                        <th>Gender</th>
                        <th>Birthdate</th>
                        <th>Phone Number</th>
                        <th>Account Status</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($stmt_users->fetch()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($id); ?></td>
                            <td><?php echo htmlspecialchars($firstname); ?></td>
                            <td><?php echo htmlspecialchars($lastname); ?></td>
                            <td><?php echo htmlspecialchars($username); ?></td>
                            <td><?php echo htmlspecialchars($address); ?></td>
                            <td><?php echo htmlspecialchars($gender); ?></td>
                            <td><?php echo htmlspecialchars($birthdate); ?></td>
                            <td><?php echo htmlspecialchars($phone_number); ?></td>
                            <td><?php echo htmlspecialchars($account_status); ?></td>
                            <td><?php echo htmlspecialchars($role); ?></td>
                            <td>
                                <form method="post" action="update_user_status.php">
                                    <input type="hidden" name="user_id" value="<?php echo $id; ?>">
                                    <select name="new_status">
                                        <option value="Active" <?php if ($account_status == 'Active') echo 'selected'; ?>>Active</option>
                                        <option value="Pending" <?php if ($account_status == 'Pending') echo 'selected'; ?>>Pending</option>
                                        <option value="Deactivated" <?php if ($account_status == 'Deactivated') echo 'selected'; ?>>Deactivated</option>
                                    </select>
                                    <button type="submit" class="button">Update Status</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No users found.</p>
        <?php endif; ?>
        <?php $stmt_users->close(); ?>
    </div>
	
	<div class="buttons">
        <a href="homepage.php" class="button">Back to Homepage</a>
    </div>
	
</body>
</html>
