<?php
session_start();
require 'config.php';

function setModal($message) {
    $_SESSION['modal_message'] = $message;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $input    = $_POST['email']; // Can be email or username
    $password = $_POST['password'];

    $query = "SELECT id, username, password FROM users WHERE email = ? OR username = ?";
    $stmt  = $conn->prepare($query);
    $stmt->bind_param("ss", $input, $input);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: Main Page.php");
            exit;
        } else {
            setModal('❌ Incorrect password.');
            header("Location: Login form.php");
            exit;
        }
    } else {
        setModal('❌ Username or Email not found.');
        header("Location: Login form.php");
        exit;
    }

    $stmt->close();
    $conn->close();
}
?>