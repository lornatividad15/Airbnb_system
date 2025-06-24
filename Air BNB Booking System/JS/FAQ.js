// FOR FAQ SCRIPTS
function toggleMenu() {
  document.querySelector(".btn").classList.toggle("active");
  document.getElementById("mobileNav").classList.toggle("active");
  document.getElementById("shadow").classList.toggle("active");
}

document.addEventListener("DOMContentLoaded", function () {
  document.getElementById("shadow").addEventListener("click", toggleMenu);
  const btn = document.querySelector(".btn");
  if (btn) btn.addEventListener("click", toggleMenu);

  const isLoggedIn = window.isLoggedIn || false;
  const isAdmin = window.isAdmin || false;

  const mobileUserMenu = document.getElementById("mobileUserMenu");
  const mobileAuthMenu = document.getElementById("mobileAuthMenu");
  const mobileProfile = document.getElementById("mobileProfile");
  const mobileLogoutBtn = document.getElementById("mobileLogoutBtn");
  const mobileSignup = document.getElementById("mobileSignup");
  const mobileLogin = document.getElementById("mobileLogin");
  const mobileMyBookings = document.getElementById("mobileMyBookings");

  // Control visibility of mobile nav sections
  if (isLoggedIn || isAdmin) {
    if (mobileUserMenu) mobileUserMenu.style.display = "flex";
    if (mobileAuthMenu) mobileAuthMenu.style.display = "none";
  } else {
    if (mobileUserMenu) mobileUserMenu.style.display = "none";
    if (mobileAuthMenu) mobileAuthMenu.style.display = "flex";
  }

  // Hide 'My Bookings' if admin
  if (isAdmin && mobileMyBookings) {
    mobileMyBookings.style.display = "none";
  }

  // Redirect profile link
  if (mobileProfile) {
    mobileProfile.addEventListener("click", function (e) {
      e.preventDefault();
      window.location.href = isAdmin ? "admin_profile.php" : "profile.php";
    });
  }

  // Logout logic
  if (mobileLogoutBtn) {
    mobileLogoutBtn.addEventListener("click", function () {
      window.location.href = "logout.php";
    });
  }

  // Modal for login when clicking 'My Bookings' if not logged in
  const loginModal = document.getElementById("loginModal");
  const closeModal = document.getElementById("closeModal");

  if (mobileMyBookings && !isLoggedIn && !isAdmin) {
    mobileMyBookings.addEventListener("click", function (e) {
      e.preventDefault();
      if (loginModal) loginModal.style.display = "flex";
    });
  }

  if (closeModal) {
    closeModal.addEventListener("click", function () {
      loginModal.style.display = "none";
    });
  }

  // Close modal when clicking outside it
  window.addEventListener("click", function (event) {
    if (event.target === loginModal) {
      loginModal.style.display = "none";
    }
  });
});