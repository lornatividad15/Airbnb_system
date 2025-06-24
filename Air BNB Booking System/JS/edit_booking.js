// FOR EDIT BOOKING SCRIPTS
document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("editBookingForm");
  const checkinInput = document.getElementById("checkin");
  const checkoutInput = document.getElementById("checkout");

  // Prevent past date selection
  checkinInput.min = minDate;
  checkoutInput.min = minDate;

  // Make input wrapper clickable
  document.querySelectorAll(".input-wrapper").forEach(wrapper => {
    const input = wrapper.querySelector("input[type='datetime-local']");
    if (input) {
      wrapper.addEventListener("click", () => {
        if (typeof input.showPicker === "function") {
          input.showPicker();
        } else {
          input.focus();
        }
      });
    }
  });

  // Validate date on form submit
  if (form) {
    form.addEventListener("submit", (e) => {
      const checkin = new Date(checkinInput.value);
      const checkout = new Date(checkoutInput.value);

      // Check if selected dates overlap with any blocked ranges
      let conflict = false;
      for (let range of blockedRanges) {
        const start = new Date(range.start);
        const end = new Date(range.end);

        const overlaps =
          (checkin >= start && checkin < end) ||
          (checkout > start && checkout <= end) ||
          (checkin <= start && checkout >= end);

        if (overlaps) {
          conflict = true;
          break;
        }
      }

      if (conflict) {
        e.preventDefault();
        showModal("Selected date is unavailable. Please choose a different date.");
        return;
      }

      if (checkout <= checkin) {
        e.preventDefault();
        showModal("Check-out must be after check-in.");
        return;
      }
    });
  }
});

// Show modal with dynamic message
function showModal(message) {
  const modal = document.getElementById("conflictModal");
  const messageBox = document.getElementById("conflictMessage");
  if (messageBox) messageBox.textContent = message;
  if (modal) modal.classList.add("active");
}

// Close modal when user clicks the close button
function closeModal() {
  const modal = document.getElementById("conflictModal") || document.getElementById("errorModal");
  if (modal) modal.classList.remove("active");
}