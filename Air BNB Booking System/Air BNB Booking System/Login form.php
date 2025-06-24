<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="stylesheet" href="CSS/Login form.css" />
  <link rel="stylesheet" href="CSS/modal_global.css" />
  <title>Login</title>
</head>
<body>

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
          <span class="toggle-password" onclick="togglePassword('password')">&#128065;</span>
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
    <div class="modal-box" id="modalBox">
      <button class="close-btn" onclick="closeModal()">&times;</button>
      <span id="modal-message"></span>
    </div>
  </div>

  <script>
    function togglePassword(fieldId) {
      const pwdInput = document.getElementById(fieldId);
      const icon = event.currentTarget;
      if (pwdInput.type === "password") {
        pwdInput.type = "text";
        icon.innerHTML = "&#128564;"; // Unicode for eye with slash
      } else {
        pwdInput.type = "password";
        icon.innerHTML = "&#128065;"; // Unicode for eye
      }
    }

    function closeModal() {
      document.getElementById('modal').classList.remove('show');
    }
    <?php if (isset($_SESSION['modal_message'])): ?>
      document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('modal');
        const modalMsg = document.getElementById('modal-message');
        modalMsg.textContent = <?php echo json_encode($_SESSION['modal_message']); ?>;
        modal.classList.add('show');
        setTimeout(() => {
          modal.classList.remove('show');
        }, 3000);
      });
    <?php unset($_SESSION['modal_message']); endif; ?>

    // Show modal if account deleted (from ?deleted=1 in URL)
    document.addEventListener('DOMContentLoaded', function () {
      const params = new URLSearchParams(window.location.search);
      if (params.get('deleted') === '1') {
        const modal = document.getElementById('modal');
        const modalMsg = document.getElementById('modal-message');
        modalMsg.innerHTML = `<span style='color:green; font-size:1.5em; margin-right:8px;'><i class='fa-solid fa-check-circle'></i></span>Account deleted successfully.`;
        modal.classList.add('show');
        setTimeout(() => {
          modal.classList.remove('show');
        }, 3000);
      }
    });
  </script>

</body>
</html>