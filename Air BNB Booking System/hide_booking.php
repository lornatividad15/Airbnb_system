<?php
// THIS SCRIPT HANDLES HIDING A CANCEL_REJECTED BOOKING FOR THE USER BY SETTING user_hidden = 1
// IT IS ACCESSED VIA AJAX FROM THE USER BOOKING PAGE

// INCLUDE THE DATABASE CONFIGURATION FILE
include 'config.php';

// SET THE RESPONSE HEADER TO JSON
header('Content-Type: application/json');

// CHECK IF THE REQUEST METHOD IS POST AND BOOKING_ID IS PROVIDED
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['booking_id'])) {
    $booking_id = intval($_POST['booking_id']);

    // PREPARE THE SQL STATEMENT TO UPDATE user_hidden
    $stmt = $conn->prepare("UPDATE bookings SET user_hidden = 1 WHERE id = ? AND status = 'Confirmed (Cancellation Rejected)'");
    $stmt->bind_param('i', $booking_id);
    $success = $stmt->execute();
    $stmt->close();

    if ($success) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'DATABASE UPDATE FAILED']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'INVALID REQUEST']);
}
?>
