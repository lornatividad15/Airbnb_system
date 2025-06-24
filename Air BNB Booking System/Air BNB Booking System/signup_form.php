<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Sign Up</title>
  <link rel="stylesheet" href="CSS/signup_form.css" />
  <link rel="stylesheet" href="CSS/modal_global.css" />
</head>
<body>
  <header>
    <a href="Main Page.php" class="logo">
      <img src="Images/logo-light-transparent.png" alt="Site Logo" />
    </a>
  </header>

  <main>
    <div class="signup-container">
      <h2>Sign Up</h2>
      <form action="register.php" method="POST" enctype="multipart/form-data">

        <div class="profile-picture-wrapper">
          <img src="Images/profile_logo.png" id="preview" alt="Profile Picture" class="profile-picture" onclick="toggleProfileOptions()">
          <div class="profile-options" id="profileOptions">
            <p onclick="showProfilePicture()">Show Profile Picture</p>
            <p onclick="document.getElementById('profile_picture').click()">Change Profile Picture</p>
          </div>
          <input type="file" name="profile_picture" id="profile_picture" accept="image/*" hidden onchange="previewProfilePicture(event)" />
        </div>

        <label for="username">Username</label>
        <input type="text" id="username" name="username" placeholder="Enter your username" required />

        <label for="firstname">First Name</label>
        <input type="text" id="firstname" name="firstname" placeholder="Enter your first name" required />

        <label for="lastname">Last Name</label>
        <input type="text" id="lastname" name="lastname" placeholder="Enter your last name" required />

        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" placeholder="Enter your email address" required />

        <label for="phone_number">Phone Number</label>
        <input type="tel" id="phone_number" name="phone_number" pattern="[0-9]{11}" placeholder="e.g. 09123456789" required />

        <label for="password">Password</label>
        <div class="password-wrapper">
          <input type="password" id="password" name="password" required />
          <span class="toggle-password" onclick="togglePassword('password')">&#128065;</span>
        </div>

        <label for="confirm_password">Confirm Password</label>
        <div class="password-wrapper">
          <input type="password" id="confirm_password" name="confirm_password" required />
          <span class="toggle-password" onclick="togglePassword('confirm_password')">&#128065;</span>
        </div>

        <div class="clickable-field">
          <label for="birthdate">Birthdate</label>
          <input type="date" id="birthdate" name="birthdate" required />
        </div>

        <label for="sex">Sex</label>
        <select id="sex" name="sex" required>
          <option value="">Select</option>
          <option value="Male">Male</option>
          <option value="Female">Female</option>
          <option value="Other">Other</option>
        </select>

        <label for="age">Age</label>
        <input type="number" id="age" name="age" min="1" required />

        <button type="submit">Sign Up</button>
        <p class="switch">Already have an account? <a href="Login form.php">Login</a></p>
      </form>
    </div>
  </main>

  <script src="JS/signup_form.js"></script>
  <script>
    function togglePassword(fieldId) {
      const input = document.getElementById(fieldId);
      input.type = (input.type === "password") ? "text" : "password";
    }

    // Improved Birthdate/Age logic
    document.addEventListener('DOMContentLoaded', function () {
      const birthInput = document.getElementById('birthdate');
      const ageInput = document.getElementById('age');
      // Set max date for birthdate
      birthInput.max = new Date().toISOString().split("T")[0];
      // Open calendar on click anywhere in field
      birthInput.addEventListener('focus', () => { birthInput.showPicker && birthInput.showPicker(); });
      birthInput.addEventListener('click', () => { birthInput.showPicker && birthInput.showPicker(); });
      // Birthdate to Age (precise)
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
      // Age to Birthdate (only year changes)
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

  <div id="modal" class="modal-overlay">
    <div class="modal-box" id="modalBox">
      <button class="close-btn" onclick="closeModal()">&times;</button>
      <span id="modal-message"></span>
    </div>
  </div>

  <script>
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
  </script>
</body>
</html>