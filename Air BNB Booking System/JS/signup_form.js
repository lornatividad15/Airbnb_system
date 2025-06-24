//FOR SIGNUP FORM SCRIPTS
document.addEventListener('DOMContentLoaded', function () {
  const birthInput = document.getElementById('birthdate');
  const ageInput = document.getElementById('age');
  const form = document.querySelector('form');
  const passwordInput = document.getElementById('password');
  const confirmPasswordInput = document.getElementById('confirm_password');

  // Block future dates
  birthInput.max = new Date().toISOString().split("T")[0];

  birthInput.addEventListener('focus', () => {
    if (birthInput.showPicker) {
      birthInput.showPicker();
    }
  });

  birthInput.addEventListener('click', () => {
    if (birthInput.showPicker) {
      birthInput.showPicker();
    }
  });

  birthInput.addEventListener('change', function () {
    const birthDate = new Date(this.value);
    const today = new Date();

    let age = today.getFullYear() - birthDate.getFullYear();
    const monthDiff = today.getMonth() - birthDate.getMonth();
    const dayDiff = today.getDate() - birthDate.getDate();

    if (monthDiff < 0 || (monthDiff === 0 && dayDiff < 0)) {
      age--;
    }

    if (!isNaN(age) && age >= 0) {
      ageInput.value = age;
    } else {
      ageInput.value = '';
    }
  });

  form.addEventListener('submit', function (e) {
    const password = passwordInput.value.trim();
    const confirmPassword = confirmPasswordInput.value.trim();

    if (password !== confirmPassword) {
      e.preventDefault();
      showModal('❌ Password and Confirm Password do not match.');
    }
  });

  window.showModal = function (message) {
    const modal = document.getElementById('modal');
    const modalMsg = document.getElementById('modal-message');
    modalMsg.textContent = message;
    modal.classList.add('show');
    setTimeout(() => {
      modal.classList.remove('show');
    }, 3000);
  };

  window.closeModal = function () {
    document.getElementById('modal').classList.remove('show');
  };
});

function togglePassword(id) {
  const input = document.getElementById(id);
  if (input.type === 'password') {
    input.type = 'text';
  } else {
    input.type = 'password';
  }
}

window.toggleProfileOptions = function () {
  const options = document.getElementById('profileOptions');
  options.style.display = options.style.display === 'block' ? 'none' : 'block';
};

window.showProfilePicture = function () {
  const imageSrc = document.getElementById('preview').src;
  const modal = document.getElementById('modal');
  const modalMsg = document.getElementById('modal-message');
  modalMsg.innerHTML = `<img src="${imageSrc}" style="max-width: 100%; border-radius: 10px;">`;
  modal.classList.add('show');
  setTimeout(() => {
    modal.classList.remove('show');
  }, 4000);
};

window.previewProfilePicture = function (event) {
  const reader = new FileReader();
  reader.onload = function () {
    const output = document.getElementById('preview');
    output.src = reader.result;
  };
  reader.readAsDataURL(event.target.files[0]);
};