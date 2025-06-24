<?php
include 'config.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<h2>Invalid condo ID</h2>";
    exit;
}

$condo_id = intval($_GET['id']);

$booking_sql = "SELECT 
    COUNT(*) AS total_bookings,
    SUM(guest_count) AS total_guests,
    SUM(DATEDIFF(checkout, checkin)) AS total_days,
    MIN(checkin) AS earliest_checkin,
    MAX(checkout) AS latest_checkout
  FROM bookings
  WHERE condo_id = ? AND status != 'cancelled' AND checkout >= NOW()";
$booking_stmt = $conn->prepare($booking_sql);
$booking_stmt->bind_param("i", $condo_id);
$booking_stmt->execute();
$booking_result = $booking_stmt->get_result();
$booking = $booking_result->fetch_assoc();
$booking_stmt->close();

$occupied_rooms = $booking['total_bookings'] ?? 0;
$guest_count = $booking['total_guests'] ?? 0;
$total_days = $booking['total_days'] ?? 0;
$check_in = $booking['earliest_checkin'] ?? 'N/A';
$check_out = $booking['latest_checkout'] ?? 'N/A';

$condo_sql = "SELECT name, city, address_details, description FROM condos WHERE id = ?";
$condo_stmt = $conn->prepare($condo_sql);
$condo_stmt->bind_param("i", $condo_id);
$condo_stmt->execute();
$condo_result = $condo_stmt->get_result();
$condo = $condo_result->fetch_assoc();
$condo_stmt->close();

// Fetch bookings for this condo
$bookings_sql = "SELECT b.*, u.username, u.firstname, u.lastname, u.email, u.phone_number FROM bookings b JOIN users u ON b.user_id = u.id WHERE b.condo_id = ? AND (b.status = 'Confirmed' OR b.status = 'Confirmed (Cancellation Rejected)') AND b.checkout >= NOW() ORDER BY b.checkin DESC";
$bookings_stmt = $conn->prepare($bookings_sql);
$bookings_stmt->bind_param("i", $condo_id);
$bookings_stmt->execute();
$bookings_result = $bookings_stmt->get_result();
$bookings_stmt->close();

if ($condo):
?>

<link rel="stylesheet" href="CSS/condo_details.css">

<div class="condo-details-box">
  <h2><?= htmlspecialchars($condo['name']) ?></h2>
  <p><strong>City:</strong> <?= htmlspecialchars($condo['city']) ?></p>
  <p><strong>Address:</strong> <?= htmlspecialchars($condo['address_details']) ?></p>
  <p><strong>Description:</strong><br><?= nl2br(htmlspecialchars($condo['description'])) ?></p>

  <hr style="margin: 15px 0;">

  <h3>Booking Information</h3>
  <p><strong>Total Occupied Rooms:</strong> <?= $occupied_rooms ?></p>
  <p><strong>Number of Guests:</strong> <?= $guest_count ?></p>
  <p><strong>Day/s Use:</strong> <?= $total_days ?></p>
  <p><strong>Earliest Check-in:</strong> <?= $check_in ?></p>
  <p><strong>Latest Check-out:</strong> <?= $check_out ?></p>
</div>

<div class="condo-users-box">
  <h3>Users Who Booked This Condo</h3>
  <?php if ($bookings_result->num_rows > 0): ?>
    <div class="condo-bookings-list">
      <?php while ($b = $bookings_result->fetch_assoc()): ?>
        <div class="condo-booking-entry">
          <p><strong>User:</strong> <?= htmlspecialchars($b['firstname'] . ' ' . $b['lastname']) ?> (<?= htmlspecialchars($b['username']) ?>)</p>
          <p><strong>Email:</strong> <?= htmlspecialchars($b['email']) ?> | <strong>Phone:</strong> <?= htmlspecialchars($b['phone_number']) ?></p>
          <p><strong>Guests:</strong> <?= htmlspecialchars($b['guest_count']) ?></p>
          <p><strong>Day/s Use:</strong> <?= (new DateTime($b['checkin']))->diff(new DateTime($b['checkout']))->days ?></p>
          <p><strong>Check-in:</strong> <?= date('M d, Y - h:i A', strtotime($b['checkin'])) ?> | <strong>Check-out:</strong> <?= date('M d, Y - h:i A', strtotime($b['checkout'])) ?></p>
          <p><strong>Status:</strong> <?= htmlspecialchars($b['status']) ?></p>
        </div>
      <?php endwhile; ?>
    </div>
  <?php else: ?>
    <p>No bookings for this condo yet.</p>
  <?php endif; ?>
</div>

<?php
else:
  echo "<h2>Condo not found</h2>";
endif;
?>