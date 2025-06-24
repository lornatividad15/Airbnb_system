<?php
session_start();
include 'config.php';

function setModal($message, $type = 'error') {
    $_SESSION['modal_message'] = $message;
    $_SESSION['modal_type'] = $type;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $admin_id = $_POST['admin_id'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT * FROM admins WHERE admin_id = ? AND username = ?");
    $stmt->bind_param("ss", $admin_id, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($admin = $result->fetch_assoc()) {
        if (hash('sha256', $password) === $admin['password']) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_id'] = $admin['admin_id'];

            header("Location: admin_page.php"); 
            exit();
        } else {
            setModal('Incorrect password.', 'error');
            header("Location: admin_loginform.php");
            exit;
        }
    } else {
        setModal('Admin ID or Username not found.', 'error');
        header("Location: admin_loginform.php");
        exit;
    }

    $stmt->close();
    $conn->close();
}
?>