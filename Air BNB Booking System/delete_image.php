<?php
include 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $image_id = intval($_GET['image_id']);
    $condo_id = intval($_GET['condo_id']);

    $delete = $conn->prepare("DELETE FROM condo_images WHERE id = ?");
    $delete->bind_param("i", $image_id);
    $delete->execute();
    $delete->close();

    $check = $conn->prepare("SELECT image_path FROM condo_images WHERE condo_id = ? LIMIT 1");
    $check->bind_param("i", $condo_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows === 0) {
        $update = $conn->prepare("UPDATE condos SET image_path = 'logo-1-primary.png' WHERE id = ?");
        $update->bind_param("i", $condo_id);
        $update->execute();
        $update->close();
    }

    $check->close();

    header("Location: edit_condo.php?id=$condo_id");
    exit;
}
?>