// FAQ.js

function toggleMenu() {
  document.querySelector(".btn").classList.toggle("active");
  document.getElementById("mobileNav").classList.toggle("active");
  document.getElementById("shadow").classList.toggle("active");
}

document.addEventListener("DOMContentLoaded", function () {
  // MOBILE NAV TOGGLE
  document.getElementById("shadow").addEventListener("click", toggleMenu);

  const btn = document.querySelector(".btn");
  if (btn) btn.addEventListener("click", toggleMenu);

  const isLoggedIn = localStorage.getItem("userLoggedIn") === "true";

  const login = document.getElementById("login");
  const signup = document.getElementById("signup");
  const userMenu = document.getElementById("userMenu");
  const accountName = document.getElementById("accountName");
  const userDropdown = document.getElementById("userDropdown");
  const logoutBtn = document.getElementById("logoutBtn");

  const mobileUserMenu = document.getElementById("mobileUserMenu");
  const mobileAuthMenu = document.getElementById("mobileAuthMenu");
  const mobileProfile = document.getElementById("mobileProfile");
  const mobileLogoutBtn = document.getElementById("mobileLogoutBtn");

  // DROPDOWN
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

  if (isLoggedIn) {
    if (login) login.style.display = "none";
    if (signup) signup.style.display = "none";
    if (userMenu) userMenu.style.display = "block";
    if (accountName) accountName.textContent = "My Account";

    if (mobileUserMenu) mobileUserMenu.style.display = "block";
    if (mobileAuthMenu) mobileAuthMenu.style.display = "none";

    if (mobileProfile) {
      mobileProfile.addEventListener("click", function (e) {
        e.preventDefault();
        window.location.href = "Profile.html";
      });
    }

    if (logoutBtn) {
      logoutBtn.addEventListener("click", function () {
        localStorage.setItem("userLoggedIn", "false");
        location.reload();
      });
    }

    if (mobileLogoutBtn) {
      mobileLogoutBtn.addEventListener("click", function () {
        localStorage.setItem("userLoggedIn", "false");
        location.reload();
      });
    }
  } else {
    if (userMenu) userMenu.style.display = "none";
    if (login) login.style.display = "inline-block";
    if (signup) signup.style.display = "inline-block";
    if (mobileUserMenu) mobileUserMenu.style.display = "none";
    if (mobileAuthMenu) mobileAuthMenu.style.display = "block";
  }
});