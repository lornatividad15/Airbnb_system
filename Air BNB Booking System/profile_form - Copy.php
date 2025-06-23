<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: Login form.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>My Profile</title>
  <link rel="stylesheet" href="CSS/profile_form.css" />
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>
<header>
  <div class="header-inner">
    <a href="Main Page.php" class="logo">
      <img src="Images/logo-light-transparent.png" alt="Site Logo" />
    </a>
  </div>
</header>
<main class="profile-container">
  <h1 class="profile-title">MY PROFILE</h1>
  <form id="profileForm" method="post" action="Profile.php" enctype="multipart/form-data">
    <div class="profile-picture">
      <img 
        src="<?php echo !empty($user['profile_picture']) ? 'data:image/jpeg;base64,' . base64_encode($user['profile_picture']) : 'Images/profile_logo.png'; ?>" 
        id="profileImg" alt="Profile Picture">
      <input type="file" id="profileUpload" name="profile_picture" accept="image/*">
    </div>
      <div class="profile-dropdown" id="profileDropdown">
        <button type="button" id="showProfilePic">SHOW PROFILE PICTURE</button>
        <label for="profileUpload" id="changeProfilePic">CHANGE PROFILE PICTURE</label>
      </div>

    <div class="form-group"><label for="firstname">First Name</label>
      <input type="text" id="firstname" name="firstname" value="<?php echo htmlspecialchars($user['firstname']); ?>" required></div>
    <div class="form-group"><label for="lastname">Last Name</label>
      <input type="text" id="lastname" name="lastname" value="<?php echo htmlspecialchars($user['lastname']); ?>" required></div>
    <div class="form-group"><label for="email">Current Email</label>
      <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly></div>
    <div class="form-group"><label for="username">Username</label>
      <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required></div>
    <div class="form-group"><label for="phone_number">Phone Number</label>
      <input type="text" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number']); ?>"></div>
    <div class="form-group"><label for="birthdate">Birthdate</label>
      <input type="date" id="birthdate" name="birthdate" max="<?php echo date('Y-m-d', strtotime('-1 day')); ?>" value="<?php echo htmlspecialchars($user['birthdate']); ?>" onfocus="this.showPicker()"></div>
    <div class="form-group"><label for="sex">Sex</label>
      <select id="sex" name="sex" required>
        <option value="Male" <?php if ($user['sex'] === 'Male') echo 'selected'; ?>>Male</option>
        <option value="Female" <?php if ($user['sex'] === 'Female') echo 'selected'; ?>>Female</option>
        <option value="Other" <?php if ($user['sex'] === 'Other') echo 'selected'; ?>>Other</option>
      </select></div>
    <div class="form-group"><label for="age">Age</label>
      <input type="number" id="age" name="age" value="<?php echo htmlspecialchars($user['age']); ?>" required></div>

    <hr><h2 class="section-title">Change Email</h2>
    <div class="form-group"><label for="newEmail">New Email</label><input type="email" id="newEmail" name="new_email"></div>
    <div class="form-group"><label for="confirmEmail">Confirm New Email</label><input type="email" id="confirmEmail" name="confirm_email"></div>

    <hr><h2 class="section-title">Change Password</h2>
    <div class="form-group password-field">
      <label>Current Password</label>
      <input type="password" id="currentPassword" name="current_password">
      <i class="fas fa-eye toggle-password"></i>
    </div>
    <div class="form-group password-field">
      <label>New Password</label>
      <input type="password" id="newPassword" name="new_password">
      <i class="fas fa-eye toggle-password"></i>
    </div>
    <div class="form-group password-field">
      <label>Confirm New Password</label>
      <input type="password" id="confirmPassword" name="confirm_password">
      <i class="fas fa-eye toggle-password"></i>
    </div>

    <button type="submit">Save Changes</button>
    <button type="button" class="delete-button" id="deleteBtn">Delete Account</button>
  </form>
</main>

<!-- Shared Modal -->
<div class="modal-overlay" id="modalOverlay">
  <div class="modal-box" id="modalBox">
    <p id="modalMessage">This is a modal message.</p>
    <div class="modal-content" id="modalCustomContent"></div>
    <div class="modal-actions" id="modalActions">
      <button id="modalConfirmBtn" class="modal-confirm">Proceed</button>
      <button id="modalCancelBtn" class="modal-cancel">Cancel</button>
    </div>
    <button id="modalCloseBtn">OK</button>
  </div>
</div>

<script src="JS/profile_form.js"></script>
<script>
window.addEventListener('DOMContentLoaded', function () {
  showModal('ℹ️ You can update your credentials individually.<br><br>To update password and email, enter your current and new values. New password and email must be different.', 'ok-only');
});
</script>
</body>
</html>