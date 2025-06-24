<?php
include 'config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $city = $_POST['city'];
    $address = $_POST['address'];
    $description = $_POST['description'];

    $modalMessage = '';
    $modalType = 'error';
    $showModal = false;

    if (isset($_FILES['images']) && count($_FILES['images']['name']) > 0) {
        $imageFiles = $_FILES['images'];
        $targetDir = "Images/";

        $coverImageIndex = -1;
        for ($i = 0; $i < count($imageFiles['name']); $i++) {
            if ($imageFiles['error'][$i] == 0) {
                $coverImageIndex = $i;
                break;
            }
        }

        if ($coverImageIndex === -1) {
            $modalMessage = 'Please upload at least one valid image.';
            $modalType = 'error';
            $showModal = true;
        } else {
            $coverImage = basename($imageFiles['name'][$coverImageIndex]);
            $coverImagePath = $targetDir . $coverImage;

            if (move_uploaded_file($imageFiles['tmp_name'][$coverImageIndex], $coverImagePath)) {
                $sql = "INSERT INTO condos (name, image_path, description, city, address_details) VALUES (?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($conn, $sql);

                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "sssss", $name, $coverImage, $description, $city, $address);
                    if (mysqli_stmt_execute($stmt)) {
                        $condo_id = mysqli_insert_id($conn); 

                        for ($i = 0; $i < count($imageFiles['name']); $i++) {
                            if ($imageFiles['error'][$i] != 0) {
                                continue; 
                            }

                            $imageName = basename($imageFiles['name'][$i]);
                            $tmpName = $imageFiles['tmp_name'][$i];
                            $imagePath = $targetDir . $imageName;

                            if ($i != $coverImageIndex) {
                                if (!move_uploaded_file($tmpName, $imagePath)) {
                                    continue;
                                }
                            }

                            $insertImageSQL = "INSERT INTO condo_images (condo_id, image_path) VALUES (?, ?)";
                            $imgStmt = mysqli_prepare($conn, $insertImageSQL);
                            mysqli_stmt_bind_param($imgStmt, "is", $condo_id, $imageName);
                            mysqli_stmt_execute($imgStmt);
                            mysqli_stmt_close($imgStmt);
                        }

                        $modalMessage = 'Condo and images added successfully!';
                        $modalType = 'success';
                        $showModal = true;
                        // Redirect after short delay using JS
                        echo "<script>setTimeout(function(){ window.location.href='admin_page.php'; }, 1500);</script>";
                    } else {
                        $modalMessage = 'Failed to add condo.';
                        $modalType = 'error';
                        $showModal = true;
                    }
                    mysqli_stmt_close($stmt);
                } else {
                    $modalMessage = 'Failed to prepare statement.';
                    $modalType = 'error';
                    $showModal = true;
                }
            } else {
                $modalMessage = 'Failed to upload cover image.';
                $modalType = 'error';
                $showModal = true;
            }
        }
    } else {
        $modalMessage = 'Please upload at least one image.';
        $modalType = 'error';
        $showModal = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Add Condo</title>
  <link rel="stylesheet" href="CSS/add_condo.css" />
  <link rel="stylesheet" href="CSS/modal_global.css" />
</head>
<body>

<header>
  <a href="Main Page.php" class="logo">
    <img src="Images/logo-light-transparent.png" alt="Site Logo">
  </a>
</header>

<main>
  <h1>Add New Condo</h1>

  <form id="addCondoForm" method="post" enctype="multipart/form-data">
    <div class="form-group">
      <label for="name">Condo Name</label>
      <input type="text" id="name" name="name" placeholder="Enter condo name" required>
    </div>

    <div class="form-group">
      <label for="city">City</label>
      <select id="city" name="city" required>
        <option value="">-- Select City --</option>
        <option value="Tagaytay">Tagaytay</option>
        <option value="Cavite City">Cavite City</option>
        <option value="Dasmariñas">Dasmariñas</option>
        <option value="Bacoor">Bacoor</option>
        <option value="Imus">Imus</option>
        <option value="Alfonso">Alfonso</option>
        <option value="General Trias">General Trias</option>
        <option value="Silang">Silang</option>
        <option value="Tanza">Tanza</option>
        <option value="Trece Martires">Trece Martires</option>
      </select>
    </div>

    <div class="form-group">
      <label for="address">Address Details</label>
      <input type="text" id="address" name="address" placeholder="Street, Barangay, etc." required>
    </div>

    <div class="form-group">
      <label for="description">Description</label>
      <textarea id="description" name="description" placeholder="Enter condo description" rows="4" required></textarea>
    </div>

    <div class="form-group">
      <label for="images">Upload Images</label>
      <input type="file" id="images" name="images[]" accept="image/*" multiple required>
    </div>

    <button type="submit">Add Condo</button>
    <a href="admin_page.php" class="back-link">← Back to Admin Page</a>
  </form>

  <!-- Modal for messages -->
  <div id="modalOverlay" class="modal-overlay<?php if (!empty($showModal)) echo ' show'; ?>">
    <div class="modal-box <?php echo isset($modalType) ? $modalType : ''; ?>">
      <button class="close-btn" onclick="closeModal()">&times;</button>
      <span id="modalMessage"><?php echo isset($modalMessage) ? htmlspecialchars($modalMessage) : ''; ?></span>
    </div>
  </div>

  <script>
  function closeModal() {
    document.getElementById('modalOverlay').classList.remove('show');
  }
  // Auto-show modal if PHP sets it
  <?php if (!empty($showModal)): ?>
    document.addEventListener('DOMContentLoaded', function() {
      document.getElementById('modalOverlay').classList.add('show');
    });
  <?php endif; ?>
  </script>

</main>

</body>
</html>