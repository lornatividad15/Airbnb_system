document.addEventListener('DOMContentLoaded', function () {
  const profileForm = document.getElementById('profileForm');
  const profileImg = document.getElementById('profileImg');
  const profileUpload = document.getElementById('profileUpload');
  const profileDropdown = document.getElementById('profileDropdown');
  const currentEmail = document.querySelector('input[name="email"]');
  const newEmail = document.querySelector('input[name="new_email"]');
  const confirmEmail = document.querySelector('input[name="confirm_email"]');
  const currentPassword = document.getElementById('currentPassword');
  const newPassword = document.getElementById('newPassword');
  const confirmPassword = document.getElementById('confirmPassword');

  const modalOverlay = document.getElementById('modalOverlay');
  const modalBox = document.getElementById('modalBox');
  const modalMessage = document.getElementById('modalMessage');
  const modalCloseBtn = document.getElementById('modalCloseBtn');
  const modalActions = document.getElementById('modalActions');
  const modalConfirmBtn = document.getElementById('modalConfirmBtn');
  const modalCancelBtn = document.getElementById('modalCancelBtn');

  function showModal(message, mode = "ok-only", callback = null) {
    modalMessage.innerHTML = message;
    modalCloseBtn.style.display = mode === "ok-only" ? "inline-block" : "none";
    modalActions.style.display = mode === "ok-only" ? "none" : "flex";
    modalOverlay.style.display = "flex";

    modalConfirmBtn.onclick = () => {
      if (callback) callback();
      modalOverlay.style.display = "none";
    };
    modalCancelBtn.onclick = modalCloseBtn.onclick = () => {
      modalOverlay.style.display = "none";
    };

    modalBox.style.border = message.includes('❌') ? '2px solid #e74c3c' :
                             message.includes('✅') ? '2px solid #2ecc71' : 'none';
  }

  window.showModal = showModal;

  profileImg.addEventListener('click', () => {
    profileDropdown.style.display = profileDropdown.style.display === 'block' ? 'none' : 'block';
  });

  document.addEventListener('click', function (e) {
    if (!profileImg.contains(e.target) && !profileDropdown.contains(e.target)) {
      profileDropdown.style.display = 'none';
    }
  });

  document.getElementById('showProfilePic').addEventListener('click', () => {
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

  profileForm.addEventListener('submit', function (e) {
    if (newPassword.value && !currentPassword.value) {
      e.preventDefault();
      showModal("❌ Please enter your current password to set a new one.");
      return;
    }

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

  function togglePassword(id) {
    const input = document.getElementById(id);
    if (input.type === 'password') {
      input.type = 'text';
    } else {
      input.type = 'password';
    }
  }

  const deleteBtn = document.getElementById('deleteBtn');
  if (deleteBtn) {
    deleteBtn.addEventListener('click', function () {
      window.location.href = 'delete_account.php';
    });
  }
});