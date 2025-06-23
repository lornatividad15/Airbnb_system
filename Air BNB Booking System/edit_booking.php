<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: Login form.php");
  exit;
}

$bookingId = isset($_GET['booking_id']) ? intval($_GET['booking_id']) : 0;
$userId = $_SESSION['user_id'];

$showError = false;
$errorMessage = '';
$booking = null;
$condoName = '';
$condoImage = 'Images/logo-1-primary.png';
$blockedDates = [];

if ($bookingId <= 0) {
  $showError = true;
  $errorMessage = 'Invalid booking ID.';
} else {
  $sql = "SELECT b.*, c.name AS condo_name, c.id AS condo_id
          FROM bookings b
          JOIN condos c ON b.condo_id = c.id
          WHERE b.id = $bookingId AND b.user_id = $userId";
  $result = $conn->query($sql);

  if (!$result || $result->num_rows === 0) {
    $showError = true;
    $errorMessage = 'Booking not found or unauthorized access.';
  } else {
    $booking = $result->fetch_assoc();
    $condoId = $booking['condo_id'];
    $condoName = htmlspecialchars($booking['condo_name']);

    $imgQuery = "SELECT image_path FROM condo_images WHERE condo_id = $condoId LIMIT 1";
    $imgResult = $conn->query($imgQuery);
    if ($imgResult && $imgResult->num_rows > 0) {
      $imgRow = $imgResult->fetch_assoc();
      $condoImage = 'Images/' . htmlspecialchars($imgRow['image_path']);
    }

    // Block other bookings for the same condo (excluding current booking)
    $blockedQuery = "SELECT checkin, checkout FROM bookings 
                     WHERE condo_id = $condoId AND id != $bookingId";
    $blockedResult = $conn->query($blockedQuery);
    if ($blockedResult && $blockedResult->num_rows > 0) {
      while ($row = $blockedResult->fetch_assoc()) {
        $blockedDates[] = [
          'start' => $row['checkin'],
          'end' => $row['checkout']
        ];
      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Edit Booking</title>
  <link rel="stylesheet" href="CSS/edit_booking.css" />
</head>
<body>
  <header>
    <div class="header-inner">
      <a href="Main Page.php" class="logo">
        <img src="Images/logo-light-transparent.png" alt="Site Logo" />
      </a>
      <div class="center-nav">
        <a href="find_condo.php">Find a Condo</a>
        <a href="booking.php">My Bookings</a>
      </div>
    </div>
  </header>

  <section class="edit-booking-section">
    <div class="form-container">
      <h2>Edit Booking</h2>

      <?php if ($showError): ?>
        <div class="modal-overlay active" id="errorModal">
          <div class="modal-box">
            <p><?= htmlspecialchars($errorMessage) ?></p>
            <div class="btns">
              <button class="cancel-delete" onclick="closeModal()">Close</button>
            </div>
          </div>
        </div>
      <?php else: ?>
        <div class="condo-preview">
          <img src="<?= $condoImage ?>" alt="<?= $condoName ?>">
          <h3><?= $condoName ?></h3>
        </div>

        <form action="update_booking.php" method="POST" id="editBookingForm">
          <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">

          <label for="guest_count">Guest Count:</label>
          <input type="number" id="guest_count" name="guest_count" value="<?= $booking['guest_count'] ?>" min="1" required>

          <label for="checkin">Check-in:</label>
          <div class="input-wrapper">
            <input type="datetime-local" id="checkin" name="checkin"
              value="<?= date('Y-m-d\TH:i', strtotime($booking['checkin'])) ?>" required>
          </div>

          <label for="checkout">Check-out:</label>
          <div class="input-wrapper">
            <input type="datetime-local" id="checkout" name="checkout"
              value="<?= date('Y-m-d\TH:i', strtotime($booking['checkout'])) ?>" required>
          </div>

          <div class="form-actions">
            <button type="submit" class="save">Save Changes</button>
            <a href="booking.php" class="cancel">Cancel</a>
          </div>
        </form>
      <?php endif; ?>
    </div>
  </section>

  <!-- Modal for date conflict -->
  <div class="modal-overlay" id="conflictModal">
    <div class="modal-box">
      <p id="conflictMessage">Selected date is unavailable. Please choose a different date.</p>
      <div class="btns">
        <button class="cancel-delete" onclick="closeModal()">Close</button>
      </div>
    </div>
  </div>

  <script>
    const blockedRanges = <?= json_encode($blockedDates); ?>;
    const minDate = "<?= date('Y-m-d\TH:i'); ?>";
  </script>
  <script src="JS/edit_booking.js"></script>
</body>
</html>