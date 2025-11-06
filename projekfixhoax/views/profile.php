<?php
// views/profile.php
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FixHoax - Profile</title>
    <link rel="stylesheet" href="../projekfixhoax/public/css/partials.css">
    <link rel="stylesheet" href="../projekfixhoax/public/css/profile.css">

</head>

<body>

    <div class="profile-container">
        <div class="profile-header">
            <div>
                <h2>Welcome, <?php echo htmlspecialchars($user['name']); ?></h2>
                <p><?php echo date('D, d M Y', strtotime($user['created_at'])); ?></p>
            </div>
            <div class="user-info">
                <img src="assets/images/profile-pic.jpg" alt="Profile Picture">
                <p><?php echo htmlspecialchars($user['name']); ?></p>
                <p><?php echo htmlspecialchars($user['email']); ?></p>
            </div>
        </div>

        <?php if ($success) {
            echo "<div class='success'>" . htmlspecialchars($success) . "</div>";
        } ?>
        <?php if ($error) {
            echo "<div class='error'>" . htmlspecialchars($error) . "</div>";
        } ?>

        <form method="post" class="profile-form">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" value="<?php echo htmlspecialchars($user['name']); ?>" disabled>
            </div>
            <div class="form-group">
                <label>Nick Name</label>
                <input type="text" name="nickname" value="<?php echo htmlspecialchars($user['nickname'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label>Gender</label>
                <select name="gender" required>
                    <option value="">Your Gender</option>
                    <option value="Male" <?php if ($user['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                    <option value="Female" <?php if ($user['gender'] == 'Female') echo 'selected'; ?>>Female</option>
                    <option value="Other" <?php if ($user['gender'] == 'Other') echo 'selected'; ?>>Other</option>
                </select>
            </div>
            <div class="form-group">
                <label>Country</label>
                <select name="country" required>
                    <option value="">Your Country</option>
                    <option value="Indonesia" <?php if ($user['country'] == 'Indonesia') echo 'selected'; ?>>Indonesia</option>
                    <option value="United States" <?php if ($user['country'] == 'United States') echo 'selected'; ?>>United States</option>
                    <option value="United Kingdom" <?php if ($user['country'] == 'United Kingdom') echo 'selected'; ?>>United Kingdom</option>
                </select>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number'] ?? ''); ?>" placeholder="Your Phone Number">
            </div>
            <div>
                <button type="submit" name="save" class="save-btn">Save</button>
                <a href="index.php?action=logout" class="logout-btn">Logout</a>
            </div>
        </form>
    </div>

</body>

</html>