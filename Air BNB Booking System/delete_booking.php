<?php
//FOR DELETE BOOKING
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: Login form.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookingId = intval($_POST['booking_id']);
    $userId = $_SESSION['user_id'];
    $stmt = $conn->prepare("UPDATE bookings SET user_hidden = 1 WHERE id = ? AND user_id = ? AND status = 'cancelled'");
    $stmt->bind_param('ii', $bookingId, $userId);
    $stmt->execute();
    $stmt->close();
    header('Location: booking.php?deleted=1');
    exit;
} else {
    header('Location: booking.php');
    exit;
}
