<?php
//FOR PROCEED BOOKING
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookingId = intval($_POST['booking_id'] ?? 0);
    $userId = $_SESSION['user_id'];
    $stmt = $conn->prepare("UPDATE bookings SET status = 'confirmed' WHERE id = ? AND user_id = ? AND status = 'cancel_rejected'");
    $stmt->bind_param('ii', $bookingId, $userId);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
    $stmt->close();
    exit;
}
http_response_code(400);
echo json_encode(['error' => 'Bad request']);
exit;
