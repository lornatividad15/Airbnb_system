<?php
include 'config.php';
session_start();

$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = isset($_SESSION['admin_id']);

$cityFilter = isset($_GET['city']) ? trim($_GET['city']) : '';
$checkin = isset($_GET['checkin']) ? trim($_GET['checkin']) : '';
$checkout = isset($_GET['checkout']) ? trim($_GET['checkout']) : '';
$guests = isset($_GET['guest_count']) ? (int)$_GET['guest_count'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Find a Condo</title>
  <link rel="stylesheet" href="CSS/find_condo.css" />
</head>
<body>

<header>
  <div class="header-inner">
    <a href="Main Page.php" class="logo">
      <img src="Images/logo-light-transparent.png" alt="Logo" />
    </a>

    <div class="center-nav">
      <a href="find_condo.php">Find a Condo</a>
      <a href="#" id="myBookingsLink">My Bookings</a>
    </div>

    <div class="right-nav">
      <?php if ($isLoggedIn || $isAdmin): ?>
        <div class="user-menu" id="userMenu">
          <span id="accountName">My Account</span>
          <div class="dropdown" id="userDropdown">
            <a href="<?= $isAdmin ? 'admin_profile.php' : 'profile_form.php' ?>">Profile</a>
            <a href="logout.php" id="logoutBtn">Logout</a>
          </div>
        </div>
      <?php else: ?>
        <a href="signup form.php" id="signup">Sign Up</a>
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
      <a href="signup form.php" id="mobileSignup">Sign Up</a>
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
  <h2><?= $cityFilter ? "Condos in " . htmlspecialchars($cityFilter) : "All Available Condos" ?></h2>
  <div class="condo-container">
    <?php
    $sql = "SELECT * FROM condos WHERE is_available = 1";
    if ($cityFilter) {
      $safeCity = $conn->real_escape_string($cityFilter);
      $sql .= " AND city = '$safeCity'";
    }

    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0):
      while ($row = $result->fetch_assoc()):
        $condo_id = $row['id'];
        $name = htmlspecialchars($row['name']);

        $imgQuery = "SELECT image_path FROM condo_images WHERE condo_id = $condo_id";
        $imgResult = $conn->query($imgQuery);

        $images = [];
        if ($imgResult && $imgResult->num_rows > 0) {
          while ($imgRow = $imgResult->fetch_assoc()) {
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
        <img class="slider previewable" src="<?= $images[0] ?>" alt="<?= $name ?>" data-images='<?= $imagesJson ?>' />
      </div>
      <div class="condo-info">
        <h3><?= $name ?></h3>
        <?php if (!$isAdmin): ?>
        <button class="book-btn">Book Now</button>
        <?php endif; ?>
      </div>
    </div>
    <?php endwhile; else: ?>
    <p>No condos found.</p>
    <?php endif; ?>
  </div>
</section>

<!-- Modal for messages -->
<div id="bookingModal" class="modal-overlay">
  <div class="modal-box">
    <span class="close-btn" id="closeModalBtn">&times;</span>
    <p id="modalMessage">Placeholder message</p>
  </div>
</div>

<!-- Modal for image fullscreen -->
<div id="imageModal" class="modal-overlay">
  <div class="modal-box image-modal-box">
    <span class="close-btn" id="imageCloseBtn">&times;</span>
    <img id="modalImage" src="" alt="Preview"/>
  </div>
</div>

<script>
  window.isLoggedIn = <?= json_encode($isLoggedIn); ?>;
  window.isAdmin = <?= json_encode($isAdmin); ?>;
</script>

<script src="JS/find_condo.js"></script>

</body>
</html>