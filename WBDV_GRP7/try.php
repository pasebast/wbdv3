<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Metadata and Style definitions -->
</head>
<body>

<header>
    <!-- Header content with logo and profile/cart icons -->
</header>

<div class="profile-page">
    <div class="profile-container">
        <h2>User Profile</h2>
        <!-- Profile Picture Section -->
        <div class="profile-picture">
            <img id="profileImage" src="default-avatar.png" alt="Profile Picture">
        </div>

        <!-- Upload Button -->
        <form id="uploadForm" enctype="multipart/form-data" method="POST" action="upload_profile_picture.php">
            <input type="file" name="profileImage" id="profileImageInput" accept="image/*" style="display: none;" onchange="previewImage(event)">
            <label for="profileImageInput" class="upload-button">Upload New Picture</label>
            <button type="submit" class="upload-button">Save Picture</button>
        </form>

        <!-- Profile Details Section -->
        <div class="profile-details">
            <p><span>First Name:</span> <?php echo htmlspecialchars($firstname); ?></p>
            <p><span>Last Name:</span> <?php echo htmlspecialchars($lastname); ?></p>
            <p><span>Phone Number:</span> <?php echo htmlspecialchars($phone_number); ?></p>
            <p><span>Gender:</span> <?php echo htmlspecialchars($gender); ?></p>
            <p><span>Address:</span> <?php echo htmlspecialchars($address); ?></p>
        </div>

        <a href="edit_profile.php" class="edit-button">Edit Profile</a>
    </div>
</div>

<footer>
    <!-- Footer content -->
</footer>

<script>
    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function(){
            var output = document.getElementById('profileImage');
            output.src = reader.result;
        }
        reader.readAsDataURL(event.target.files[0]);
    }
</script>

</body>
</html>
