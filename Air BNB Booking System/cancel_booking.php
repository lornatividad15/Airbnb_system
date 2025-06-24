<?php
//FOR CANCEL BOOKING
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: Login form.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $bookingId = intval($_POST['booking_id']);
    $reason = trim($_POST['cancellation_reason']);
    $userId = $_SESSION['user_id'];

    $sql = "SELECT * FROM bookings WHERE id = $bookingId AND user_id = $userId AND status = 'confirmed'";
    $result = $conn->query($sql);

    if (!$result || $result->num_rows === 0) {
        header("Location: booking.php?error=notfound_or_cancelled");
        exit;
    }

    $reasonEscaped = $conn->real_escape_string($reason);
    $updateSql = "
        UPDATE bookings
        SET status = 'Pending',
            cancellation_reason = '$reasonEscaped'
        WHERE id = $bookingId AND user_id = $userId
    ";

    if ($conn->query($updateSql)) {
        header("Location: booking.php?cancelled=true");
        exit;
    } else {
        header("Location: booking.php?error=cancel_failed");
        exit;
    }
} else {
    header("Location: booking.php");
    exit;
}