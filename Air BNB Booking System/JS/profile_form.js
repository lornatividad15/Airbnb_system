//FOR PROFILE FORM SCRIPTS
document.addEventListener('DOMContentLoaded', function () {
  const profileForm = document.getElementById('profileForm');
  const profileImg = document.getElementById('profileImg');
  const profileUpload = document.getElementById('profileUpload');
  const currentEmail = document.querySelector('input[name="email"]');
  const newEmail = document.querySelector('input[name="new_email"]');
  const confirmEmail = document.querySelector('input[name="confirm_email"]');
  const currentPassword = document.getElementById('currentPassword');
  const newPassword = document.getElementById('newPassword');
  const confirmPassword = document.getElementById('confirmPassword');

  // --- Modal Logic (Works For Both PHP + JS Triggered Feedback) ---
  const modalOverlay = document.getElementById("modalOverlay");
  const modalBox = document.getElementById("modalBox");
  const modalMessage = document.getElementById("modalMessage");
  const modalCloseBtn = document.getElementById("modalCloseBtn");

  function closeModal(e) {
    if (e) e.preventDefault();
    modalOverlay.style.display = 'none';
    modalOverlay.classList.remove('show');
    if (window.modalTimeout) clearTimeout(window.modalTimeout);
  }

  if (modalOverlay && modalBox && modalMessage && modalCloseBtn) {
    modalCloseBtn.onclick = closeModal;
    modalOverlay.onclick = function (e) {
      if (e.target === modalOverlay) closeModal();
    };

    if (modalOverlay.classList.contains('show')) {
      modalOverlay.style.display = 'flex';
      window.modalTimeout = setTimeout(closeModal, 3000);
    }

    window.showModal = function (message, type = 'error') {
      modalBox.className = 'modal-box ' + type;
      modalMessage.textContent = message;
      modalOverlay.style.display = 'flex';
      modalOverlay.classList.add('show');
      if (window.modalTimeout) clearTimeout(window.modalTimeout);
      window.modalTimeout = setTimeout(closeModal, 3000);
    };
  }

  // --- Profile Image Click To Open Modal Viewer ---
  const viewerModal = document.getElementById('imageViewerModal');
  const viewerImgModal = document.getElementById('viewerImageModal');
  const closeViewerModalBtn = document.getElementById('closeViewerModalBtn');

  if (profileImg && viewerModal && viewerImgModal && closeViewerModalBtn) {
    profileImg.onclick = function (e) {
      e.preventDefault();
      viewerImgModal.src = profileImg.src;
      viewerModal.style.display = 'flex';
    };

    closeViewerModalBtn.onclick = function (e) {
      e.preventDefault();
      viewerModal.style.display = 'none';
    };

    viewerModal.onclick = function (e) {
      if (e.target === viewerModal) viewerModal.style.display = 'none';
    };
  }

  // --- Pen Icon Triggers File Input ---
  const editProfilePicBtn = document.getElementById('editProfilePicBtn');
  if (editProfilePicBtn && profileUpload) {
    editProfilePicBtn.onclick = function (e) {
      e.preventDefault();
      profileUpload.click();
    };
  }

  // --- Profile Picture Preview On Upload ---
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

  // --- Password Visibility Toggle ---
  window.togglePassword = function (id) {
    const input = document.getElementById(id);
    if (input.type === 'password') {
      input.type = 'text';
    } else {
      input.type = 'password';
    }
  };

  // --- Form Validation Before Submission ---
  profileForm.addEventListener('submit', function (e) {
    function showValidationModal(msg) {
      if (typeof showModal === 'function') {
        showModal(msg, 'error');
      } else {
        alert(msg);
      }
    }

    if (newEmail.value.trim() && newEmail.value.trim() === currentEmail.value.trim()) {
      e.preventDefault();
      showValidationModal("❌ New email must be different from your current email.");
      return;
    }

    if (newPassword.value && !currentPassword.value) {
      e.preventDefault();
      showValidationModal("❌ Please enter your current password to set a new one.");
      return;
    }

    if (newEmail.value && newEmail.value !== confirmEmail.value) {
      e.preventDefault();
      showValidationModal("❌ New email and confirm email do not match.");
      return;
    }

    if (newPassword.value && newPassword.value === currentPassword.value) {
      e.preventDefault();
      showValidationModal("❌ New password must be different from your current password.");
      return;
    }

    if (newPassword.value && newPassword.value !== confirmPassword.value) {
      e.preventDefault();
      showValidationModal("❌ New password and confirm password do not match.");
      return;
    }
  });

  // --- Account Delete Button ---
  const deleteBtn = document.getElementById('deleteBtn');
  if (deleteBtn) {
    deleteBtn.addEventListener('click', function () {
      window.location.href = 'delete_account.php';
    });
  }
});