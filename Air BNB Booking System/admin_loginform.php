<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Login</title>
  <link rel="stylesheet" href="CSS/admin_loginform.css" />
</head>
<body>

  <header>
    <div class="header-inner">
      <a href="Main Page.php" class="logo">
        <img src="Images/logo-light-transparent.png" alt="Logo" />
      </a>
    </div>
  </header>

  <main>
    <div class="admin-login-container">
      <h2>Admin Login</h2>
      <form id="adminLoginForm" action="admin_login.php" method="POST">
        <label for="adminId">Admin ID</label>
        <input type="text" id="adminId" name="admin_id" required />

        <label for="adminUsername">Username</label>
        <input type="text" id="adminUsername" name="username" required />

        <label for="adminPassword">Password</label>
        <input type="password" id="adminPassword" name="password" required />

        <button type="submit">Login</button>

        <p class="back-link"><a href="Login form.php">← Back to User Login</a></p>
      </form>
    </div>
  </main>
</body>
</html>