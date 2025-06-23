<?php
include 'config.php';
session_start();

$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = isset($_SESSION['admin_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="stylesheet" href="CSS/main_page.css"/>
  <title>Airbnb Booking System Main Page</title>
</head>
<body>

<header>
  <div class="header-inner">
    <a href="Main Page.php" class="logo">
      <img src="Images/logo-light-transparent.png" alt="Site Logo" />
    </a>

    <div class="center-nav" id="centerNav">
      <a href="find_condo.php">Find a Condo</a>
      <a href="#" id="myBookingsLink">My Bookings</a>
    </div>

    <div class="right-nav" id="rightNav">
      <?php if ($isLoggedIn || $isAdmin): ?>
        <div class="user-menu" id="userMenu">
          <span id="accountName">My Account</span>
          <div class="dropdown" id="userDropdown">
            <a href="<?= $isAdmin ? 'admin_profile.php' : 'profile_form.php' ?>">Profile</a>
            <a href="logout.php" id="logoutBtn">Logout</a>
          </div>
        </div>
      <?php else: ?>
        <a href="signup_form.php" id="signup">Sign Up</a>
        <a href="Login form.php" id="login">Login</a>
      <?php endif; ?>
    </div>

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
    <a href="find_condo.php">Find a Condo</a>
    <a href="#" id="mobileMyBookings">My Bookings</a>
  </div>

  <?php if ($isLoggedIn || $isAdmin): ?>
    <div class="mobile-nav-section" id="mobileUserMenu">
      <h4>My Account</h4>
      <a href="<?= $isAdmin ? 'admin_profile.php' : 'profile_form.php' ?>" id="mobileProfile">Profile</a>
      <a href="logout.php" id="mobileLogoutBtn">Logout</a>
    </div>
  <?php else: ?>
    <div class="mobile-nav-section" id="mobileAuthMenu">
      <h4>Account</h4>
      <a href="signup_form.php" id="mobileSignup">Sign Up</a>
      <a href="Login form.php" id="mobileLogin">Login</a>
    </div>
  <?php endif; ?>

  <div class="mobile-nav-section">
    <h4>Support</h4>
    <a href="FAQ.php">Need help?</a>
  </div>
</nav>

<?php include 'search_form.php'; ?>

<section class="condos">
  <div class="condo-header">
    <h2>Available Condos</h2>
    <div class="underline"></div>
  </div>

  <div class="condo-container">
    <?php
    $sql = "SELECT * FROM condos WHERE is_available = 1";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0):
      while($row = $result->fetch_assoc()):
        $condo_id = $row['id'];
        $name = htmlspecialchars($row['name']);

        $imgQuery = "SELECT image_path FROM condo_images WHERE condo_id = $condo_id";
        $imgResult = $conn->query($imgQuery);

        $images = [];
        if ($imgResult && $imgResult->num_rows > 0) {
          while($imgRow = $imgResult->fetch_assoc()) {
            $images[] = 'Images/' . htmlspecialchars($imgRow['image_path']);
          }
        }

        if (empty($images)) {
            if (!empty($row['image_path']) && file_exists('Images/' . $row['image_path'])) {
                $images[] = 'Images/' . htmlspecialchars($row['image_path']);
            } else {
                $images[] = 'Images/logo-1-primary.png';
            }
        }

        $imagesJson = htmlspecialchars(json_encode($images));
    ?>
      <div class="condo-card">
        <div class="condo-image">
          <img class="slider" src="<?= $images[0]; ?>" alt="<?= $name; ?>" data-images='<?= $imagesJson; ?>' />
        </div>
        <div class="condo-info">
          <h3><?= $name; ?></h3>
          <?php if (!$isAdmin): ?>
            <a href="book_condo.php?id=<?= $condo_id ?>" class="book-now-btn">Book Now</a>
          <?php endif; ?>
        </div>
      </div>
    <?php endwhile; else: ?>
      <p>No condos available at the moment.</p>
    <?php endif; ?>
  </div>
</section>

<div id="mainModal" class="modal-overlay">
  <div class="modal-box">
    <span class="close-btn" id="mainModalCloseBtn">&times;</span>
    <p id="mainModalMessage">This is a modal message.</p>
  </div>
</div>

<div id="imageViewer" class="image-viewer-modal">
  <span class="close-viewer">&times;</span>
  <img id="viewerImage" src="" alt="Condo Image">
  <div class="nav-btns">
    <button id="prevImg">&#10094;</button>
    <button id="nextImg">&#10095;</button>
  </div>
</div>

<script>
  window.isLoggedIn = <?= json_encode($isLoggedIn); ?>;
  window.isAdmin = <?= json_encode($isAdmin); ?>;
</script>

<script src="JS/main_page.js"></script>

</body>
</html>