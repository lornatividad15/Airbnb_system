<?php
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
        $status = 'cancelled';
        // Approve: set status only
        $stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE id = ? AND status = 'pending_cancel'");
        $stmt->bind_param('si', $status, $bookingId);
        $stmt->execute();
        $stmt->close();
    } elseif ($action === 'reject') {
        $status = 'cancel_rejected';
        // Reject: set status to cancel_rejected only
        $stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE id = ? AND status = 'pending_cancel'");
        $stmt->bind_param('si', $status, $bookingId);
        $stmt->execute();
        $stmt->close();
    }
    // Reset AUTO_INCREMENT if all bookings are deleted
    $count = $conn->query("SELECT COUNT(*) as count FROM bookings")->fetch_assoc();
    if ($count['count'] == 0) {
        $conn->query("ALTER TABLE bookings AUTO_INCREMENT = 1");
    }
    header('Location: admin_page.php');
    exit;
} else {
    header('Location: admin_page.php');
    exit;
}
