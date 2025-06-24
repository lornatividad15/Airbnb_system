<?php
//FOR DELETE FROM DB
include 'config.php';
session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_loginform.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookingId = intval($_POST['booking_id']);
    $stmt = $conn->prepare("DELETE FROM bookings WHERE id = ?");
    $stmt->bind_param('i', $bookingId);
    $stmt->execute();
    $stmt->close();
    $count = $conn->query("SELECT COUNT(*) as count FROM bookings")->fetch_assoc();
    if ($count['count'] == 0) {
        $conn->query("ALTER TABLE bookings AUTO_INCREMENT = 1");
    }
    header('Location: admin_page.php?deleted=1');
    exit;
} else {
    header('Location: admin_page.php');
    exit;
}
