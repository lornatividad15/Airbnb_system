<?php
//FOR DELETE CONDO
include 'config.php';
session_start();

if (isset($_GET['id'])) {
    $condo_id = intval($_GET['id']);

    $img_sql = "SELECT image_path FROM condo_images WHERE condo_id = ?";
    $img_stmt = $conn->prepare($img_sql);
    $img_stmt->bind_param("i", $condo_id);
    $img_stmt->execute();
    $img_result = $img_stmt->get_result();
    $img_stmt->close();

    $del_img_sql = "DELETE FROM condo_images WHERE condo_id = ?";
    $del_img_stmt = $conn->prepare($del_img_sql);
    $del_img_stmt->bind_param("i", $condo_id);
    $del_img_stmt->execute();
    $del_img_stmt->close();

    $delete_sql = "DELETE FROM condos WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("i", $condo_id);
    $delete_stmt->execute();
    $delete_stmt->close();

    $check_condos = $conn->query("SELECT COUNT(*) AS total FROM condos")->fetch_assoc();
    $check_images = $conn->query("SELECT COUNT(*) AS total FROM condo_images")->fetch_assoc();

    if ($check_condos['total'] == 0) {
        $conn->query("ALTER TABLE condos AUTO_INCREMENT = 1");
    }

    if ($check_images['total'] == 0) {
        $conn->query("ALTER TABLE condo_images AUTO_INCREMENT = 1");
    }

    $modalMessage = 'Condo deleted successfully!';
    $modalType = 'success';
    $showModal = true;
    echo "<script>setTimeout(function(){ window.location.href='admin_page.php'; }, 1500);</script>";
    include __DIR__ . '/modal_snippet.php';
    exit;
} else {
    $modalMessage = 'No condo ID provided.';
    $modalType = 'error';
    $showModal = true;
    echo "<script>setTimeout(function(){ window.location.href='admin_page.php'; }, 1500);</script>";
    include __DIR__ . '/modal_snippet.php';
    exit;
}
?>