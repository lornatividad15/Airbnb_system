<?php
// FOR UPDATE CANCEL STATUS
include 'config.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_loginform.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookingId = intval($_POST['booking_id']);
    $action = $_POST['action'];
    if ($action === 'approve') {
        $status = 'Cancelled';
        $stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE id = ? AND status = 'Pending'");
        $stmt->bind_param('si', $status, $bookingId);
        $stmt->execute();
        $stmt->close();
    } elseif ($action === 'reject') {
        $status = 'Confirmed (Cancellation Rejected)';
        $stmt = $conn->prepare("UPDATE bookings SET status = ?, user_hidden = 0 WHERE id = ? AND status = 'Pending'");
        $stmt->bind_param('si', $status, $bookingId);
        $stmt->execute();
        $stmt->close();
    }
    header('Location: admin_page.php');
    exit;
} else {
    header('Location: admin_page.php');
    exit;
}
