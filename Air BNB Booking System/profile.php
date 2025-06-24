<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: Login form.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$success = $error = "";

// Fetch user info
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_profile'])) {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $username = $_POST['username'];
    $phone_number = $_POST['phone_number'];
    $birthdate = $_POST['birthdate'];
    $sex = $_POST['sex'];
    $age = $_POST['age'];

    $current_email = $_POST['email'];
    $new_email = $_POST['new_email'];
    $confirm_email = $_POST['confirm_email'];

    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $email_to_save = $user['email'];
    $password_to_save = $user['password'];
    $profile_picture = $user['profile_picture'];

    // Profile picture
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $profile_picture = file_get_contents($_FILES['profile_picture']['tmp_name']);
    }

    // Email validation
    if (!empty($new_email)) {
        if ($new_email === $user['email']) {
            $error = "New email must be different.";
        } elseif ($new_email !== $confirm_email) {
            $error = "New email does not match confirmation.";
        } else {
            $email_to_save = $new_email;
        }
    }

    // Password validation (like admin_profile)
    if (!$error && (!empty($current_password) || !empty($new_password) || !empty($confirm_password))) {
        if (empty($current_password)) {
            $error = "Please enter your current password.";
        } elseif (!password_verify($current_password, $user['password'])) {
            $error = "Current password is incorrect.";
        } elseif (!empty($new_password)) {
            if ($new_password !== $confirm_password) {
                $error = "New password and confirm password do not match.";
            } elseif (password_verify($new_password, $user['password'])) {
                $error = "New password must be different from your current password.";
            } else {
                $password_to_save = password_hash($new_password, PASSWORD_DEFAULT);
            }
        }
    }

    // Username validation
    if (!$error && $username !== $user['username']) {
        // Check in users table (exclude self)
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $stmt->bind_param("si", $username, $user_id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error = "Username already exists.";
        }
        $stmt->close();
        // Check in admins table
        if (!$error) {
            $stmt = $conn->prepare("SELECT admin_id FROM admins WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $error = "Username already exists.";
            }
            $stmt->close();
        }
    }

    if (!$error) {
        $sql = "UPDATE users SET firstname=?, lastname=?, username=?, phone_number=?, birthdate=?, sex=?, age=?, email=?, password=?, profile_picture=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $null = NULL;
        $stmt->bind_param("ssssssissbi",
            $firstname, $lastname, $username, $phone_number, $birthdate,
            $sex, $age, $email_to_save, $password_to_save, $null, $user_id);
        if (!empty($profile_picture)) {
            $stmt->send_long_data(9, $profile_picture);
        }
        if ($stmt->execute()) {
            $success = "✅ Profile updated successfully.";
        } else {
            $error = "❌ Database error. Try again.";
        }
        $stmt->close();
        // Refresh user info
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();
    }
}
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
<main class="profile-wrapper">
  <div class="profile-container">
    <form id="profileForm" method="post" enctype="multipart/form-data">
      <h1 class="profile-title">MY PROFILE</h1>
      <div id="modalOverlay" class="modal-overlay" style="display:none;">
        <div class="modal-box" id="modalBox">
          <button type="button" class="close-btn" id="modalCloseBtn">&times;</button>
          <span id="modalMessage"></span>
        </div>
      </div>
      <?php if ($success || $error): ?>
      <script>
        document.addEventListener("DOMContentLoaded", function () {
          const modalOverlay = document.getElementById("modalOverlay");
          const modalBox = document.getElementById("modalBox");
          const modalMessage = document.getElementById("modalMessage");
          const modalType = <?= json_encode($success ? 'success' : 'error') ?>;
          const modalText = <?= json_encode($success ?: $error) ?>;
          if (modalOverlay && modalBox && modalMessage) {
            modalBox.className = 'modal-box ' + modalType;
            modalMessage.textContent = modalText;
            modalOverlay.classList.add('show');
            modalOverlay.style.display = 'flex';
          }
        });
      </script>
      <?php endif; ?>
      <div class="profile-picture">
        <img 
          src="<?php echo !empty($user['profile_picture']) 
            ? 'data:image/jpeg;base64,' . base64_encode($user['profile_picture']) 
            : 'Images/profile_logo.png'; ?>" 
          id="profileImg" alt="Profile Picture" style="cursor:pointer;">
        <input type="file" id="profileUpload" name="profile_picture" accept="image/*" style="display:none;">
        <button type="button" id="editProfilePicBtn" title="Change Profile Picture">
          <span style="color:white;font-size:18px;" class="fa fa-pen"></span>
        </button>
      </div>
      <div class="form-group"><label>First Name</label>
        <input type="text" name="firstname" value="<?= htmlspecialchars($user['firstname']) ?>" required></div>
      <div class="form-group"><label>Last Name</label>
        <input type="text" name="lastname" value="<?= htmlspecialchars($user['lastname']) ?>" required></div>
      <div class="form-group"><label>Current Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" readonly style="background:#f5f5f5;pointer-events:none;" tabindex="-1"></div>
      <div class="form-group"><label>Username</label>
        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required></div>
      <div class="form-group"><label>Phone Number</label>
        <input type="text" name="phone_number" value="<?= htmlspecialchars($user['phone_number']) ?>"></div>
      <div class="form-group"><label>Birthdate</label>
        <input type="date" id="birthdate" name="birthdate" max="<?= date('Y-m-d', strtotime('-1 day')) ?>" value="<?= htmlspecialchars($user['birthdate']) ?>"></div>
      <div class="form-group"><label>Age</label>
        <input type="number" id="age" name="age" value="<?= htmlspecialchars($user['age']) ?>" required></div>
      <div class="form-group"><label>Sex</label>
        <select name="sex" required>
          <option value="Male" <?php echo ($user['sex']==='Male')?'selected':''; ?>>Male</option>
          <option value="Female" <?php echo ($user['sex']==='Female')?'selected':''; ?>>Female</option>
          <option value="Other" <?php echo ($user['sex']==='Other')?'selected':''; ?>>Other</option>
        </select>
      </div>
      <hr><h2 class="section-title">Change Email</h2>
      <div class="form-group"><label>New Email</label><input type="email" name="new_email"></div>
      <div class="form-group"><label>Confirm New Email</label><input type="email" name="confirm_email"></div>
      <hr><h2 class="section-title">Change Password</h2>
      <div class="form-group password-field">
        <label>Current Password</label>
        <input type="password" id="currentPassword" name="current_password">
        <span class="toggle-password" onclick="togglePassword('currentPassword')">&#128065;</span>
      </div>
      <div class="form-group password-field">
        <label>New Password</label>
        <input type="password" id="newPassword" name="new_password">
        <span class="toggle-password" onclick="togglePassword('newPassword')">&#128065;</span>
      </div>
      <div class="form-group password-field">
        <label>Confirm New Password</label>
        <input type="password" id="confirmPassword" name="confirm_password">
        <span class="toggle-password" onclick="togglePassword('confirmPassword')">&#128065;</span>
      </div>
      <input type="hidden" name="update_profile" value="1">
      <button type="submit">Save Changes</button>
      <button type="button" class="delete-button" id="deleteBtn">Delete Account</button>
    </form>
  </div>
</main>
<!-- Only one modal overlay in the DOM -->
<div class="modal-overlay" id="modalOverlay" style="display:none;">
  <div class="modal-box" id="modalBox">
    <button type="button" class="close-btn" id="modalCloseBtn">&times;</button>
    <span id="modalMessage"></span>
  </div>
</div>
<!-- Profile Picture Image Viewer Modal (centered popout) -->
<div id="imageViewerModal" class="image-viewer-modal" style="display:none;position:fixed;z-index:1000;left:0;top:0;width:100vw;height:100vh;background:rgba(0,0,0,0.7);justify-content:center;align-items:center;">
  <span class="close-viewer" id="closeViewerModalBtn" style="position:absolute;top:32px;right:48px;font-size:2.5rem;color:white;cursor:pointer;">&times;</span>
  <img id="viewerImageModal" src="" alt="Profile Picture" style="max-width:90vw;max-height:80vh;border-radius:16px;box-shadow:0 4px 32px rgba(0,0,0,0.4);display:block;">
</div>
<script src="JS/profile_form.js"></script>
<!-- Profile Picture Bottom Sheet Viewer -->
<div id="imageViewerBottom" class="image-viewer-bottom" style="display:none;position:fixed;left:0;right:0;bottom:0;z-index:1000;background:rgba(255,255,255,0.98);box-shadow:0 -2px 16px rgba(0,0,0,0.2);padding:24px 0 16px 0;text-align:center;">
  <span class="close-viewer" id="closeViewerBottomBtn" style="position:absolute;top:8px;right:24px;font-size:2rem;cursor:pointer;">&times;</span>
  <img id="viewerImageBottom" src="" alt="Profile Picture">
</div>
<script>
  // --- Birthdate/Age logic (untouched) ---
  document.addEventListener('DOMContentLoaded', function () {
    const birthInput = document.getElementById('birthdate');
    const ageInput = document.getElementById('age');
    if (!birthInput || !ageInput) return;
    birthInput.max = new Date().toISOString().split("T")[0];
    birthInput.addEventListener('focus', () => { birthInput.showPicker && birthInput.showPicker(); });
    birthInput.addEventListener('click', () => { birthInput.showPicker && birthInput.showPicker(); });
    birthInput.addEventListener('change', function () {
      const birthDate = new Date(this.value);
      const today = new Date();
      let age = today.getFullYear() - birthDate.getFullYear();
      if (
        today.getMonth() < birthDate.getMonth() ||
        (today.getMonth() === birthDate.getMonth() && today.getDate() < birthDate.getDate())
      ) {
        age--;
      }
      ageInput.value = !isNaN(age) && age > 0 ? age : '';
    });
    ageInput.addEventListener('input', function () {
      const age = parseInt(this.value);
      if (!isNaN(age) && age > 0) {
        const today = new Date();
        const birthDate = new Date(birthInput.value || today);
        const birthYear = today.getFullYear() - age;
        birthDate.setFullYear(birthYear);
        birthInput.value = birthDate.toISOString().split('T')[0];
      }
    });
  });
</script>
</body>
</html>