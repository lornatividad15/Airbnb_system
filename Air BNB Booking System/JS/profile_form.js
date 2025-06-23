document.addEventListener('DOMContentLoaded', function () {
  const profileForm = document.getElementById('profileForm');

  // Inputs
  const currentEmail = document.getElementById('email');
  const newEmail = document.querySelector('input[name="new_email"]');
  const confirmEmail = document.querySelector('input[name="confirm_email"]');
  const currentPassword = document.getElementById('currentPassword');
  const newPassword = document.getElementById('newPassword');
  const confirmPassword = document.getElementById('confirmPassword');

  const birthdate = document.querySelector('input[name="birthdate"]');
  const ageInput = document.querySelector('input[name="age"]');

  // Profile picture logic
  const profileImg = document.getElementById('profileImg');
  const profileDropdown = document.getElementById('profileDropdown');
  const profileUpload = document.getElementById('profileUpload');

  // Modal elements
  const modalOverlay = document.getElementById('modalOverlay');
  const modalBox = document.querySelector('.modal-box');
  const modalMessage = document.getElementById('modalMessage');
  const modalCloseBtn = document.getElementById('modalCloseBtn');
  const modalActions = document.getElementById('modalActions');
  const modalConfirmBtn = document.getElementById('modalConfirmBtn');
  const modalCancelBtn = document.getElementById('modalCancelBtn');

  const deleteBtn = document.getElementById('deleteBtn');

  // Show modal on load
  showModal(
    "ℹ️ You can update your credentials individually.<br><br>To update password and email, enter your current and new values. New password and email must be different.",
    "ok-only"
  );

  function showModal(message, mode = "ok-only", callback = null) {
    modalMessage.innerHTML = message;

    modalCloseBtn.style.display = mode === "ok-only" ? "inline-block" : "none";
    modalActions.style.display = mode === "ok-only" ? "none" : "flex";

    modalOverlay.style.display = "flex";

    modalConfirmBtn.onclick = () => {
      if (callback) callback();
      modalOverlay.style.display = "none";
    };

    modalCancelBtn.onclick = () => {
      modalOverlay.style.display = "none";
    };

    modalCloseBtn.onclick = () => {
      modalOverlay.style.display = "none";
    };

    modalBox.style.border = message.includes('❌') ? '2px solid #e74c3c' :
                         message.includes('✅') ? '2px solid #2ecc71' : 'none';
  }

  // Toggle profile dropdown
  profileImg.addEventListener('click', () => {
    profileDropdown.style.display =
      profileDropdown.style.display === 'block' ? 'none' : 'block';
  });

  document.addEventListener('click', function (e) {
    if (!profileImg.contains(e.target) && !profileDropdown.contains(e.target)) {
      profileDropdown.style.display = 'none';
    }
  });

  // View profile picture in modal
  document.querySelector('#profileDropdown button').addEventListener('click', () => {
    profileDropdown.style.display = 'none';
    showModal(`<img src="${profileImg.src}" style="width:100%;max-width:300px;border-radius:12px;" />`);
  });

  profileUpload.addEventListener('change', function () {
    const file = this.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function (e) {
        profileImg.src = e.target.result;
      };
      reader.readAsDataURL(file);
    }
  });

  // Delete account with confirmation
  deleteBtn.addEventListener('click', () => {
    showModal(
      `
      <strong>Confirm your credentials to delete your account.</strong><br>
      <input type="text" id="confirmUsernameEmail" placeholder="Enter email or username" />
      <input type="password" id="confirmPassword" placeholder="Enter password" />
      `,
      "confirm-cancel",
      () => {
        const emailOrUsername = document.getElementById('confirmUsernameEmail').value.trim();
        const password = document.getElementById('confirmPassword').value.trim();

        if (!emailOrUsername || !password) {
          showModal("❌ Both fields are required.");
          return;
        }

        const input1 = document.createElement('input');
        input1.type = 'hidden';
        input1.name = 'delete_account';
        input1.value = '1';

        const input2 = document.createElement('input');
        input2.type = 'hidden';
        input2.name = 'confirm_identifier';
        input2.value = emailOrUsername;

        const input3 = document.createElement('input');
        input3.type = 'hidden';
        input3.name = 'confirm_password';
        input3.value = password;

        profileForm.appendChild(input1);
        profileForm.appendChild(input2);
        profileForm.appendChild(input3);

        profileForm.submit();
      }
    );
  });

  // Validate on submit
  profileForm.addEventListener('submit', function (e) {
    if (newEmail.value && newEmail.value === currentEmail.value) {
      e.preventDefault();
      showModal("❌ New email must be different from your current email.");
      return;
    }

    if (newEmail.value && newEmail.value !== confirmEmail.value) {
      e.preventDefault();
      showModal("❌ New email and confirm email do not match.");
      return;
    }

    if (newPassword.value && newPassword.value === currentPassword.value) {
      e.preventDefault();
      showModal("❌ New password must be different from your current password.");
      return;
    }

    if (newPassword.value && newPassword.value !== confirmPassword.value) {
      e.preventDefault();
      showModal("❌ New password and confirm password do not match.");
      return;
    }
  });

  // Toggle password visibility
  document.querySelectorAll('.toggle-password').forEach(icon => {
    icon.addEventListener('click', () => {
      const input = icon.previousElementSibling;
      input.type = input.type === 'password' ? 'text' : 'password';
      icon.textContent = input.type === 'password' ? '👁️' : '🙈';
    });
  });

  // Birthdate and age synchronization
  birthdate.addEventListener('change', () => {
    const birth = new Date(birthdate.value);
    const today = new Date();
    let age = today.getFullYear() - birth.getFullYear();
    const m = today.getMonth() - birth.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) age--;
    ageInput.value = age > 0 ? age : '';
  });

  ageInput.addEventListener('input', () => {
    const inputAge = parseInt(ageInput.value, 10);
    if (isNaN(inputAge) || inputAge < 1 || inputAge > 120) return;

    const today = new Date();

    let birthMonth = 0;
    let birthDay = 1;

    if (birthdate.value) {
      const existing = new Date(birthdate.value);
      birthMonth = existing.getMonth();
      birthDay = existing.getDate();
    }

    // Check if birthday has occurred this year
    const hasHadBirthdayThisYear =
      today.getMonth() > birthMonth || 
      (today.getMonth() === birthMonth && today.getDate() >= birthDay);

    const baseYear = today.getFullYear();
    const newYear = hasHadBirthdayThisYear
      ? baseYear - inputAge
      : baseYear - inputAge - 1;

    const adjustedDate = new Date(newYear, birthMonth, birthDay);

    // Format YYYY-MM-DD
    const month = String(adjustedDate.getMonth() + 1).padStart(2, '0');
    const day = String(adjustedDate.getDate()).padStart(2, '0');
    const dateStr = `${adjustedDate.getFullYear()}-${month}-${day}`;
    birthdate.value = dateStr;
  });

  document.getElementById('birthdate').addEventListener('click', function () {
    this.showPicker && this.showPicker(); // For modern browsers
  });
});