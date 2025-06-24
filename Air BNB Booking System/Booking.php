<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: Login form.php");
  exit;
}

$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = isset($_SESSION['admin_id']);
$userId = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="stylesheet" href="CSS/booking.css" />
  <title>My Bookings</title>
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
            <a href="<?= $isAdmin ? 'admin_profile.php' : 'profile.php' ?>">Profile</a>
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
    <h4>Navigation</h4>
    <a href="find_condo.php">Find a Condo</a>
    <a href="#" id="mobileMyBookings">My Bookings</a>
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
      <a href="signup form.php" id="mobileSignup">Sign Up</a>
      <a href="Login form.php" id="mobileLogin">Login</a>
    </div>
  <?php endif; ?>

  <div class="mobile-nav-section">
    <h4>Support</h4>
    <a href="FAQ.php">Need help?</a>
  </div>
</nav>

<section class="booking-section">
  <div class="booking-header">
    <h2>My Bookings</h2>
    <div class="underline"></div>
  </div>

  <div class="booking-container-wrapper">
    <div class="booking-container">
      <?php
      $sql = "
        SELECT b.*, c.name AS condo_name, c.description, c.id AS condo_id
        FROM bookings b
        JOIN condos c ON b.condo_id = c.id
        WHERE b.user_id = $userId AND (b.status != 'Cancelled' OR (b.status = 'Cancelled' AND IFNULL(b.user_hidden,0) = 0))
        ORDER BY b.checkin DESC
      ";
      $result = $conn->query($sql);

      if ($result && $result->num_rows > 0):
        while ($row = $result->fetch_assoc()):
          $condoId = $row['condo_id'];
          $bookingId = $row['id'];
          $condoName = htmlspecialchars($row['condo_name']);
          $description = htmlspecialchars($row['description']);
          $guestCount = $row['guest_count'];
          $checkin = date('M d, Y - h:i A', strtotime($row['checkin']));
          $checkout = date('M d, Y - h:i A', strtotime($row['checkout']));
          $status = $row['status'];

          $imgQuery = "SELECT image_path FROM condo_images WHERE condo_id = $condoId LIMIT 1";
          $imgResult = $conn->query($imgQuery);
          $imgPath = ($imgResult && $imgResult->num_rows > 0)
            ? 'Images/' . htmlspecialchars($imgResult->fetch_assoc()['image_path'])
            : 'Images/logo-1-primary.png';
      ?>
        <div class="booking-card" data-booking-id="<?= $bookingId ?>">
          <div class="booking-image">
            <img src="<?= $imgPath ?>" alt="<?= $condoName ?>" />
          </div>
          <div class="booking-info">
            <h3><?= $condoName ?></h3>
            <p><?= $description ?></p>
            <p><strong>Guests:</strong> <?= $guestCount ?></p>
            <p><strong>Check-in:</strong> <?= $checkin ?></p>
            <p><strong>Check-out:</strong> <?= $checkout ?></p>
            <div class="booking-status">
              <strong>Status:</strong> <?= htmlspecialchars($status) ?>
              <?php if ($status === 'Confirmed (Cancellation Rejected)'): ?>
                <span class="cancel-rejected-label" style="color: #388e3c; font-weight: bold; display: block; margin-top: 6px;">
                  Cancellation Rejected (Contact support@airbnb.com or call (555) 123-4567)
                </span>
              <?php endif; ?>
              <?php if ($status === 'Pending'): ?>
                <span class="pending-cancel-label" style="color: #fbc02d; font-weight: bold; display: block; margin-top: 6px;">
                  Pending Cancellation (Admin review)
                </span>
              <?php endif; ?>
            </div>
            <div class="booking-actions" style="margin-top:18px;">
              <?php if ($status === 'Confirmed' || $status === 'Confirmed (Cancellation Rejected)'): ?>
                <a class="edit-btn" href="edit_booking.php?booking_id=<?= $bookingId ?>">Edit</a>
              <?php endif; ?>
              <?php if ($status === 'Confirmed'): ?>
                <form method="POST" action="cancel_booking.php" class="cancel-form" style="display:inline;">
                  <input type="hidden" name="booking_id" value="<?= $bookingId ?>">
                  <button type="button" class="delete-btn open-cancel-modal">Cancel</button>
                </form>
              <?php elseif ($status === 'Confirmed (Cancellation Rejected)'): ?>
                <button type="button" class="cancel-btn" disabled style="opacity:0.5; cursor:not-allowed; margin-left:10px;">Cancel</button>
              <?php endif; ?>
              <?php if ($status === 'Cancelled'): ?>
                <form method="POST" action="delete_booking.php" class="delete-form" style="display:inline;">
                  <input type="hidden" name="booking_id" value="<?= $bookingId ?>">
                  <button type="submit" class="cancel-form-btn">Delete</button>
                </form>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endwhile; else: ?>
        <p class="no-bookings">You have no bookings at the moment.</p>
      <?php endif; ?>
    </div>
  </div>
</section>

<div id="deleteModal" class="modal-overlay">
  <div class="modal-box">
    <p>Please provide a reason for cancellation:</p>
    <textarea id="cancellationReason" placeholder="Enter reason..." required></textarea>
    <div style="margin-top: 20px;">
      <button id="confirmDeleteBtn" class="confirm-btn">Yes, Cancel Booking</button>
      <button id="cancelDeleteBtn" class="cancel-btn">Cancel</button>
    </div>
    <span class="close-btn" id="closeDeleteModal">&times;</span>
  </div>
</div>

<div id="messageModal" class="modal-overlay">
  <div class="modal-box">
    <p id="messageModalText"></p>
    <button id="messageModalCloseBtn" class="confirm-btn" style="margin-top: 15px;">OK</button>
    <span class="close-btn" id="messageModalCloseIcon">&times;</span>
  </div>
</div>

<div id="proceedModal" class="modal-overlay">
  <div class="modal-box">
    <p>Are you sure you want to proceed with this booking?</p>
    <div style="margin-top: 20px;">
      <button id="proceedYesBtn" class="confirm-btn">Yes</button>
      <button id="proceedNoBtn" class="cancel-btn">No</button>
    </div>
    <span class="close-btn" id="closeProceedModal">&times;</span>
  </div>
</div>

<!-- POPUP MESSAGE MODAL (ALIGNED WITH PAGE DESIGN) -->
<div id="popupMessageModal" class="modal-overlay">
  <div class="modal-box">
    <span class="close-btn" id="popupMessageCloseBtn">&times;</span>
    <p id="popupMessageText"></p>
    <button id="popupMessageOkBtn" class="confirm-btn">OK</button>
  </div>
</div>

<script src="JS/booking.js"></script>
<script>
  window.isLoggedIn = <?= json_encode($isLoggedIn); ?>;
  window.isAdmin = <?= json_encode($isAdmin); ?>;
  // Show message if booking delete (hide) was successful
  <?php if (isset($_GET['deleted'])): ?>
    window.addEventListener('DOMContentLoaded', function() {
      showPopupMessage('Booking deleted from your view.');
    });
  <?php endif; ?>

  // Hide booking card on Delete (user side only)
  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.hide-booking-btn').forEach(function(btn) {
      btn.addEventListener('click', function() {
        var card = btn.closest('.booking-card');
        if (card) card.style.display = 'none';
        // Optionally, send AJAX to set user_hidden=1 for this booking
      });
    });

    // Proceed modal logic
    let proceedModal = document.getElementById('proceedModal');
    let proceedYesBtn = document.getElementById('proceedYesBtn');
    let proceedNoBtn = document.getElementById('proceedNoBtn');
    let closeProceedModal = document.getElementById('closeProceedModal');
    let currentBookingId = null;
    let currentCard = null;

    document.querySelectorAll('.show-proceed-modal').forEach(function(btn) {
      btn.addEventListener('click', function() {
        currentBookingId = btn.getAttribute('data-booking-id');
        currentCard = btn.closest('.booking-card');
        proceedModal.classList.add('active');
      });
    });
    function closeProceed() {
      proceedModal.classList.remove('active');
      currentBookingId = null;
      currentCard = null;
    }
    proceedNoBtn.addEventListener('click', closeProceed);
    closeProceedModal.addEventListener('click', closeProceed);
    proceedYesBtn.addEventListener('click', function() {
      if (!currentBookingId || !currentCard) return closeProceed();
      // AJAX to update status to confirmed
      fetch('proceed_booking.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'booking_id=' + encodeURIComponent(currentBookingId)
      })
      .then(res => res.json())
      .then(data => {
        closeProceed(); // Close modal immediately
        if (data.success) {
          // Redirect to popup_message.php for confirmation
          window.location.href = 'popup_message.php?msg=' + encodeURIComponent('You have chosen to proceed with your booking.') + '&type=success&redirect=booking.php';
        } else {
          window.location.href = 'popup_message.php?msg=' + encodeURIComponent('Failed to proceed with your booking.') + '&type=error&redirect=booking.php';
        }
      })
      .catch(() => {
        closeProceed();
        window.location.href = 'popup_message.php?msg=' + encodeURIComponent('Failed to proceed with your booking.') + '&type=error&redirect=booking.php';
      });
    });
  });
</script>

</body>
</html>