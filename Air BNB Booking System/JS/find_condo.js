document.addEventListener("DOMContentLoaded", () => {
  const isLoggedIn = window.isLoggedIn || false;
  const isAdmin = window.isAdmin || false;

  // Image slideshow
  document.querySelectorAll(".slider").forEach((img) => {
    const images = JSON.parse(img.getAttribute("data-images") || "[]");
    if (images.length < 2) return;
    let i = 0;
    setInterval(() => {
      i = (i + 1) % images.length;
      img.src = images[i];
    }, 3000);
  });

  // Dropdown toggle
  const userMenu = document.getElementById("userMenu");
  const accountName = document.getElementById("accountName");
  const userDropdown = document.getElementById("userDropdown");

  if (accountName && userDropdown) {
    accountName.addEventListener("click", function (e) {
      e.stopPropagation();
      userDropdown.classList.toggle("show");
    });

    document.addEventListener("click", function (e) {
      if (!userMenu.contains(e.target)) {
        userDropdown.classList.remove("show");
      }
    });
  }

  // Modal utility
  const showModal = (message) => {
    let existing = document.querySelector(".modal-overlay");
    if (existing) existing.remove();

    const overlay = document.createElement("div");
    overlay.className = "modal-overlay";
    overlay.style.display = "flex";

    overlay.innerHTML = `
      <div class="modal-box">
        <span class="close-btn">&times;</span>
        <p>${message}</p>
      </div>
    `;

    document.body.appendChild(overlay);

    overlay.querySelector(".close-btn").onclick = () => overlay.remove();
    overlay.onclick = (e) => {
      if (e.target === overlay) overlay.remove();
    };
  };

  // Hide book buttons for admin
  if (isAdmin) {
    document.querySelectorAll(".condo-card button").forEach((button) => {
      button.style.display = "none";
    });
  }

  // Booking button logic
  document.querySelectorAll(".condo-card button").forEach((button) => {
    button.addEventListener("click", (e) => {
      if (!isLoggedIn && !isAdmin) {
        e.preventDefault();
        showModal("Please log in to book a condo.");
        setTimeout(() => (window.location.href = "Login form.php"), 2000);
      } else if (isAdmin) {
        e.preventDefault();
        showModal("Admins cannot book condos.");
      }
    });
  });

  // Handle My Bookings logic
  const myBookingsLink = document.getElementById("myBookingsLink");
  const mobileMyBookings = document.getElementById("mobileMyBookings");

  // Hide from admin
  if (isAdmin) {
    if (myBookingsLink) myBookingsLink.style.display = "none";
    if (mobileMyBookings) mobileMyBookings.style.display = "none";
  }

  const handleBookingsClick = (e) => {
    e.preventDefault();
    if (!isLoggedIn) {
      showModal("Please log in first to view your bookings.");
      setTimeout(() => (window.location.href = "Login form.php"), 2000);
    } else {
      window.location.href = "booking-status.php";
    }
  };

  if (!isAdmin && myBookingsLink)
    myBookingsLink.addEventListener("click", handleBookingsClick);

  if (!isAdmin && mobileMyBookings)
    mobileMyBookings.addEventListener("click", handleBookingsClick);

  // Check-in default value
  const checkinInput = document.getElementById("checkin");
  if (checkinInput) {
    const now = new Date();
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
    checkinInput.value = now.toISOString().slice(0, 16);
  }

  // Floating label for datetime
  document.querySelectorAll(".floating-label-container").forEach((container) => {
    const input = container.querySelector("input[type='datetime-local']");
    container.addEventListener("click", () => {
      input.showPicker?.();
      input.focus();
    });
  });

  // Mobile menu toggle
  const shadow = document.getElementById("shadow");
  const mobileNav = document.getElementById("mobileNav");
  const toggleBtn = document.querySelector(".btn");

  if (toggleBtn && shadow && mobileNav) {
    toggleBtn.addEventListener("click", () => {
      toggleBtn.classList.toggle("active");
      shadow.classList.toggle("active");
      mobileNav.classList.toggle("active");
    });

    shadow.addEventListener("click", () => {
      toggleBtn.classList.remove("active");
      shadow.classList.remove("active");
      mobileNav.classList.remove("active");
    });
  }

  // Mobile profile / logout
  const mobileProfile = document.getElementById("mobileProfile");
  const mobileLogoutBtn = document.getElementById("mobileLogoutBtn");

  if (mobileProfile) {
    mobileProfile.addEventListener("click", function (e) {
      e.preventDefault();
      window.location.href = isAdmin ? "admin_profile.php" : "profile_form.php";
    });
  }

  if (mobileLogoutBtn) {
    mobileLogoutBtn.addEventListener("click", function () {
      window.location.href = "logout.php";
    });
  }

  // Desktop logout
  const logoutBtn = document.getElementById("logoutBtn");
  if (logoutBtn) {
    logoutBtn.addEventListener("click", function () {
      window.location.href = "logout.php";
    });
  }

  // Image modal viewer
  const imageModal = document.getElementById("imageModal");
  const modalImage = document.getElementById("modalImage");
  const imageCloseBtn = document.getElementById("imageCloseBtn");

  const prevBtn = document.createElement("button");
  const nextBtn = document.createElement("button");
  prevBtn.textContent = "←";
  nextBtn.textContent = "→";
  prevBtn.className = nextBtn.className = "nav-btn";
  imageModal.querySelector(".modal-box").appendChild(prevBtn);
  imageModal.querySelector(".modal-box").appendChild(nextBtn);

  let currentImages = [];
  let currentIndex = 0;

  document.querySelectorAll(".slider").forEach((img) => {
    img.addEventListener("click", () => {
      const data = img.getAttribute("data-images");
      if (!data) return;

      currentImages = JSON.parse(data);
      currentIndex = currentImages.indexOf(img.src);
      if (currentIndex === -1) currentIndex = 0;

      modalImage.src = currentImages[currentIndex];
      imageModal.style.display = "flex";
    });
  });

  const updateModalImage = () => {
    modalImage.src = currentImages[currentIndex];
  };

  prevBtn.addEventListener("click", (e) => {
    e.stopPropagation();
    currentIndex = (currentIndex - 1 + currentImages.length) % currentImages.length;
    updateModalImage();
  });

  nextBtn.addEventListener("click", (e) => {
    e.stopPropagation();
    currentIndex = (currentIndex + 1) % currentImages.length;
    updateModalImage();
  });

  if (imageCloseBtn) {
    imageCloseBtn.addEventListener("click", () => {
      imageModal.style.display = "none";
    });
  }

  imageModal.addEventListener("click", (e) => {
    if (e.target === imageModal) {
      imageModal.style.display = "none";
    }
  });
});