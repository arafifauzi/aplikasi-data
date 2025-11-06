<?php
// views/auth/login.php
$email = $email ?? '';
$error = $error ?? '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FixHoax - Login</title>
    <link rel="stylesheet" href="../projekfixhoax/public/css/style.css">
</head>

<body>
    <div class="left-panel">
        <h2>Selamat Datang!</h2>
        <p>Masuk sekarang sebagai pengguna yang bijak dan keren!</p>
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
                <label>Email address</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <div class="form-group">
                <input type="submit" name="login" value="Login">
            </div>
            <a href="index.php?action=auth/register" class="signup-link">Don't have an account? Sign Up</a>
        </form>
    </div>
    <div class="right-panel">
        <img src="public/images/login-logo.jpg" alt="FixHoax Logo">
    </div>
</body>

</html>