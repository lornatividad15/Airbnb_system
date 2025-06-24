// FOR MAIN PAGE SCRIPTS
// MOBILE NAV TOGGLE
function toggleMenu() {
  document.querySelector(".btn").classList.toggle("active");
  document.getElementById("mobileNav").classList.toggle("active");
  document.getElementById("shadow").classList.toggle("active");
}

// IMAGE SLIDER
document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".slider").forEach((img) => {
    const images = JSON.parse(img.getAttribute("data-images") || "[]");
    if (images.length < 2) return;

    let i = 0;
    setInterval(() => {
      i = (i + 1) % images.length;
      img.src = images[i];
    }, 2000);
  });

  function showMainModal(message) {
    const modal = document.getElementById("mainModal");
    const modalMsg = document.getElementById("mainModalMessage");
    const closeModalBtn = document.getElementById("mainModalCloseBtn");

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

  document.getElementById("shadow").addEventListener("click", toggleMenu);

  const isLoggedIn = window.isLoggedIn || false;
  const isAdmin = window.isAdmin || false;
  const isAuthenticated = isLoggedIn || isAdmin;

  // Manage account UI
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
  const mobileLogin = document.getElementById("mobileLogin");
  const mobileSignup = document.getElementById("mobileSignup");
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

    if (mobileLogoutBtn) {
      mobileLogoutBtn.addEventListener("click", () => {
        window.location.href = "logout.php";
      });
    }

    if (logoutBtn) {
      logoutBtn.addEventListener("click", () => {
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

  // Disable Book Now and My Bookings for admin
  if (isAdmin) {
    document.querySelectorAll('.condo-card button').forEach(button => {
      button.style.display = "none";
    });
    if (myBookingsLink) myBookingsLink.style.display = "none";
    if (mobileMyBookings) mobileMyBookings.style.display = "none";
  }

  // BOOK NOW HANDLER
  document.querySelectorAll('.condo-card button').forEach(button => {
    button.addEventListener('click', (e) => {
      // Disable "Book Now" for admin
      if (isAdmin) {
        document.querySelectorAll('.book-now-btn').forEach(btn => {
          btn.style.display = "none";
        });
      }
    });
  });

  // BOOK NOW HANDLER (should not be nested inside the previous forEach)
  document.querySelectorAll('.book-now-btn').forEach(button => {
    button.addEventListener('click', (e) => {
      e.preventDefault();
      if (!isLoggedIn && !isAdmin) {
        showMainModal("Please log in to book a condo.");
        setTimeout(() => {
          window.location.href = "Login form.php";
        }, 2000);
      } else if (isAdmin) {
        showMainModal("Admins cannot book condos.");
      } else {
        const condoId = new URL(button.href).searchParams.get("id");
        window.location.href = `book_condo.php?id=${condoId}`;
      }
    });
  });

  // MY BOOKINGS
  const handleBookingsClick = (e) => {
    e.preventDefault();
    if (isAdmin) {
      showMainModal("Admins can't access 'My Bookings'.");
    } else if (!isLoggedIn) {
      showMainModal("Please log in first to view your bookings.");
      setTimeout(() => {
        window.location.href = "Login form.php";
      }, 2000);
    } else {
      window.location.href = "booking.php";
    }
  };

  if (myBookingsLink) myBookingsLink.addEventListener("click", handleBookingsClick);
  if (mobileMyBookings) mobileMyBookings.addEventListener("click", handleBookingsClick);
});

// IMAGE VIEWER LIGHTBOX
const viewer = document.getElementById("imageViewer");
const viewerImg = document.getElementById("viewerImage");
const closeViewer = document.querySelector(".close-viewer");
const nextBtn = document.getElementById("nextImg");
const prevBtn = document.getElementById("prevImg");

let currentImages = [];
let currentIndex = 0;

function openViewer(images, startIndex) {
  currentImages = images;
  currentIndex = startIndex;
  viewerImg.src = currentImages[currentIndex];
  viewer.style.display = "flex";
}

function closeImageViewer() {
  viewer.style.display = "none";
}

function showNext() {
  currentIndex = (currentIndex + 1) % currentImages.length;
  viewerImg.src = currentImages[currentIndex];
}

function showPrev() {
  currentIndex = (currentIndex - 1 + currentImages.length) % currentImages.length;
  viewerImg.src = currentImages[currentIndex];
}

// Set up event listeners
closeViewer.addEventListener("click", closeImageViewer);
nextBtn.addEventListener("click", showNext);
prevBtn.addEventListener("click", showPrev);

// Close on outside click
window.addEventListener("click", (e) => {
  if (e.target === viewer) closeImageViewer();
});

// Enable image click
document.querySelectorAll(".condo-image img").forEach((img) => {
  const images = JSON.parse(img.getAttribute("data-images") || "[]");
  img.style.cursor = "pointer";
  img.addEventListener("click", () => {
    if (images.length) openViewer(images, 0);
  });
});
