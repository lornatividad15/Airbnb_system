// FOR BOOKING SCRIPTS
document.addEventListener("DOMContentLoaded", function () {
  // === NAV TOGGLE ===
  function toggleMenu() {
    document.querySelector(".btn")?.classList.toggle("active");
    document.getElementById("mobileNav")?.classList.toggle("active");
    document.getElementById("shadow")?.classList.toggle("active");
  }

  document.querySelector(".btn")?.addEventListener("click", toggleMenu);
  document.getElementById("shadow")?.addEventListener("click", toggleMenu);

  // === USER STATE ===
  const isLoggedIn = window.isLoggedIn || false;
  const isAdmin = window.isAdmin || false;

  // === DROPDOWN MENU ===
  const userMenu = document.getElementById("userMenu");
  const userDropdown = document.getElementById("userDropdown");
  const accountName = document.getElementById("accountName");

  if (accountName && userDropdown) {
    accountName.addEventListener("click", (e) => {
      e.stopPropagation();
      userDropdown.classList.toggle("show");
    });
    document.addEventListener("click", (e) => {
      if (!userMenu.contains(e.target)) {
        userDropdown.classList.remove("show");
      }
    });
  }

  // === MESSAGE MODAL ===
  const messageModal = document.getElementById("messageModal");
  const messageModalText = document.getElementById("messageModalText");
  const messageModalCloseBtn = document.getElementById("messageModalCloseBtn");
  const messageModalCloseIcon = document.getElementById("messageModalCloseIcon");

  function showModal(message) {
    if (messageModalText && messageModal && messageModalCloseBtn && messageModalCloseIcon) {
      messageModalText.textContent = message;
      messageModal.classList.add("active");
    } else {
      alert(message); // fallback
    }
  }

  function closeModal() {
    messageModal?.classList.remove("active");
  }

  messageModalCloseBtn?.addEventListener("click", closeModal);
  messageModalCloseIcon?.addEventListener("click", closeModal);

  messageModal?.addEventListener("click", (e) => {
    if (e.target === messageModal) {
      closeModal();
    }
  });

  // === BOOKINGS LINK ===
  const myBookingsLink = document.getElementById("myBookingsLink");
  const mobileMyBookings = document.getElementById("mobileMyBookings");

  function handleBookingsClick(e) {
    e.preventDefault();
    if (isAdmin) {
      showModal("Admins can't access 'My Bookings'.");
    } else if (!isLoggedIn) {
      showModal("Please log in to view your bookings.");
      setTimeout(() => {
        window.location.href = "Login form.php";
      }, 2000);
    } else {
      window.location.href = "booking.php";
    }
  }

  myBookingsLink?.addEventListener("click", handleBookingsClick);
  mobileMyBookings?.addEventListener("click", handleBookingsClick);

  // === CANCEL MODAL ===
  const deleteModal = document.getElementById("deleteModal");
  const cancelDeleteBtn = document.getElementById("cancelDeleteBtn");
  const confirmDeleteBtn = document.getElementById("confirmDeleteBtn");
  const closeDeleteModalBtn = document.getElementById("closeDeleteModal");
  const cancellationReasonInput = document.getElementById("cancellationReason");

  let formToDelete = null;

  document.querySelectorAll('form[action="cancel_booking.php"]').forEach(form => {
    const deleteBtn = form.querySelector(".delete-btn");
    if (deleteBtn) {
      deleteBtn.addEventListener("click", (e) => {
        e.preventDefault();
        formToDelete = form;
        deleteModal.classList.add("active");
      });
    }
  });

  confirmDeleteBtn?.addEventListener("click", () => {
    if (formToDelete) {
      const reason = cancellationReasonInput?.value.trim();
      if (!reason) {
        showModal("Please enter a reason for cancellation.");
        return;
      }
      const hiddenReasonInput = document.createElement("input");
      hiddenReasonInput.type = "hidden";
      hiddenReasonInput.name = "cancellation_reason";
      hiddenReasonInput.value = reason;
      formToDelete.appendChild(hiddenReasonInput);
      formToDelete.submit();
    }
  });

  cancelDeleteBtn?.addEventListener("click", () => {
    deleteModal.classList.remove("active");
    formToDelete = null;
    if (cancellationReasonInput) cancellationReasonInput.value = "";
  });

  closeDeleteModalBtn?.addEventListener("click", () => {
    deleteModal.classList.remove("active");
    formToDelete = null;
    if (cancellationReasonInput) cancellationReasonInput.value = "";
  });

  // === USER DELETE CANCEL_REJECTED BOOKING ===
  document.querySelectorAll('.user-delete-cancel-rejected').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      const bookingId = btn.getAttribute('data-booking-id');
      if (!bookingId) {
        showPopupMessage('BOOKING ID NOT FOUND.');
        return;
      }
      showPopupMessage('Are you sure you want to delete this booking?', function onConfirm() {
        fetch('hide_booking.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: 'booking_id=' + encodeURIComponent(bookingId)
        })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              const bookingRow = btn.closest('.booking-row, .booking-card, tr');
              showPopupMessage('Booking Successfully Deleted.', () => {
                if (bookingRow) bookingRow.remove();
              });
            } else {
              showPopupMessage(data.message || 'FAILED TO DELETE BOOKING.');
            }
          })
          .catch(() => {
            showPopupMessage('ERROR OCCURRED WHILE DELETING BOOKING.');
          });
      }, true);
    });
  });

  // === POPUP MESSAGE MODAL LOGIC ===
  const popupMessageModal = document.getElementById("popupMessageModal");
  const popupMessageText = document.getElementById("popupMessageText");
  const popupMessageCloseBtn = document.getElementById("popupMessageCloseBtn");
  const popupMessageOkBtn = document.getElementById("popupMessageOkBtn");

  function showPopupMessage(message, onConfirm, isConfirm) {
    if (!isConfirm) {
      if (popupMessageText && popupMessageModal) {
        popupMessageText.textContent = message;
        popupMessageModal.classList.add("active");
        popupMessageOkBtn.textContent = "OK";
        popupMessageOkBtn.style.display = "inline-block";
        popupMessageCloseBtn.style.display = "inline-block";

        function cleanup() {
          popupMessageModal.classList.remove("active");
          popupMessageOkBtn.onclick = null;
          popupMessageCloseBtn.onclick = null;
        }

        popupMessageOkBtn.onclick = function () {
          cleanup();
          if (typeof onConfirm === "function") onConfirm();
        };

        popupMessageCloseBtn.onclick = cleanup;
        popupMessageModal.onclick = function (e) {
          if (e.target === popupMessageModal) cleanup();
        };
      } else {
        if (typeof onConfirm === "function") onConfirm();
        else alert(message);
      }
      return;
    }

    if (popupMessageText && popupMessageModal) {
      popupMessageText.textContent = message;
      popupMessageModal.classList.add("active");
      popupMessageOkBtn.textContent = "Yes";
      popupMessageOkBtn.style.display = "inline-block";
      popupMessageCloseBtn.style.display = "inline-block";

      function cleanup() {
        popupMessageModal.classList.remove("active");
        popupMessageOkBtn.textContent = "OK";
        popupMessageOkBtn.onclick = null;
        popupMessageCloseBtn.onclick = null;
      }

      popupMessageOkBtn.onclick = function () {
        cleanup();
        if (typeof onConfirm === "function") onConfirm();
      };

      popupMessageCloseBtn.onclick = cleanup;
      popupMessageModal.onclick = function (e) {
        if (e.target === popupMessageModal) cleanup();
      };
    } else {
      if (confirm(message)) onConfirm();
    }
  }
});
