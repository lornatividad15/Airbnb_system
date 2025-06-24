<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: Login form.php');
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $confirm_identifier = $_POST['confirm_identifier'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if (
        ($confirm_identifier === $user['email'] || $confirm_identifier === $user['username']) &&
        password_verify($confirm_password, $user['password'])
    ) {
        $conn->query("DELETE FROM users WHERE id = $user_id");
        $count_check = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc();
        if ($count_check['count'] == 0) {
            $conn->query("ALTER TABLE users AUTO_INCREMENT = 1");
        }

        session_destroy();
        header("Location: Login form.php?deleted=1");
        exit;
    } else {
        $_SESSION['delete_error'] = "❌ Invalid credentials. Account not deleted.";
        header("Location: delete_account.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Confirm Account Deletion</title>
  <link rel="stylesheet" href="CSS/delete_account.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <form method="post" class="modal-box">
    <h2>Delete Account</h2>
    <p>Please confirm your identity to permanently delete your account.</p>
    <input type="text" name="confirm_identifier" placeholder="Email or Username" required>
    <div class="password-wrapper">
      <input type="password" id="confirmPassword" name="confirm_password" placeholder="Password" required>
      <span class="toggle-password" onclick="togglePassword('confirmPassword', this)"><i class="fa-regular fa-eye"></i></span>
    </div>
    <button type="submit">Delete Account</button>
    <a href="profile.php" class="cancel">Cancel</a>
    <?php if (isset($_SESSION['delete_error'])): ?>
      <p class="error-msg"><?php echo $_SESSION['delete_error']; unset($_SESSION['delete_error']); ?></p>
    <?php endif; ?>
  </form>
  <script>
    function togglePassword(fieldId, icon) {
      const pwdInput = document.getElementById(fieldId);
      const eye = icon.querySelector('i');
      if (pwdInput.type === 'password') {
        pwdInput.type = 'text';
        eye.classList.remove('fa-eye');
        eye.classList.add('fa-eye-slash');
      } else {
        pwdInput.type = 'password';
        eye.classList.remove('fa-eye-slash');
        eye.classList.add('fa-eye');
      }
    }
    // Show modal if account deleted (from ?deleted=1 in URL)
    window.addEventListener('DOMContentLoaded', function() {
      const params = new URLSearchParams(window.location.search);
      if (params.get('deleted') === '1') {
        showDeleteModal();
      }
    });
    function showDeleteModal() {
      const modal = document.createElement('div');
      modal.className = 'modal-overlay';
      modal.innerHTML = `
        <div class="modal-message-box">
          <span class="close-modal" onclick="this.closest('.modal-overlay').remove()">&times;</span>
          <div class="modal-success-icon"><i class='fa-solid fa-check-circle'></i></div>
          <div class="modal-success-text">Account deleted successfully.</div>
        </div>
      `;
      document.body.appendChild(modal);
      setTimeout(() => {
        if (modal.parentNode) modal.remove();
        window.location.href = 'Login form.php';
      }, 3000);
    }
  </script>
</body>
</html>