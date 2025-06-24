<?php
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
      <img src="Images/logo-light-transparent.png" alt="Logo" />
    </a>

    <div class="admin-menu">
      <div class="dropdown-wrapper">
        <span class="dropdown-toggle" onclick="toggleDropdown()">Admin</span>
        <div class="dropdown" id="adminDropdown">
          <a href="admin_profile.php">View Profile</a>
          <a href="logout.php">Logout</a>
        </div>
      </div>
    </div>
  </div>
</header>

<main>
  <div class="top-bar">
    <h1>Manage Condos</h1>
    <a href="add_condo.php" class="add-btn">Add Condo</a>
  </div>

  <div class="main-flex-container">
    <div class="condo-list">
      <?php if ($condo_result && $condo_result->num_rows > 0): ?>
        <?php while ($condo = $condo_result->fetch_assoc()): ?>
          <div class="condo-card" onclick="loadCondoDetails(<?= $condo['id'] ?>)">
            <h3><?= htmlspecialchars($condo['name']) ?></h3>
            <div class="thumbnail-container">
              <?php
              $image_sql = "SELECT image_path FROM condo_images WHERE condo_id = ?";
              $img_stmt = $conn->prepare($image_sql);
              $img_stmt->bind_param("i", $condo['id']);
              $img_stmt->execute();
              $img_result = $img_stmt->get_result();
              while ($img = $img_result->fetch_assoc()):
              ?>
                <img src="Images/<?= htmlspecialchars($img['image_path']) ?>" class="thumbnail" alt="Condo Image" />
              <?php endwhile; $img_stmt->close(); ?>
            </div>
            <div class="card-buttons">
              <a href="edit_condo.php?id=<?= $condo['id'] ?>" class="edit-btn">Edit</a>
              <a href="#" class="delete-btn open-delete-condo-modal" data-condo-id="<?= $condo['id'] ?>">Delete</a>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p>No condos found.</p>
      <?php endif; ?>
    </div>

    <div class="condo-details" id="condoDetails">
      <h2>Select a condo to view details</h2>
    </div>
  </div>

  <div class="admin-bookings-section">
    <h2>Booking Cancellation Requests</h2>
    <div class="admin-bookings-list">
      <?php
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

  <div class="admin-bookings-section">
    <h2>Deletable Bookings (Cancelled or Completed)</h2>
    <div class="admin-bookings-list">
      <?php
      $now = date('Y-m-d H:i:s');
      $deletableSql = "SELECT b.*, u.username, u.email, u.firstname, u.lastname, u.phone_number, c.name AS condo_name FROM bookings b JOIN users u ON b.user_id = u.id JOIN condos c ON b.condo_id = c.id WHERE (b.status = 'cancelled' OR b.checkout < '$now') ORDER BY b.checkin DESC";
      $deletableResult = $conn->query($deletableSql);
      if ($deletableResult && $deletableResult->num_rows > 0):
        while ($row = $deletableResult->fetch_assoc()):
      ?>
        <div class="admin-booking-card">
          <h4><?= htmlspecialchars($row['condo_name']) ?> (Booking #<?= $row['id'] ?>)</h4>
          <p><strong>User:</strong> <?= htmlspecialchars($row['firstname'] . ' ' . $row['lastname']) ?> (<?= htmlspecialchars($row['username']) ?>)</p>
          <p><strong>Email:</strong> <?= htmlspecialchars($row['email']) ?></p>
          <p><strong>Phone:</strong> <?= htmlspecialchars($row['phone_number']) ?></p>
          <p><strong>Check-in:</strong> <?= date('M d, Y - h:i A', strtotime($row['checkin'])) ?></p>
          <p><strong>Check-out:</strong> <?= date('M d, Y - h:i A', strtotime($row['checkout'])) ?></p>
          <p><strong>Status:</strong> <?= htmlspecialchars($row['status']) ?></p>
          <form method="POST" action="delete_from_db.php" class="admin-delete-form" style="display:inline-block;">
            <input type="hidden" name="booking_id" value="<?= $row['id'] ?>">
            <button type="button" class="delete-btn admin-delete-modal-btn">Delete</button>
          </form>
        </div>
      <?php endwhile; else: ?>
        <p>No deletable bookings found.</p>
      <?php endif; ?>
    </div>
  </div>

  <!-- Admin Delete Modal -->
  <div id="adminDeleteModal">
    <div class="modal-box">
      <p>Are you sure you want to permanently delete this booking?</p>
      <div class="modal-btns">
        <form id="adminDeleteForm" method="POST" action="delete_from_db.php" style="display:inline;">
          <input type="hidden" name="booking_id" id="adminDeleteBookingId" value="">
          <button type="submit" class="confirm">Delete</button>
        </form>
        <button type="button" class="cancel" id="adminDeleteCancelBtn">Cancel</button>
      </div>
    </div>
  </div>

  <!-- Delete Condo Modal -->
  <div id="deleteCondoModal" class="modal-overlay">
    <div class="modal-box">
      <p>Are you sure you want to permanently delete this condo?</p>
      <div style="margin-top: 20px; display: flex; gap: 16px; justify-content: center;">
        <form id="deleteCondoForm" method="GET" action="delete_condo.php" style="display:inline;">
          <input type="hidden" name="id" id="deleteCondoId" value="">
          <button type="submit" class="confirm-btn">Delete</button>
        </form>
        <button type="button" class="cancel-btn" id="cancelDeleteCondoBtn">Cancel</button>
      </div>
      <span class="close-btn" id="closeDeleteCondoModal">&times;</span>
    </div>
  </div>
</main>

<script>
  function loadCondoDetails(condoId) {
    fetch(`get_condo_details.php?id=${condoId}`)
      .then(response => response.text())
      .then(html => {
        document.getElementById('condoDetails').innerHTML = html;
      });
  }

  function toggleDropdown() {
    document.getElementById("adminDropdown").classList.toggle("show");
  }

  window.onclick = function(event) {
    if (!event.target.matches('.dropdown-toggle')) {
      const dropdowns = document.getElementsByClassName("dropdown");
      for (let i = 0; i < dropdowns.length; i++) {
        dropdowns[i].classList.remove("show");
      }
    }
  }

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