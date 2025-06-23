<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: Login form.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$errors = [];

// FETCH USER
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// DELETE ACCOUNT
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_account'])) {
    $confirm_identifier = $_POST['confirm_identifier'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (
        ($confirm_identifier === $user['email'] || $confirm_identifier === $user['username']) &&
        password_verify($confirm_password, $user['password'])
    ) {
        $conn->query("DELETE FROM users WHERE id = $user_id");
        $count_check = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc();
        if ($count_check['count'] == 0) {
            $conn->query("ALTER TABLE users AUTO_INCREMENT = 1");
        }
        session_destroy();
        $_SESSION['profile_message'] = '✅ Account deleted successfully.';
        header("Location: Login form.php");
        exit;
    } else {
        $_SESSION['profile_message'] = '❌ Invalid credentials. Account not deleted.';
        header("Location: profile_form.php");
        exit;
    }
}

// PROFILE UPDATE
if ($_SERVER["REQUEST_METHOD"] === "POST" && !isset($_POST['delete_account'])) {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $username = $_POST['username'];
    $phone_number = $_POST['phone_number'];
    $birthdate = $_POST['birthdate'];
    $sex = $_POST['sex'];
    $age = $_POST['age'];

    $current_email = $_POST['email'];
    $new_email = $_POST['new_email'];
    $confirm_email = $_POST['confirm_email'];

    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $profile_picture = $user['profile_picture'];

    if (!empty($_FILES['profile_picture']['tmp_name'])) {
        $profile_picture = file_get_contents($_FILES['profile_picture']['tmp_name']);
    }

    $email_to_save = $user['email'];
    $password_to_save = $user['password'];

    if (!empty($new_email)) {
        if ($new_email === $user['email']) {
            $errors[] = "New email must be different from current email.";
        } elseif ($new_email !== $confirm_email) {
            $errors[] = "New email and confirm email do not match.";
        } else {
            $email_to_save = $new_email;
        }
    }

    if (!empty($new_password)) {
        if (!password_verify($current_password, $user['password'])) {
            $errors[] = "Current password is incorrect.";
        } elseif (password_verify($new_password, $user['password'])) {
            $errors[] = "New password must be different from current password.";
        } elseif ($new_password !== $confirm_password) {
            $errors[] = "New password and confirm password do not match.";
        } else {
            $password_to_save = password_hash($new_password, PASSWORD_DEFAULT);
        }
    }

    if (empty($errors)) {
        $update_sql = "UPDATE users SET 
            firstname = ?, lastname = ?, username = ?, phone_number = ?, birthdate = ?, 
            sex = ?, age = ?, email = ?, password = ?, profile_picture = ? 
            WHERE id = ?";

        $stmt = $conn->prepare($update_sql);

        // Use dummy value for the blob temporarily
        $null = NULL;

        $stmt->bind_param("ssssssissbi", 
            $firstname, $lastname, $username, $phone_number, $birthdate, 
            $sex, $age, $email_to_save, $password_to_save, $null, $user_id);

        if (!empty($profile_picture)) {
            $stmt->send_long_data(9, $profile_picture); // index 9 for `profile_picture` in bind_param
        }
        if ($stmt->execute()) {
            $_SESSION['profile_message'] = '✅ Profile updated successfully.';
        } else {
            $_SESSION['profile_message'] = '❌ Failed to update profile.';
        }
    } else {
        $_SESSION['profile_message'] = '❌ ' . $errors[0];
    }

    header("Location: profile_form.php");
    exit;
}
?>