document.addEventListener("DOMContentLoaded", function () {
  const whereToDropdown = document.querySelector(".dropdown-input");
  const cityList = document.querySelector(".dropdown-list");
  const selectedCityInput = document.getElementById("selectedCity");

  // Dropdown logic
  if (whereToDropdown && cityList) {
    whereToDropdown.addEventListener("click", (e) => {
      e.stopPropagation();
      cityList.classList.toggle("show");
    });

    cityList.querySelectorAll(".dropdown-item").forEach(item => {
      item.addEventListener("click", () => {
        const city = item.textContent;
        whereToDropdown.textContent = city;
        selectedCityInput.value = city;
        cityList.classList.remove("show");
      });
    });

    document.addEventListener("click", (e) => {
      if (!cityList.contains(e.target) && e.target !== whereToDropdown) {
        cityList.classList.remove("show");
      }
    });
  }

  const checkinInput = document.getElementById("checkin");
  const checkoutInput = document.getElementById("checkout");

  // Set default min datetime
  const now = new Date();
  now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
  const formattedNow = now.toISOString().slice(0, 16);
  if (checkinInput) {
    checkinInput.min = formattedNow;
    checkinInput.value = formattedNow;
  }
  if (checkoutInput) {
    checkoutInput.min = formattedNow;
  }

  // Floating label interaction
  document.querySelectorAll(".floating-label-container").forEach(container => {
    const input = container.querySelector("input[type='datetime-local']");
    container.addEventListener("click", () => {
      input.showPicker?.();
      input.focus();
    });
  });

  // Use already injected bookedRanges from PHP
  const bookedData = (typeof bookedRanges !== "undefined") ? bookedRanges : [];
  
  function showBookingModal(message) {
    const modal = document.getElementById("bookingModal");
    const modalMsg = document.getElementById("modalMessage");
    const closeModalBtn = document.getElementById("closeModalBtn");

    if (modal && modalMsg && closeModalBtn) {
      modalMsg.textContent = message;
      modal.style.display = "flex";

      closeModalBtn.onclick = () => {
        modal.style.display = "none";
      };

      window.onclick = (e) => {
        if (e.target === modal) {
          modal.style.display = "none";
        }
      };
    }
  }

  function isOverlappingRange(start, end, selectedCity) {
    return bookedData.some(range => {
      if (range.city !== selectedCity) return false;
      const bookedStart = new Date(range.start);
      const bookedEnd = new Date(range.end);
      return start < bookedEnd && end > bookedStart;
    });
  }

  function validateDateRange() {
    const checkinVal = checkinInput.value;
    const checkoutVal = checkoutInput.value;
    const selectedCity = document.getElementById("selectedCity").value;

    if (!checkinVal || !checkoutVal || !selectedCity) return;

    const checkinDate = new Date(checkinVal);
    const checkoutDate = new Date(checkoutVal);

    if (checkinDate >= checkoutDate) {
      showBookingModal("Check-out must be after check-in.");
      return;
    }

    if (isOverlappingRange(checkinDate, checkoutDate, selectedCity)) {
      showBookingModal("Selected dates overlap with an existing booking in " + selectedCity + ". Please choose a different range.");
      checkinInput.value = '';
      checkoutInput.value = '';
    }
  }


  if (checkinInput && checkoutInput) {
    checkinInput.addEventListener("change", validateDateRange);
    checkoutInput.addEventListener("change", validateDateRange);
  }
});