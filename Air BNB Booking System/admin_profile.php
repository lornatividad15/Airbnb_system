<?php
session_start();
include 'config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_loginform.php");
    exit;
}

$admin_id = $_SESSION['admin_id'];
$success = $error = "";

$stmt = $conn->prepare("SELECT admin_id, username, password, created_at FROM admins WHERE admin_id = ?");
$stmt->bind_param("s", $admin_id);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();
$stmt->close();

$current_admin_id = $admin['admin_id'];
$current_username = $admin['username'];
$current_password = $admin['password'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $new_admin_id = $_POST['admin_id'] ?? $current_admin_id;
    $new_username = $_POST['username'] ?? $current_username;
    $current_password_input = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';

    if (!empty($current_password_input) || !empty($new_password)) {
        if (hash('sha256', $current_password_input) !== $current_password) {
            $error = "Current password is incorrect.";
        } elseif (hash('sha256', $new_password) === $current_password) {
            $error = "New password cannot be the same as the old password.";
        } elseif (!empty($new_password)) {
            $hashed_new = hash('sha256', $new_password);
            $stmt = $conn->prepare("UPDATE admins SET admin_id = ?, username = ?, password = ? WHERE admin_id = ?");
            $stmt->bind_param("ssss", $new_admin_id, $new_username, $hashed_new, $admin_id);
        }
    }

    if (empty($error)) {
        if (empty($new_password)) {
            $stmt = $conn->prepare("UPDATE admins SET admin_id = ?, username = ? WHERE admin_id = ?");
            $stmt->bind_param("sss", $new_admin_id, $new_username, $admin_id);
        }

        if ($stmt->execute()) {
            $_SESSION['admin_id'] = $new_admin_id;
            $_SESSION['admin_username'] = $new_username;
            $success = "Profile updated successfully.";
            $admin_id = $new_admin_id;
        } else {
            $error = "Failed to update profile.";
        }
        $stmt->close();
    }

    $stmt = $conn->prepare("SELECT admin_id, username, password, created_at FROM admins WHERE admin_id = ?");
    $stmt->bind_param("s", $admin_id);
    $stmt->execute();
    $admin = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Profile</title>
  <link rel="stylesheet" href="CSS/admin_page.css" />
  <link rel="stylesheet" href="CSS/admin_profile.css" />
</head>
<body>

<header>
  <div class="header-inner">
    <a href="Main Page.php" class="logo">
      <img src="Images/logo-light-transparent.png" alt="Logo" />
    </a>
    <div class="admin-menu">
      <div class="dropdown-wrapper">
        <span class="dropdown-toggle">Admin</span>
        <div class="dropdown" id="adminDropdown">
          <a href="admin_page.php">Admin Page</a>
          <a href="logout.php">Logout</a>
        </div>
      </div>
    </div>
  </div>
</header>

<main>

  <div class="modal-overlay" id="infoModal">
    <div class="modal-content">
      <h3>Profile Update Info</h3>
      <p>You can update your <strong>Admin ID</strong>, <strong>Username</strong>, or <strong>Password</strong> individually.</p>
      <p>To update password, enter your <strong>current</strong> and <strong>new password</strong>. New password must be different.</p>
      <button class="modal-close-btn" onclick="closeModal()">Got it</button>
    </div>
  </div>

  <div class="profile-container">
    <h2>Admin Profile</h2>

    <?php if ($success): ?>
      <div class="success-msg"><?= $success ?></div>
    <?php elseif ($error): ?>
      <div class="error-msg"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" class="profile-form">
      <label for="admin_id">Admin ID</label>
      <input type="text" id="admin_id" name="admin_id" value="<?= htmlspecialchars($admin['admin_id']) ?>" required>

      <label for="username">Username</label>
      <input type="text" id="username" name="username" value="<?= htmlspecialchars($admin['username']) ?>" required>

      <label for="current_password">Current Password</label>
      <div class="password-wrapper">
        <input type="password" id="current_password" name="current_password">
        <span class="toggle-password" onclick="togglePassword('current_password')">&#128065;</span>
      </div>

      <label for="new_password">New Password</label>
      <div class="password-wrapper">
        <input type="password" id="new_password" name="new_password">
        <span class="toggle-password" onclick="togglePassword('new_password')">&#128065;</span>
      </div>

      <label for="created">Created At</label>
      <input type="text" id="created" value="<?= date("F j, Y", strtotime($admin['created_at'])) ?>" disabled>

      <button type="submit" class="update-btn">Update Profile</button>
    </form>
  </div>
</main>

<script src="JS/admin_profile.js"></script>
</body>
</html>