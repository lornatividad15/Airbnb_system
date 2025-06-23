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
    
    <button class="btn" onclick="toggleMenu()">
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
      <a href="FindCondo.php">Find a Condo</a>
      <a href="#" id="mobileMyBookings">My Bookings</a>
    </div>

    <div class="mobile-nav-section" id="mobileUserMenu" style="display: none;">
      <h4>My Account</h4>
      <a href="Profile.php" id="mobileProfile">Profile</a>
      <a href="#" id="mobileLogoutBtn">Logout</a>
    </div>

    <div class="mobile-nav-section" id="mobileAuthMenu" style="display: none;">
      <h4>Account</h4>
      <a href="signup form.php" id="mobileSignup">Sign Up</a>
      <a href="Login form.php" id="mobileLogin">Login</a>
    </div>
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

  
  <script src="JS/main_page.js"></script>

</body>
</html>