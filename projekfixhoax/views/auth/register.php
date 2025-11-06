<?php
// views/auth/register.php
$name = $name ?? '';
$email = $email ?? '';
$error = $error ?? '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FixHoax - Get Started</title>
    <link rel="stylesheet" href="../projekfixhoax/public/css/style.css">
</head>

<body>
    <div class="left-panel register">
        <h2>Get Started Now</h2>
        <p>Enter your Credentials to make your account</p>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="success"><?php echo htmlspecialchars($_SESSION['success']);
                                    unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if ($error || isset($_SESSION['error'])): ?>
            <div class="error"><?php echo htmlspecialchars($error ?: $_SESSION['error']);
                                unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        <form method="post" action="">
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" placeholder="Enter your name" required>
            </div>
            <div class="form-group">
                <label>Email address</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            </div>
            <div class="form-group">
                <input type="submit" name="register" value="Register">
            </div>
            <a href="index.php?action=auth/login" class="login-link">Have an account? Sign In</a>
        </form>
    </div>
    <div class="right-panel">
        <img src="public/images/login-logo.jpg" alt="FixHoax Logo">
    </div>
</body>

</html>