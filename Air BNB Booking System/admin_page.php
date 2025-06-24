<?php
//FOR ADMIN PAGE
include 'config.php';

$condo_sql = "SELECT * FROM condos";
$condo_result = $conn->query($condo_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Condo Management</title>
  <link rel="stylesheet" href="CSS/admin_page.css" />
  <link rel="stylesheet" href="CSS/admin_booking_cancel.css" />
</head>
<body>

<header>
  <div class="header-inner">
    <a href="Main Page.php" class="logo">
    </a>

    <div class="admin-menu">
    </div>
  </div>
</header>

<main>
  <div class="main-flex-container">
    <div class="condo-list">
    </div>

    <div class="condo-details" id="condoDetails">
      <h2>Select a condo to view details</h2>
    </div>
  </div>

  <div class="admin-bookings-section">
    <h2>Booking Cancellation Requests</h2>
    <div class="admin-bookings-list">
      <?php
      //FOR BOOKING CANCELLATION REQUESTS
      $pendingSql = "SELECT b.*, u.username, u.email, u.firstname, u.lastname, u.phone_number, c.name AS condo_name FROM bookings b JOIN users u ON b.user_id = u.id JOIN condos c ON b.condo_id = c.id WHERE b.status = 'pending_cancel' ORDER BY b.checkin DESC";
      $pendingResult = $conn->query($pendingSql);
      if ($pendingResult && $pendingResult->num_rows > 0):
        while ($row = $pendingResult->fetch_assoc()):
      ?>
        <div class="admin-booking-card">
          <h4><?= htmlspecialchars($row['condo_name']) ?> (Booking #<?= $row['id'] ?>)</h4>
          <p><strong>User:</strong> <?= htmlspecialchars($row['firstname'] . ' ' . $row['lastname']) ?> (<?= htmlspecialchars($row['username']) ?>)</p>
          <p><strong>Email:</strong> <?= htmlspecialchars($row['email']) ?></p>
          <p><strong>Phone:</strong> <?= htmlspecialchars($row['phone_number']) ?></p>
          <p><strong>Check-in:</strong> <?= date('M d, Y - h:i A', strtotime($row['checkin'])) ?></p>
          <p><strong>Check-out:</strong> <?= date('M d, Y - h:i A', strtotime($row['checkout'])) ?></p>
          <p><strong>Reason:</strong> <?= htmlspecialchars($row['cancellation_reason']) ?></p>
          <form method="POST" action="update_cancel_status.php" class="admin-cancel-action-form" style="display:inline-block;">
            <input type="hidden" name="booking_id" value="<?= $row['id'] ?>">
            <button type="submit" name="action" value="approve" class="approve-btn">Approve Cancel</button>
            <button type="submit" name="action" value="reject" class="reject-btn">Reject Cancel</button>
          </form>
          <?php if ($row['status'] === 'cancelled'): ?>
          <form method="POST" action="delete_from_db.php" class="admin-delete-form" style="display:inline-block; margin-left:10px;">
            <input type="hidden" name="booking_id" value="<?= $row['id'] ?>">
            <button type="button" class="delete-btn admin-delete-modal-btn" style="background-color:#dc3545;">Delete</button>
          </form>
          <?php endif; ?>
        </div>
      <?php endwhile; else: ?>
        <p>No pending cancellation requests.</p>
      <?php endif; ?>
    </div>
  </div>
</main>

<script>
//FOR ADMIN PAGE SCRIPTS
function loadCondoDetails(condoId) {
  //FOR LOAD CONDO DETAILS
  fetch(`get_condo_details.php?id=${condoId}`)
    .then(response => response.text())
    .then(html => {
      document.getElementById('condoDetails').innerHTML = html;
    });
}

function toggleDropdown() {
  //FOR ADMIN DROPDOWN TOGGLE
  document.getElementById("adminDropdown").classList.toggle("show");
}

window.onclick = function(event) {
  //FOR DROPDOWN CLOSE OUTSIDE
  if (!event.target.matches('.dropdown-toggle')) {
    const dropdowns = document.getElementsByClassName("dropdown");
    for (let i = 0; i < dropdowns.length; i++) {
      dropdowns[i].classList.remove("show");
    }
  }
}

//FOR ADMIN DELETE MODAL BUTTONS
  // Admin delete modal script
  document.querySelectorAll('.admin-delete-modal-btn').forEach(button => {
    button.addEventListener('click', function() {
      const bookingId = this.closest('.admin-booking-card').querySelector('input[name="booking_id"]').value;
      document.getElementById('adminDeleteBookingId').value = bookingId;
      document.getElementById('adminDeleteModal').style.display = 'flex';
    });
  });

  document.getElementById('adminDeleteCancelBtn').addEventListener('click', function() {
    document.getElementById('adminDeleteModal').style.display = 'none';
  });

  window.addEventListener('click', function(event) {
    if (event.target == document.getElementById('adminDeleteModal')) {
      document.getElementById('adminDeleteModal').style.display = 'none';
    }
  });

  document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.open-delete-condo-modal').forEach(function(btn) {
      btn.addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('deleteCondoId').value = this.getAttribute('data-condo-id');
        document.getElementById('deleteCondoModal').classList.add('show');
      });
    });
    document.getElementById('cancelDeleteCondoBtn').onclick = function() {
      document.getElementById('deleteCondoModal').classList.remove('show');
    };
    document.getElementById('closeDeleteCondoModal').onclick = function() {
      document.getElementById('deleteCondoModal').classList.remove('show');
    };
    document.getElementById('deleteCondoModal').onclick = function(e) {
      if (e.target === this) {
        this.classList.remove('show');
      }
    };
  });
</script>

</body>
</html>