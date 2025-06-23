<?php
include 'config.php';
session_start();

$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = isset($_SESSION['admin_id']);
$userId = $_SESSION['user_id'] ?? null;

if (!$isLoggedIn) {
    header("Location: Login form.php");
    exit;
}

if (!isset($_GET['id'])) {
    die("Condo ID is required.");
}

$condoId = intval($_GET['id']);
$sql = "SELECT * FROM condos WHERE id = $condoId";
$result = $conn->query($sql);

if (!$result || $result->num_rows === 0) {
    die("Condo not found.");
}

$condo = $result->fetch_assoc();
$condoName = htmlspecialchars($condo['name']);
$description = htmlspecialchars($condo['description']);

$imgQuery = "SELECT image_path FROM condo_images WHERE condo_id = $condoId LIMIT 1";
$imgResult = $conn->query($imgQuery);
$imgPath = ($imgResult && $imgResult->num_rows > 0)
    ? 'Images/' . htmlspecialchars($imgResult->fetch_assoc()['image_path'])
    : 'Images/logo-1-primary.png';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($isAdmin) {
        $error = "Admins cannot book condos.";
    } else {
        $guest_count = intval($_POST['guest_count']);
        $checkin = $_POST['checkin'];
        $checkout = $_POST['checkout'];

        $stmt = $conn->prepare("INSERT INTO bookings (condo_id, user_id, guest_count, checkin, checkout) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiss", $condoId, $userId, $guest_count, $checkin, $checkout);

        if ($stmt->execute()) {
            header("Location: booking.php");
            exit;
        } else {
            $error = "Failed to book condo. Please try again.";
        }
    }
}

$bookedDates = [];
$res = $conn->query("SELECT checkin, checkout FROM bookings");
if ($res && $res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $bookedDates[] = [
            'start' => $row['checkin'],
            'end' => $row['checkout']
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="stylesheet" href="CSS/book_condo.css"/>
    <title>Book Condo - <?= $condoName ?></title>
</head>
<body>

<header>
    <div class="header-inner">
        <a href="Main Page.php" class="logo">
            <img src="Images/logo-light-transparent.png" alt="Site Logo" />
        </a>

        <div class="center-nav" id="centerNav">
            <a href="find_condo.php">Find a Condo</a>
            <a href="#" id="myBookingsLink">My Bookings</a>
        </div>

        <div class="right-nav" id="rightNav">
            <?php if ($isLoggedIn || $isAdmin): ?>
                <div class="user-menu" id="userMenu">
                    <span id="accountName">My Account</span>
                    <div class="dropdown" id="userDropdown">
                        <a href="<?= $isAdmin ? 'admin_profile.php' : 'profile_form.php' ?>">Profile</a>
                        <a href="logout.php" id="logoutBtn">Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="signup form.php" id="signup">Sign Up</a>
                <a href="Login form.php" id="login">Login</a>
            <?php endif; ?>
        </div>

        <button class="btn" onclick="toggleMenu()">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>
</header>

<div class="shadow" id="shadow"></div>

<nav class="mobile-nav" id="mobileNav">
    <div class="mobile-nav-section" id="mobileNavLinks">
        <h4>Navigation</h4>
        <a href="find_condo.php">Find a Condo</a>
        <a href="#" id="mobileMyBookings">My Bookings</a>
    </div>

    <?php if ($isLoggedIn || $isAdmin): ?>
        <div class="mobile-nav-section" id="mobileUserMenu">
            <h4>My Account</h4>
            <a href="<?= $isAdmin ? 'admin_profile.php' : 'profile_form.php' ?>" id="mobileProfile">Profile</a>
            <a href="logout.php" id="mobileLogoutBtn">Logout</a>
        </div>
    <?php else: ?>
        <div class="mobile-nav-section" id="mobileAuthMenu">
            <h4>Account</h4>
            <a href="signup form.php" id="mobileSignup">Sign Up</a>
            <a href="Login form.php" id="mobileLogin">Login</a>
        </div>
    <?php endif; ?>

    <div class="mobile-nav-section">
        <h4>Support</h4>
        <a href="FAQ.php">Need help?</a>
    </div>
</nav>

<section class="booking-form-section">
    <div class="booking-header">
        <h2>Book <?= $condoName ?></h2>
        <div class="underline"></div>
    </div>

    <div class="booking-form-container">
        <div class="booking-condo-preview">
            <img src="<?= $imgPath ?>" alt="<?= $condoName ?>" />
            <p><?= $description ?></p>
        </div>

        <form method="POST" class="booking-form" id="bookingForm">
            <?php if (isset($error)): ?>
                <p class="error"><?= $error ?></p>
            <?php endif; ?>

            <label for="guest_count">Number of Guests:</label>
            <input type="number" name="guest_count" id="guest_count" min="1" required />

            <label for="checkin">Check-in:</label>
            <input type="datetime-local" name="checkin" id="checkin" required />

            <label for="checkout">Check-out:</label>
            <input type="datetime-local" name="checkout" id="checkout" required />

            <button type="submit" id="confirmBookingBtn">Confirm Booking</button>
        </form>
    </div>
</section>

<div id="mainModal" class="modal-overlay" style="display:none;">
    <div class="modal-box">
        <span class="close-btn" id="mainModalCloseBtn">&times;</span>
        <p id="mainModalMessage">This is a modal message.</p>
    </div>
</div>

<script>
    window.isLoggedIn = <?= json_encode($isLoggedIn); ?>;
    window.isAdmin = <?= json_encode($isAdmin); ?>;
    const bookedRanges = <?= json_encode($bookedDates); ?>;
</script>
<script src="JS/book_condo.js"></script>

</body>
</html>
