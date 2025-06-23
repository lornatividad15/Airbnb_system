<?php
session_start();
$modalScript = '';
if (isset($_SESSION['modal_message'])) {
    $modalScript = "<script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('modal');
            const modalMsg = document.getElementById('modal-message');
            modalMsg.textContent = " . json_encode($_SESSION['modal_message']) . ";
            modal.classList.add('show');
            setTimeout(() => {
                modal.classList.remove('show');
            }, 3000);
        });
    </script>";
    unset($_SESSION['modal_message']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="stylesheet" href="CSS/Login form.css" />
  <title>Login</title>
</head>
<body>

  <?= $modalScript ?>

  <header>
    <div class="header-inner">
      <a href="Main Page.php" class="logo">
        <img src="Images/logo-light-transparent.png" alt="Site Logo" style="height: 40px;">
      </a>
    </div>
  </header>

  <main>
    <div class="login-container">
      <h2>Login</h2>

      <form action="login.php" method="POST">
        <label for="email">Username or Email</label>
        <input type="text" id="email" name="email" placeholder="Enter username or email" required />

        <label for="password">Password</label>
        <div class="password-wrapper">
          <input type="password" id="password" name="password" placeholder="Enter password" required />
          <span class="toggle-password" onclick="togglePassword()">👁️</span>
        </div>

        <button type="submit" class="login-btn">Login</button> 

        <div class="login-options">
          <p><a href="admin_loginform.php">Log in as Admin</a></p>
          <p>Don't have an account? <a href="signup_form.php">Sign up</a></p>
        </div>
      </form>
    </div>
  </main>

  <div id="modal" class="modal-overlay">
    <div class="modal-box">
      <span class="close-btn" onclick="document.getElementById('modal').classList.remove('show')">&times;</span>
      <p id="modal-message"></p>
    </div>
  </div>

  <script>
    function togglePassword() {
      const pwdInput = document.getElementById("password");
      const toggleBtn = document.querySelector(".toggle-password");
      if (pwdInput.type === "password") {
        pwdInput.type = "text";
        toggleBtn.textContent = "🙈";
      } else {
        pwdInput.type = "password";
        toggleBtn.textContent = "👁️";
      }
    }
  </script>

</body>
</html>