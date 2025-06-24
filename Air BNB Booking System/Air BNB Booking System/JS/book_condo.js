// ========== GLOBAL MODAL FUNCTION ==========
function showModal(message) {
  const modal = document.getElementById("mainModal");
  const msg = document.getElementById("mainModalMessage");
  const closeBtn = document.getElementById("mainModalCloseBtn");

  if (modal && msg && closeBtn) {
    msg.textContent = message;
    modal.style.display = "flex";

    closeBtn.onclick = () => (modal.style.display = "none");
    window.onclick = (e) => {
      if (e.target === modal) modal.style.display = "none";
    };
  } else {
    alert(message); // fallback
  }
}

// ========== NAVIGATION & ACCOUNT HANDLING ==========
function toggleMenu() {
  document.querySelector(".btn").classList.toggle("active");
  document.getElementById("mobileNav").classList.toggle("active");
  document.getElementById("shadow").classList.toggle("active");
}

document.addEventListener("DOMContentLoaded", function () {
  const isLoggedIn = window.isLoggedIn || false;
  const isAdmin = window.isAdmin || false;
  const isAuthenticated = isLoggedIn || isAdmin;

  const login = document.getElementById("login");
  const signup = document.getElementById("signup");
  const userMenu = document.getElementById("userMenu");
  const accountName = document.getElementById("accountName");
  const userDropdown = document.getElementById("userDropdown");
  const logoutBtn = document.getElementById("logoutBtn");

  const mobileUserMenu = document.getElementById("mobileUserMenu");
  const mobileProfile = document.getElementById("mobileProfile");
  const mobileLogoutBtn = document.getElementById("mobileLogoutBtn");
  const mobileAuthMenu = document.getElementById("mobileAuthMenu");
  const myBookingsLink = document.getElementById("myBookingsLink");
  const mobileMyBookings = document.getElementById("mobileMyBookings");

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

  if (isAuthenticated) {
    if (login) login.style.display = "none";
    if (signup) signup.style.display = "none";
    if (userMenu) userMenu.style.display = "block";
    if (accountName) accountName.textContent = "My Account";

    if (mobileUserMenu) mobileUserMenu.style.display = "block";
    if (mobileAuthMenu) mobileAuthMenu.style.display = "none";

    if (mobileProfile) {
      mobileProfile.addEventListener("click", () => {
        window.location.href = isAdmin ? "admin_profile.php" : "profile_form.php";
      });
    }

    if (logoutBtn) {
      logoutBtn.addEventListener("click", () => {
        window.location.href = "logout.php";
      });
    }

    if (mobileLogoutBtn) {
      mobileLogoutBtn.addEventListener("click", () => {
        window.location.href = "logout.php";
      });
    }
  } else {
    if (userMenu) userMenu.style.display = "none";
    if (login) login.style.display = "inline-block";
    if (signup) signup.style.display = "inline-block";
    if (mobileUserMenu) mobileUserMenu.style.display = "none";
    if (mobileAuthMenu) mobileAuthMenu.style.display = "block";
  }

  const handleBookingsClick = (e) => {
    e.preventDefault();
    if (isAdmin) {
      showModal("Admins can't access 'My Bookings'.");
    } else if (!isLoggedIn) {
      showModal("Please log in first to view your bookings.");
      setTimeout(() => {
        window.location.href = "Login form.php";
      }, 2000);
    } else {
      window.location.href = "booking.php";
    }
  };

  if (myBookingsLink) myBookingsLink.addEventListener("click", handleBookingsClick);
  if (mobileMyBookings) mobileMyBookings.addEventListener("click", handleBookingsClick);

  const shadow = document.getElementById("shadow");
  if (shadow) shadow.addEventListener("click", toggleMenu);
});

// ========== FORM VALIDATION + BOOKED DATE BLOCKING ==========
document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("bookingForm");
  const checkinInput = document.getElementById("checkin");
  const checkoutInput = document.getElementById("checkout");
  const guestCountInput = document.getElementById("guest_count");

  if (!form || !checkinInput || !checkoutInput || !guestCountInput) return;

  const now = new Date();
  now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
  const formattedNow = now.toISOString().slice(0, 16);
  checkinInput.min = formattedNow;
  checkoutInput.min = formattedNow;

  ["checkin", "checkout"].forEach(id => {
    const input = document.getElementById(id);
    if (input) {
      input.addEventListener("click", () => {
        input.showPicker?.();
        input.focus();
      });
    }
  });

  const bookedData = typeof bookedRanges !== "undefined" ? bookedRanges : [];

  function isDateInBookedRange(dateStr) {
    const date = new Date(dateStr);
    return bookedData.some(range => {
      const start = new Date(range.start);
      const end = new Date(range.end);
      return date >= start && date < end;
    });
  }

  checkinInput.addEventListener("change", () => {
    const dateStr = checkinInput.value;
    if (isDateInBookedRange(dateStr)) {
      showModal("That check-in date is already booked. Please choose another.");
      setTimeout(() => {
        checkinInput.value = "";
      }, 200);
    }
  });

  checkoutInput.addEventListener("change", () => {
    const dateStr = checkoutInput.value;
    if (isDateInBookedRange(dateStr)) {
      showModal("That check-out date is already booked. Please choose another.");
      setTimeout(() => {
        checkoutInput.value = "";
      }, 200);
    }
  });

  form.addEventListener("submit", (e) => {
    const checkin = new Date(checkinInput.value);
    const checkout = new Date(checkoutInput.value);

    if (checkout <= checkin) {
      e.preventDefault();
      showModal("Check-out date must be after check-in date.");
      return;
    }

    const guests = parseInt(guestCountInput.value, 10);
    if (isNaN(guests) || guests < 1) {
      e.preventDefault();
      showModal("Please enter a valid number of guests.");
      return;
    }

    if (isDateInBookedRange(checkinInput.value) || isDateInBookedRange(checkoutInput.value)) {
      e.preventDefault();
      showModal("Selected date overlaps with an existing booking. Please choose another range.");
      return;
    }
  });
});