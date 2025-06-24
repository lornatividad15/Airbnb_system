<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: Login form.php");
  exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $bookingId = intval($_POST['booking_id']);
  $guestCount = intval($_POST['guest_count']);
  $checkin = $_POST['checkin'];
  $checkout = $_POST['checkout'];
  $userId = $_SESSION['user_id'];

  $sql = "SELECT * FROM bookings WHERE id = $bookingId AND user_id = $userId";
  $result = $conn->query($sql);

  if (!$result || $result->num_rows === 0) {
    header("Location: edit_booking.php?booking_id=$bookingId&error=notfound");
    exit;
  }

  $booking = $result->fetch_assoc();
  $condoId = $booking['condo_id'];

  $checkin_dt = strtotime($checkin);
  $checkout_dt = strtotime($checkout);

  if ($checkin_dt >= $checkout_dt || $checkin_dt < strtotime('now')) {
    header("Location: edit_booking.php?booking_id=$bookingId&error=invalid_date");
    exit;
  }

  $conflictQuery = "
    SELECT * FROM bookings
    WHERE condo_id = $condoId
      AND id != $bookingId
      AND (
        ('$checkin' BETWEEN checkin AND checkout) OR
        ('$checkout' BETWEEN checkin AND checkout) OR
        (checkin BETWEEN '$checkin' AND '$checkout')
      )
  ";
  $conflictResult = $conn->query($conflictQuery);
  if ($conflictResult && $conflictResult->num_rows > 0) {
    header("Location: edit_booking.php?booking_id=$bookingId&error=conflict");
    exit;
  }

  $updateSql = "
    UPDATE bookings
    SET guest_count = $guestCount,
        checkin = '$checkin',
        checkout = '$checkout'
    WHERE id = $bookingId AND user_id = $userId
  ";

  if ($conn->query($updateSql)) {
    header("Location: booking.php?updated=true");
    exit;
  } else {
    header("Location: edit_booking.php?booking_id=$bookingId&error=update_failed");
    exit;
  }
} else {
  header("Location: booking.php");
  exit;
}