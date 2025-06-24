<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Login</title>
  <link rel="stylesheet" href="CSS/admin_loginform.css" />
  <link rel="stylesheet" href="CSS/modal_global.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        <input type="text" id="adminId" name="admin_id" placeholder="Enter admin ID" required />

        <label for="adminUsername">Username</label>
        <input type="text" id="adminUsername" name="username" placeholder="Enter username" required />

        <label for="adminPassword">Password</label>
        <div class="password-wrapper">
          <input type="password" id="adminPassword" name="password" placeholder="Enter password" required autocomplete="off" />
        </div>


        <button type="submit">Login</button>

        <p class="back-link"><a href="Login form.php">← Back to User Login</a></p>
      </form>
    </div>
  </main>

  <?php if (isset($_SESSION['modal_message'])): ?>
    <div id="modalOverlay" class="modal-overlay show">
      <div class="modal-box <?php echo isset($_SESSION['modal_type']) ? $_SESSION['modal_type'] : 'error'; ?>">
        <button class="close-btn" onclick="closeModal()">&times;</button>
        <span id="modalMessage"><?php echo htmlspecialchars($_SESSION['modal_message']); ?></span>
      </div>
    </div>
    <script src="JS/admin_loginform.js"></script>
    <?php unset($_SESSION['modal_message'], $_SESSION['modal_type']); ?>
  <?php endif; ?>

</body>
</html>