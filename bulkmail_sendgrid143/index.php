<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Triunity Login</title>
  <link rel="stylesheet" href="login.css" />
</head>
<body>

  <div class="login-wrapper">
    <div class="glass-card">
      <div class="logo">
        <img src="https://cdn-icons-png.flaticon.com/512/1047/1047711.png" alt="Triunity Logo">
      </div>
      <h2>Welcome to <span>Triunity</span></h2>
      <p class="subtitle">Email Sending Software Login</p>

      <?php if (isset($_GET['error'])) { ?>
        <p class="error"><?php echo $_GET['error']; ?></p>
      <?php } ?>

      <form action="login.php" method="post">
        <div class="input-group">
          <label for="uname">User Name</label>
          <input type="text" id="uname" name="uname" placeholder="Enter your username" required>
        </div>

        <div class="input-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" placeholder="Enter your password" required>
        </div>

        <button type="submit">Login</button>
      </form>

      <p class="footer-text">© 2025 Triunity. All rights reserved.</p>
    </div>
  </div>

</body>
</html>
