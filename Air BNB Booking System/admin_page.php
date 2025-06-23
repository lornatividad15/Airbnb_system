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
              <a href="delete_condo.php?id=<?= $condo['id'] ?>" class="delete-btn" onclick="return confirm('Are you sure?')">Delete</a>
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
</script>

</body>
</html>