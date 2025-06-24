<?php
include 'config.php';
session_start();

if (!isset($_GET['id'])) {
    $modalMessage = 'No condo selected';
    $modalType = 'error';
    $showModal = true;
    $modalRedirect = true;
    echo "<script>setTimeout(function(){ window.location.href='admin_page.php'; }, 1500);</script>";
    include __DIR__ . '/modal_snippet.php';
    exit;
}

$condo_id = intval($_GET['id']);
$sql = "SELECT * FROM condos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $condo_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $modalMessage = 'Condo not found';
    $modalType = 'error';
    $showModal = true;
    $modalRedirect = true;
    echo "<script>setTimeout(function(){ window.location.href='admin_page.php'; }, 1500);</script>";
    include __DIR__ . '/modal_snippet.php';
    exit;
}
$condo = $result->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    $name = $_POST['name'];
    $city = $_POST['city'];
    $address = $_POST['address'];
    $description = $_POST['description'];

    $update_sql = "UPDATE condos SET name = ?, city = ?, address_details = ?, description = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ssssi", $name, $city, $address, $description, $condo_id);
    $stmt->execute();
    $stmt->close();

    if (!empty($_FILES['images']['name'][0])) {
        $targetDir = "Images/";
        foreach ($_FILES['images']['name'] as $i => $fileName) {
            if ($_FILES['images']['error'][$i] === 0) {
                $imageName = basename($fileName);
                $targetPath = $targetDir . $imageName;
                if (move_uploaded_file($_FILES['images']['tmp_name'][$i], $targetPath)) {
                    $img_sql = "INSERT INTO condo_images (condo_id, image_path) VALUES (?, ?)";
                    $img_stmt = $conn->prepare($img_sql);
                    $img_stmt->bind_param("is", $condo_id, $imageName);
                    $img_stmt->execute();
                    $img_stmt->close();
                }
            }
        }
    }

    $modalMessage = 'Condo updated successfully!';
    $modalType = 'success';
    $showModal = true;
    $modalRedirect = true;
    echo "<script>setTimeout(function(){ window.location.href='admin_page.php'; }, 1500);</script>";
    include __DIR__ . '/modal_snippet.php';
    exit;
}

$img_sql = "SELECT * FROM condo_images WHERE condo_id = ?";
$img_stmt = $conn->prepare($img_sql);
$img_stmt->bind_param("i", $condo_id);
$img_stmt->execute();
$img_result = $img_stmt->get_result();
$images = $img_result->fetch_all(MYSQLI_ASSOC);
$img_stmt->close();

// Show modal if image was deleted
if (isset($_GET['img_deleted']) && $_GET['img_deleted'] == 1) {
    $modalMessage = 'Image deleted successfully!';
    $modalType = 'success';
    $showModal = true;
    $modalRedirect = false;
    include __DIR__ . '/modal_snippet.php';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Condo</title>
  <link rel="stylesheet" href="CSS/edit_condo.css">
</head>
<body>

<header>
  <a href="Main Page.php" class="logo">
    <img src="Images/logo-light-transparent.png" alt="Logo">
  </a>
</header>

<main>
  <h1>Edit Condo</h1>

  <form method="POST" enctype="multipart/form-data">
    <div class="form-group">
      <label for="name">Condo Name</label>
      <input type="text" id="name" name="name" value="<?= htmlspecialchars($condo['name']) ?>" required>
    </div>

    <div class="form-group">
      <label for="city">City</label>
      <select id="city" name="city" required>
        <option value="">-- Select City --</option>
        <?php
        $cities = ["Tagaytay", "Cavite City", "Dasmariñas", "Bacoor", "Imus", "Alfonso", "General Trias", "Silang", "Tanza", "Trece Martires"];
        foreach ($cities as $city) {
          $selected = $condo['city'] == $city ? 'selected' : '';
          echo "<option value='$city' $selected>$city</option>";
        }
        ?>
      </select>
    </div>

    <div class="form-group">
      <label for="address">Address Details</label>
      <input type="text" id="address" name="address" value="<?= htmlspecialchars($condo['address_details']) ?>" required>
    </div>

    <div class="form-group">
      <label for="description">Description</label>
      <textarea id="description" name="description" rows="4" required><?= htmlspecialchars($condo['description']) ?></textarea>
    </div>

    <div class="form-group">
      <label>Existing Images</label>
      <div class="image-preview">
        <?php foreach ($images as $img): ?>
          <div class="image-wrapper">
            <img src="Images/<?= htmlspecialchars($img['image_path']) ?>" alt="Condo Image">
            <a href="#" 
               class="delete-btn-link open-delete-image-modal" 
               data-image-id="<?= $img['id'] ?>" 
               data-condo-id="<?= $condo_id ?>">×</a>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="form-group">
      <label for="images">Add New Images</label>
      <input type="file" id="images" name="images[]" accept="image/*" multiple>
    </div>

    <button type="submit" class="update-btn">Update Condo</button>
    <div class="form-group">
      <a href="admin_page.php" class="back-link">← Back to Admin Page</a>
    </div>
  </form>
</main>

<!-- Delete Image Modal -->
<div id="deleteImageModal" class="modal-overlay">
  <div class="modal-box">
    <p>Delete this image?</p>
    <div style="margin-top: 20px; display: flex; gap: 16px; justify-content: center;">
      <form id="deleteImageForm" method="GET" action="delete_image.php" style="display:inline;">
        <input type="hidden" name="image_id" id="deleteImageId" value="">
        <input type="hidden" name="condo_id" id="deleteCondoId" value="">
        <button type="submit" class="confirm-btn">Delete</button>
      </form>
      <button type="button" class="cancel-btn" id="cancelDeleteImageBtn">Cancel</button>
    </div>
    <span class="close-btn" id="closeDeleteImageModal">&times;</span>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.open-delete-image-modal').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      document.getElementById('deleteImageId').value = this.getAttribute('data-image-id');
      document.getElementById('deleteCondoId').value = this.getAttribute('data-condo-id');
      document.getElementById('deleteImageModal').classList.add('show');
    });
  });
  document.getElementById('cancelDeleteImageBtn').onclick = function() {
    document.getElementById('deleteImageModal').classList.remove('show');
  };
  document.getElementById('closeDeleteImageModal').onclick = function() {
    document.getElementById('deleteImageModal').classList.remove('show');
  };
  document.getElementById('deleteImageModal').onclick = function(e) {
    if (e.target === this) {
      this.classList.remove('show');
    }
  };
});
</script>

</body>
</html>