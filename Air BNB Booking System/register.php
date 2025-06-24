<?php
session_start();
require 'config.php';

function setModal($message) {
    $_SESSION['modal_message'] = $message;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username         = $_POST['username'];
    $firstname        = $_POST['firstname'];
    $lastname         = $_POST['lastname'];
    $email            = $_POST['email'];
    $phone_number     = $_POST['phone_number'];
    $password         = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $birthdate        = $_POST['birthdate'];
    $sex              = $_POST['sex'];
    $age              = $_POST['age'];

    // Password check
    if ($password !== $confirm_password) {
        setModal('\u274c Password and Confirm Password do not match.');
        header("Location: signup_form.php");
        exit;
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check email
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->close();
        setModal('\u274c Email already exists. Please use a different one.');
        header("Location: signup_form.php");
        exit;
    }
    $stmt->close();

    // Check username in users
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->close();
        setModal('\u274c Username already exists. Please choose another.');
        header("Location: signup_form.php");
        exit;
    }
    $stmt->close();

    // Check username in admins
    $stmt = $conn->prepare("SELECT id FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->close();
        setModal('\u274c Username already exists. Please choose another.');
        header("Location: signup_form.php");
        exit;
    }
    $stmt->close();

    // Handle profile picture
    $profile_picture = null;
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $profile_picture = file_get_contents($_FILES['profile_picture']['tmp_name']);
    }

    // Insert user
    $stmt = $conn->prepare("INSERT INTO users (username, firstname, lastname, email, phone_number, password, birthdate, sex, age, profile_picture) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssis", $username, $firstname, $lastname, $email, $phone_number, $hashed_password, $birthdate, $sex, $age, $profile_picture);
    
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        setModal("\u2705 Registration successful! You may now log in.");
        header("Location: Login form.php");
        exit;
    } else {
        $error = htmlspecialchars($stmt->error);
        $stmt->close();
        $conn->close();
        setModal("\u274c An error occurred: $error");
        header("Location: signup_form.php");
        exit;
    }
}
?>