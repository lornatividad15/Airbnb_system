<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = isset($_SESSION['admin_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="stylesheet" href="CSS/FAQ.css"/>
  <title>Need Help?</title>
</head>
<body>

<header>
  <div class="header-inner">
    <a href="Main Page.php" class="logo">
      <img src="Images/logo-light-transparent.png" alt="Logo">
    </a>
    <button class="btn">
      <span></span>
      <span></span>
      <span></span>
    </button>
  </div>
</header>

<div class="shadow" id="shadow"></div>

<nav class="mobile-nav" id="mobileNav">
  <div class="mobile-nav-section" id="mobileNavLinks">
    <h4>Navigation</h4>
    <a href="find_condo.php">Find a Condo</a>
    <?php if (!$isAdmin): ?>
      <a href="Booking.php" id="mobileMyBookings">My Bookings</a>
    <?php endif; ?>
  </div>

  <?php if ($isLoggedIn || $isAdmin): ?>
  <div class="mobile-nav-section" id="mobileUserMenu">
    <h4>My Account</h4>
    <a href="<?= $isAdmin ? 'admin_profile.php' : 'profile.php' ?>" id="mobileProfile">Profile</a>
    <a href="logout.php" id="mobileLogoutBtn">Logout</a>
  </div>
  <?php else: ?>
  <div class="mobile-nav-section" id="mobileAuthMenu">
    <h4>Account</h4>
    <a href="signup_form.php" id="mobileSignup">Sign Up</a>
    <a href="Login form.php" id="mobileLogin">Login</a>
  </div>
  <?php endif; ?>
</nav>

<div class="faq-container">
  <h1 class="faq-title">Frequently Asked Questions</h1>

  <div class="faq-item">
    <h2 class="faq-question">How do I book a condo?</h2>
    <p class="faq-answer">To book a condo, simply navigate to the "Find a Condo" section...</p>
  </div>

  <div class="faq-item">
    <h2 class="faq-question">What payment methods are accepted?</h2>
    <p class="faq-answer">We accept credit cards, debit cards, and PayPal...</p>
  </div>

  <div class="faq-item">
    <h2 class="faq-question">Can I cancel my booking?</h2>
    <p class="faq-answer">Yes, you can cancel your booking within 24 hours...</p>
  </div>

  <div class="faq-item">
    <h2 class="faq-question">How do I contact customer support?</h2>
    <p class="faq-answer">You can reach our support team via email at support@airbnb.com.</p>
  </div>
</div>

<script>
  window.isLoggedIn = <?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>;
  window.isAdmin = <?= isset($_SESSION['admin_id']) ? 'true' : 'false' ?>;
</script>

<script src="JS/FAQ.js"></script>

<!-- Modal for login prompt -->
<div id="loginModal" class="modal">
  <div class="modal-content">
    <p>You must log in first to access your bookings.</p>
    <div class="modal-actions">
      <a href="Login form.php" class="modal-btn">Login</a>
      <button class="modal-btn close-btn" id="closeModal">Close</button>
    </div>
  </div>
</div>

</body>
</html>